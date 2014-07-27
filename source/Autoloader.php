<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\CronManager;

// append autoloader
spl_autoload_register(array('\alxmsl\CronManager\Autoloader', 'autoload'));

/**
 * CronManager classes autoloader
 * @author alxmsl
 * @date 1/13/13
 */
final class Autoloader {
    /**
     * @var array array of available classes
     */
    private static $classes = array(
        'alxmsl\\CronManager\\Autoloader'      => 'Autoloader.php',
        'alxmsl\\CronManager\\CronManager'     => 'CronManager.php',
        'alxmsl\\CronManager\\Crontab'         => 'Crontab.php',
        'alxmsl\\CronManager\\CrontabCommand'  => 'CrontabCommand.php',
        'alxmsl\\CronManager\\LockerInterface' => 'LockerInterface.php',
    );

    /**
     * Component autoloader
     * @param string $className claass name
     */
    public static function autoload($className) {
        if (array_key_exists($className, self::$classes)) {
            $fileName = realpath(dirname(__FILE__)) . '/' . self::$classes[$className];
            if (file_exists($fileName)) {
                include $fileName;
            }
        }
    }
}
