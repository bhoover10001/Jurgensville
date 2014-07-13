<?php
/**
 * Created by PhpStorm.
 * User: Brian Hoover
 * Date: 7/11/2014
 * Time: 3:58 PM
 */
set_include_path("../");
require_once("lib.php");

class RestaurantTest extends PHPUnit_Framework_TestCase
{
    /** @var  Menu */
    private $target;

    /**
     * Tests adding an item menu
     */
    public function testAddItem()
    {
        $expectedCount = count($this->target->getItems()) + 1; // The initializer is adding items already, so get the expected count
        $this->target->addItem(10.00, "hamburger");
        $this->assertEquals($expectedCount, count($this->target->getItems()));
        $this->assertEquals(10.00, $this->target->getItems()["hamburger"]);
    }

    /**
     * Tests creating a new mealCombo
     */
    public function testSetPackage()
    {
        $expectedCount = count($this->target->getComboDeals()) + 1; // Since the packages might be initialized in advance
        $this->target->addComboDeal(10.02, ["hamburger", "coca_cola", "garlic_fries"]);
        $this->assertEquals($expectedCount, count($this->target->getComboDeals()));
        $comboDeals = $this->target->getComboDeals();
        /** @var $resultComboDeal ComboDeal */
        $resultComboDeal = array_pop($comboDeals); // It's ok to have a destructive test here.  The tested item should be the last one on the list
        $this->assertEquals(10.02, $resultComboDeal->getPrice());
        $expectedItemList = ["hamburger" => "hamburger", "coca_cola" => "coca_cola", "garlic_fries" => "garlic_fries"];
        $this->assertEquals($expectedItemList, $resultComboDeal->getItems());
    }

    /** @before */
    protected function createMenu()
    {
        $this->target = new Menu();
    }

    /** @before */
    protected function provideStartingItems()
    {
        $this->target->addItem(10.01, "boston");
        $this->target->addItem(20.01, "romaine");
    }

    /** @before */
    protected function provideStartingPackages()
    {
        $this->target->addComboDeal(15.01, ["boston", "red_leaf"]);
    }


}
 