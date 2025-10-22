<?php

/**
 * Generic autoloader for classes
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName
 */

class Pay_Autoload
{
    /**
     * Register the autoloader
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'spl_autoload_register'));
    }

    /**
     * @param string $class_name
     * @return void
     */
    public static function spl_autoload_register($class_name)
    {
        $dir = realpath(dirname(__FILE__));
        $class_path = $dir . '/../' . str_replace('_', '/', $class_name) . '.php';

        if (file_exists($class_path)) {
            require_once $class_path;
        }
    }
}

require_once __DIR__ . '/vendor/autoload.php';
//Register the autoloader
Pay_Autoload::register();
