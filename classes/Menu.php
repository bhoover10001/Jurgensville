<?php

/**
 * Class Menu
 */
class Menu {
    private $items = array(); // An of items by itemName.  The value is the price.  For a more complex object this would be a more complex object
    private $comboDeals = array();

    /**
     * adds a new item to the menu
     * @param $price
     * @param $menuItem
     */
    public function addItem($price, $menuItem) {
        $this->items[trim($menuItem)] = $price;
    }

    /**
     * adds a new item to the menu
     * @param $price
     * @param array $packageItems
     */
    public function addComboDeal($price, array $packageItems) {
        $this->comboDeals[] = new ComboDeal($price, $packageItems);
    }

    /**
     * Gets a list of all the package meals for this menu
     * @return array
     */
    public function getComboDeals() {
        return $this->comboDeals;
    }

    /**
     * Gets a list of all the items on the menu
     * @return array
     */
    public function getItems() {
        return $this->items;
    }

}
