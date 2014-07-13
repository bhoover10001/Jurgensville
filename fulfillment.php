<?php
/**
 * Created by PhpStorm.
 * User: bhoover
 * Date: 7/13/2014
 * Time: 7:32 AM
 */

require_once("lib.php");

if (count($argv) < 3) {
    throw new RuntimeException("There should be at least three arguments.  The second argument is the file name");
}
$filename = $argv[1];
$requestedItems = array_slice($argv, 2);

$manager = new Manager();

print($manager->runManager($filename, $requestedItems));
