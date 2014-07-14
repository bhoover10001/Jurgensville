<?php
/**
 * Created by PhpStorm.
 * User: bhoover
 * Date: 7/13/2014
 * Time: 8:56 AM
 */

set_include_path("../");

class fulfillmentTest extends PHPUnit_Framework_TestCase {

    /**
     * The happy path case.  The test data exists and there is valid data.
     */
    public function testFulfillmentTest() {
        $argv[0] = "fulfillmentTest.php";
        $argv[1] = __DIR__ . "/" . "testData1.csv";
        $argv[2] = "burger";
        $argv[3] = "tofu_log";
        include 'fulfillment.php';
        $this->expectOutputString("2, 11.5");
    }

    /**
     * In this case, there aren't enough items on the list
     */
    public function testFulfillmentTest_not_valid_request_not_enough_items() {
        $argv[0] = "fulfillmentTest.php";
        $argv[1] = __DIR__ . "/" . "testData1.csv";
        $this->setExpectedException("RuntimeException", "There should be at least three arguments.  The second argument is the file name");
        include 'fulfillment.php';
    }

    /**
     * the File is not there
     */
    public function testFulfillmentTest_file_not_found() {
        $argv[0] = "fulfillmentTest.php";
        $argv[1] = __DIR__ . "/" . "file_not_present.csv";
        $argv[2] = "burger";
        $argv[3] = "tofu_log";
        $this->setExpectedException("Exception", "File " . $argv[1] . " was not found");
        include 'fulfillment.php';
    }

}
 