<?php

namespace Plain\Helpers;

/**
 * @package   Kirby Plain Helpers
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://plain-solutions.net/
 * @copyright Roman Gsponer
 * @license   https://plain-solutions.net/terms/ 
 * 
 * If you're reading this, you're probably up to skip the license validation.
 *  
 * Keep in mind, that i spent a lot of time developing this. 
 * You will also save a lot of time with this extension.
 *  
 */

use Kirby\Cms\App;
use Kirby\Data\Json;
use Kirby\Panel\Field;
use Kirby\Http\Remote;
use Kirby\Toolkit\V;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\I18n;
use Kirby\Exception\Exception;
use Kirby\Toolkit\A;

class License
{

    private const PROXY           = 'https://plain-solutions.net/proxy';

    public string $title;
    public string $link;
    private string $prefix;
    private string $licensefile;
    private array $licensedata = [];

    private static $cache = [];
    public static array $licenses = [];

    public function __construct(
        public string $name, 
        public ?array $info = null,
        private ?bool $isValid = null
    ) {

        if ($info['license'] === 'MIT') {
            return null;
        }

        $this->prefix = Str::after($this->name, '/');
        $this->licensefile = App::instance()->root("config") . "/.{$this->prefix}_license";

        $this->title = $info['extra']['title'] ?? $this->name;
        $this->link = $info['homepage'];
        
        if (file_exists($this->licensefile)) {
            $this->licensedata = Json::read($this->licensefile, 'json', false);
        }

        static::$cache[$name] = $this; 
    }

    public static function factory($name, ?array $info = null): self
    {
        if (array_key_exists($name, static::$cache)) {
            return static::$cache[$name];
        }

        return new self($name, $info);
    }

    public function saveTranslate($key) {
        return App::instance()->translation()->get($key) ?? App::instance()->translation('en')->get($key);
    }

    public function getLicenseObject(): ?array
    {
        if (static::isValid()) {
            return null;
        }
        return [
            'title'     => $this->title,
            'cta'       => $this->saveTranslate('license.activate.label'),
            'dialog'    => $this->prefix . "/register"
        ];
    }

    public function licenseArray(): ?array
    {
        if ($this->isValid()) {
            return null;
        }

        return [
            'value'     => 'missing',
            'theme'     => 'negative',
            'label'     => $this->saveTranslate('license.unregistered.label'),
            'icon'      => 'alert',
            'dialog'    => "{$this->prefix}/register"
        ];
    }

    private function isValid(): bool
    {

        if ($this->isValid !== null) {
            return $this->isValid;
        }

        $license = $this->licensedata;

        if (
            isset($license["key"], $license["email"], $license["signature"]) !== true &&
            count($license) === 0
        ) {
            return $this->isValid = false;
        }

        $licensedata = $this->generateLicensedata($license["key"], $license["email"]);

        if ($license["signature"] !== md5(json_encode($licensedata))) {
            return $this->isValid = false;
        }

        return $this->isValid = true;
    }

    public function extends($extends) {
        if ($this->isValid()) {
            return $extends;
        }

        $prefix = $this->prefix;
        $lang = App::instance()->user()?->language() ?? App::instance()->currentLanguage()?->code() ?? 'en';;

        return A::merge($extends, [
            'api' => [
                'routes' => [
                    [
                        "pattern" => "plain/licenses/validate",
                        "action" => function () {
                            //return License::factory(get('name'))->register(get("key"), get("email"));
                        },
                    ],
                ]
            ],
            'areas' => [
                $prefix  => [
                    'dialogs' => [
                        "$prefix/register" => $this->dialog()
                    ]
                ]
            ],
            'translations' => [
                $lang => [
                    "plain.licenses.$prefix" => $this->getLicenseObject()
                ]
            ],
        ]);
    }

    public function dialog(): array
    {

        $license_obj = $this;

        return [
            'load' => function () use ($license_obj) {

                $system   = App::instance()->system();
                $local    = $system->isLocal();
                $instance = $system->indexUrl();
                $text_key = 'license.activate.' . ($local ? 'local' : 'domain');
                $text = I18n::template($text_key, ['host' => $instance]);
    
                return [
                    'component' => 'k-form-dialog',

                    'props' => [
                        'fields' => [
                            'headline' => [
                                'label' => $license_obj->title,
                                'type'  => 'headline'
                            ],
                            'domain' => [
                                'label' => $license_obj->saveTranslate('license.activate.label'),
                                'type'  => 'info',
                                'theme' => $local ? 'warning' : 'info',
                                'text'  => Str::replace($text, 'Kirby', $license_obj->title)
                            ],
                            'license' => [
                                'label'       => $license_obj->saveTranslate('license.code.label'),
                                'type'        => 'text',
                                'required'    => true,
                                'counter'     => false,
                                'placeholder' => '',
                                'help'        => $license_obj->saveTranslate('license.code.help') . ' ' . '<a href="' . $license_obj->link . '" target="_blank">' . $license_obj->saveTranslate('license.buy') . ' &rarr;</a>'
                            ],
                            'email' => Field::email(['required' => true]),
                            'license_id' => Field::hidden()
                        ],
                        'submitButton' => [
                            'icon'  => 'key',
                            'text'  => $license_obj->saveTranslate('activate'),
                            'theme' => 'love',
                        ],
                        'value' => [
                            'license'   => null,
                            'email'     => null,
                            'name'      => $license_obj->name
                        ]
                    ]
                ];
            },
            'submit' => function () {

                $request = App::instance()->request();

                License::factory($request->get('name'))->register (
                    $request->get('license'),
                    $request->get('email')
                );

                return [
                    'message' => $this->saveTranslate('license.success')
                ];
                
            }
        ];
    }

    public function register(string $key, string $email): void
    {

        if (V::email($email) === false) {
            throw new Exception("error.validation.email");
        }

        $licensedata = $this->generateLicensedata($key, $email);

        try {
            $request = new Remote($this->link, [
                "method" => "POST",
                "data" => $licensedata,
                "timeout" => 5,
            ]);

            $response = $request->json();

        } catch (\Throwable $e) {
            throw new Exception("No connection to the license server. Visit: " . static::PROXY);
        }

        if ($response === null || $response["error"] ?? false === 1) {
            throw new Exception($response["text"] ??= 'An error has occurred!');
        }

        $this->writeLicensedata($licensedata);

    }

    private function generateLicensedata(string $key, string $email): array
    {
        return [
            "product" => Str::ltrim(parse_url($this->link, PHP_URL_PATH), '/'),
            "key" => $key,
            "email" => Str::lower(trim($email)),
            "site" => App::instance()
                ->system()
                ->indexUrl(),
        ];
    }

    private function writeLicensedata(array $licensedata): void
    {
        $licensedata["signature"] = md5(json_encode($licensedata));
        $this->licensedata = $licensedata;
        Json::write($this->licensefile, $licensedata);
    }
}
