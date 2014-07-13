<?php

/**
 * Created by PhpStorm.
 * User: Brian Hoover
 * Date: 7/12/2014
 * Time: 9:30 AM
 */
class Test extends PHPUnit_Framework_TestCase
{

    public function testCodingProject()
    {
        include '..\codingProject.php';
        $this->expectOutputString("1 30<br />\n");
    }
}
 