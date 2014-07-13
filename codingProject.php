<?php
/**
 * Created by PhpStorm.
 * User: Brian Hoover
 * Date: 7/12/2014
 * Time: 9:21 AM
 */

require_once("lib.php");

$menus = array();
$manager = new Manager();

$menu1 = new Menu();
$menu1->addItem(10.00, "romaine");
$menu1->addItem(20.00, "red_leaf");
$menus["1"] = $menu1;

print("1 " . $manager->getPriceForRequestedItems(["romaine", "red_leaf"], $menu1) . "<br />\n");

