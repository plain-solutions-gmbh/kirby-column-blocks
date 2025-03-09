<?php 
use Kirby\Filesystem\F;

F::loadClasses([
    'Plain\Helpers\Plugin'      => __DIR__ . '/Plugin.php',
    'Plain\Helpers\Autoloader'  => __DIR__ . '/Autoloader.php',
    'Plain\Helpers\License'     => __DIR__ . '/License.php'
]);