<?php
/**
 * Created by PhpStorm.
 * User: bhoover
 * Date: 7/12/2014
 * Time: 10:46 AM
 */
set_include_path("../");
require_once("lib.php");

class ManagerTest extends PHPUnit_Framework_TestCase
{

    /** @var  Manager */
    private $target;
    /** @var  Menu */
    private $menu;

    private $testDataFileName = "testData1.csv";
    private $testDataFileNameWithInvalidLine = "testDataWithInvalidLine.csv";

    /**
     * Most basic case, pass in one item that exists and get the price
     */
    public function testGetPriceOfItems()
    {
        $this->assertEquals(10.01, $this->target->getPriceForRequestedItems(["boston"], $this->menu));
    }

    /**
     * the item is not on the menu, should get a null back
     */
    public function testGetPriceOfItems_item_not_on_menu()
    {
        $this->assertEquals(Manager::$NOTFULFILLABLERESULT, $this->target->getPriceForRequestedItems(["sir_not_appearing_in_this_movie"], $this->menu));
    }

    /**
     * pass in multiple items that all exist
     */
    public function testGetPriceOfItems_multiple_items()
    {
        $this->assertEquals(30.02, $this->target->getPriceForRequestedItems(["boston", "romaine"], $this->menu));
    }

    /**
     * pass in multiple items, one of which doesn't exist
     */
    public function testGetPriceOfItems_multiple_items_one_doesnt_exist()
    {
        $this->assertEquals(Manager::$NOTFULFILLABLERESULT, $this->target->getPriceForRequestedItems(["boston", "sir_not_appearing_in_this_movie"], $this->menu));
    }

    /**
     * tests getting the price when a meal can be fully fulfilled from a package deal
     */
    public function testGetPriceOfItems_multiple_items_from_package()
    {
        $this->assertEquals(15.01, $this->target->getPriceForRequestedItems(["boston", "red_leaf"], $this->menu));
    }

    /**
     * tests getting the price when a meal can be partially filled from a package and a ala cart item
     */
    public function testGetPriceOfItems_multiple_items_from_package_and_item()
    {
        $this->assertEquals(35.02, $this->target->getPriceForRequestedItems(["boston", "red_leaf", "romaine"], $this->menu));
    }

    /**
     * in this case, the package is a better price than the ala carte method
     */
    public function testGetPriceOfItems_package_price_better_than_ala_carte()
    {
        $this->menu->addComboDeal(11.01, ["boston", "romaine"]);
        $this->assertEquals(11.01, $this->target->getPriceForRequestedItems(["boston", "romaine"], $this->menu));
    }


    /**
     * The data passed in was not an array
     */
    public function testValidateLine_validLine_not_array()
    {
        $this->assertFalse($this->target->validateLine("bad Data"));
    }

    /**
     * In this case, the line is valid, with one item
     */
    public function testValidateLine_validLine_one_item()
    {
        $this->assertTrue($this->target->validateLine([1, 10.00, "boston"]));
    }

    /**
     * In this case, the line is valid, with two item
     */
    public function testValidateLine_validLine_two_item()
    {
        $this->assertTrue($this->target->validateLine([1, 10.00, "boston", "red_leaf"]));
    }

    /**
     * In this case, there aren't enough items on the line
     */
    public function testValidateLine_not_enough_items()
    {
        $this->assertFalse($this->target->validateLine([""]));
    }

    /**
     * In this case, the first item isn't an integer
     */
    public function testValidateLine_first_value_not_valid()
    {
        $this->assertFalse($this->target->validateLine(["a", 10.00, "boston"]));
    }

    /**
     * In this case, the second item isn't a float
     */
    public function testValidateLine_second_item_not_float()
    {
        $this->assertFalse($this->target->validateLine([1, "as", "boston"]));
    }

    /**
     * In this case, the 3rd item isn't well formed.  It has a capital Letter
     */
    public function testValidateLine_one_item_not_valid()
    {
        $this->assertFalse($this->target->validateLine([1, 10.00, "Boston"]));
    }

    /**
     * In this case, the 4th item isn't well formed, since it has a space
     */
    public function testValidateLine_two_item_not_valid()
    {
        $this->assertFalse($this->target->validateLine([1, 10.00, "boston", "red leaf"]));
    }

    /**
     * In this case, the 5th item isn't well formed, since it has a number
     */
    public function testValidateLine_three_item_not_valid()
    {
        $this->assertFalse($this->target->validateLine([1, 10.00, "boston", "red_leaf", "red_leaf_123"]));
    }

    /**
     * this is use case number one from the specifications.
     * The data file has entries for burger and tofu_log, and the price of those items should be 11.5 from
     * restaurant 2.
     */
    public function testRunManager_use_case_1()
    {
        $this->assertEquals("2, 11.5", $this->target->runManager($this->testDataFileName, ["burger", "tofu_log"]));
    }

    /**
     * this is use case number two from the specifications.
     * The data file does not have a combination that can fulfill the order.
     */
    public function testRunManager_use_case_2()
    {
        $this->assertEquals(Manager::$NOTFULFILLABLERESULT,
            $this->target->runManager($this->testDataFileName, ["chef_salad", "wine_spritzer"]));
    }

    /**
     * this is use case number two from the specifications.
     * The data file is using a combo and ala-carte item to fulfill the order
     */
    public function testRunManager_use_case_3()
    {
        $this->assertEquals("6, 11",
            $this->target->runManager($this->testDataFileName, ["fancy_european_water", "extreme_fajita"]));
    }

    /**
     * In this case, there is a line in the test data that isn't valid.  The test should be able to continue
     *
     */
    public function testRunManager_test_data_file_has_invalid_data()
    {
        $this->assertEquals("6, 11",
            $this->target->runManager($this->testDataFileNameWithInvalidLine, ["fancy_european_water", "extreme_fajita"]));
    }

    /** @before */
    protected function createObjects()
    {
        $this->target = new Manager();
        $this->menu = new Menu();
    }

    /** @before */
    protected function provideStartingItems()
    {
        $this->menu->addItem(10.01, "boston");
        $this->menu->addItem(20.01, "romaine");
    }

    /** @before */
    protected function provideStartingPackages()
    {
        $this->menu->addComboDeal(15.01, ["boston", "red_leaf"]);
    }

    /** @before */
    protected function setupFileName()
    {
        $this->testDataFileName =  __DIR__ . "/" . $this->testDataFileName;
        $this->testDataFileNameWithInvalidLine =  __DIR__ . "/" . $this->testDataFileNameWithInvalidLine;
    }

}
 