<?php

/**
 * Created by PhpStorm.
 * User: bhoover
 * Date: 7/12/2014
 * Time: 10:12 AM
 *
 * a Combo Deal is when a set of items are combined with a special price point.
 */
class ComboDeal {
    // The price of the packageMeal
    private $price;

    // This is going to be a set of items.  Since this is a set, the value of the array is not important.
    private $items = Array();

    /**
     * Creates a new package meal
     * @param $price
     * @param array $items
     */
    public function __construct($price, array $items) {
        $this->price = $price;
        // this is really creating a hashset, from the passed in array.
        foreach ($items as $item) {
            $this->items[$item] = $item;
        }
    }

    public function getPrice() {
        return $this->price;
    }

    /**
     * @return array
     */
    public function getItems() {
        return $this->items;
    }
} 