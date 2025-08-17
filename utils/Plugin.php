<?php

namespace Plain\Helpers;

/**
 * @package   Kirby Plain Helpers
 * @author    Roman Gsponer <support@plain-solutions.net>
 * @link      https://plain-solutions.net/
 * @copyright Roman Gsponer
 * @license   https://plain-solutions.net/terms/ 
 */

use Kirby\Cms\App;
use Kirby\Data\Data;

class Plugin
{

    public static function load(
        string $name,
        ?array $extends = null,
        ?array $info = null,
        string|null $root = null,
        string|bool $autoloader = false
    ): void {


        $root ??= dirname(debug_backtrace()[0]['file']);
        $info ??= Data::read($root . '/composer.json');

        //Needs to be loadet before autoload!
        $license_obj = ($info['license'] === 'MIT') ? null : new License($name, $info);

		if ($autoloader) {

			//Allow to apply custom Autoloader
			$autolader_class = is_bool($autoloader) ? Autoloader::class : $autoloader;

			$extends = $autolader_class::load(
				name: $name,
				root: $root,
				data: $extends ?? []
			);
		}

        $params = [
            'name'      => $name,
            'info'      => $info,
            'root'      => $root
        ];
        
        //Kirby > 5.0.0 allows license status
        if (version_compare(App::version() ?? '0.0.0', '4.9.9', '>')) {

            if ($license_obj === null) {
                $status = static::licenseArray($info);
            }

            $params['license'] = [
                'name'      => $info['license'],
                'status'    => $status ?? $license_obj?->licenseArray($info)
            ];
        }
        
        $params['extends'] = $license_obj?->extends($extends) ?? $extends;

        App::plugin(...$params);
    }

    private static function licenseArray($info): ?array
    {
        if ($donate = $info['funding'][0]['url'] ?? null) {
            return  [
                'value'     => 'active',
                'link'      => $donate,
                'theme'     => 'pink',
                'label'     => 'Donate',
                'icon'      => 'heart',
            ];
        }
        return null;
    }

}
