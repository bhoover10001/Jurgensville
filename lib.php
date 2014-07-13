<?php
/**
 * Created by PhpStorm.
 * User: Brian Hoover
 * Date: 7/12/2014
 * Time: 10:09 AM
 */

/** setup the class autoloader.
 * All classes should be in the class folder */

/**
 * sets up the autoloader
 * @param $class
 */
function my_autoloader($class)
{
    // This is necessary to be able to run code coverage.
    if (strpos($class, "PHPUnit") !== false || strpos($class, "ClassLoader")) {
        return;
    }
    include 'classes/' . $class . '.php';
}

spl_autoload_register('my_autoloader');