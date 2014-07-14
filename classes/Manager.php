<?php

/**
 * Created by PhpStorm.
 * User: Brian Hoover
 * Date: 7/12/2014
 * Time: 10:41 AM
 *
 * This is the business rule manager for the application.
 * It's callsed
 */
class Manager {

    public static $NOTFULFILLABLERESULT = "nil";

    // Regular expressions to validate each line
    private static $VALIDITEMNAME = "/^[a-z_]+$/";
    private static $VALIDRESTAURANTID = "/^[0-9]+$/";
    private static $VALIDPRICE = "/^[0-9]+(\.[0-9]{1,2})?$/";

    // An array of menus.  The key is the restaurant id.
    private $menus = array();

    public function runManager($fileName, array $requestedItems) {
        $requestedItems = $this->cleanData($requestedItems);
        ini_set('auto_detect_line_endings', TRUE);
        try {
            $handle = fopen($fileName, "r");
        } catch (Exception $e) {
            throw new Exception("File " . $fileName . " was not found");
        }
        $bestRestaurant = null;
        $bestPrice = INF;
        while ($data = fgetcsv($handle, 1024)) {
            // for each line, validate the data, determine if it's relevant to the requested items.  If so,
            // put it into the appropriate menu and then calculate the best price for the changed menu.
            if (!$this->validateLine($data)) {
                // This line wasn't valid.  TODO - Should be logged somewhere.
                continue;
            }
            if ($this->isEntryRelevant($data, $requestedItems)) {
                // There are no entries on this line that can fulfill the order.
                continue;
            }
            $restaurantId = (integer)$data[0];
            $price = (float)$data[1];
            $items = array_slice($data, 2);
            /** @var Menu $menu */
            $menu = $this->getMenu($restaurantId);
            $this->addEntryToMenu($menu, $items, $price);
            $comboPrice = $this->getPriceForRequestedItems($requestedItems, $menu);
            if ($comboPrice != static::$NOTFULFILLABLERESULT && $comboPrice < $bestPrice) {
                $bestPrice = $comboPrice;
                $bestRestaurant = $restaurantId;
            }
        }
        fclose($handle);
        if (is_infinite($bestPrice)) {
            return static::$NOTFULFILLABLERESULT;
        } else {
            return $bestRestaurant . ", " . $bestPrice;
        }

    }

    /**
     * Takes an array of string and returns an array with all leading spaces removed.
     *
     * @param array $data
     * @return array
     */
    private function cleanData($data) {
        $returnData = array();
        foreach ($data as $entry) {
            $returnData[] = trim($entry);
        }
        return $returnData;
    }

    /**
     * Makes sure that the line being provided from the CSV is valid.  Returns true if it's valid.  false if
     * it's not.
     * From the specifications, a valid line has:
     * It is an array
     * The first item as an integer
     * The second item as a decimal point
     * An unlimited number of entries after the second item.  Each entry after the second item has to
     * consist of only lower case letters and underscores.
     *
     * @param array $data
     * @return boolean
     */
    public function validateLine($data) {
        if (!is_array($data)) {
            return false;
        }
        if (count($data) < 3) {
            return false;
        }
        if (!preg_match(static::$VALIDRESTAURANTID, trim($data[0]))) {
            return false;
        }
        if (!preg_match(static::$VALIDPRICE, trim($data[1]))) {
            return false;
        }
        for ($i = 2; $i < count($data); $i++) {
            if (preg_match(static::$VALIDITEMNAME, trim($data[$i])) === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * This is a bit of a micro-optimization.  In general, many of the entries won't be relevant
     * to fulfilling the requested items list.  Since we are loading and getting the best price at the same time,
     * this eliminates the need to either load or calc the best price.
     *
     * An item is relevant if it has an entry that in the requested item list.
     * @param $items
     * @param $requestedItems
     * @return boolean
     */
    private function isEntryRelevant($items, $requestedItems) {
        foreach ($items as $item) {
            foreach ($requestedItems as $requestedItem) {
                if ($item === $requestedItem) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns the menu for a specific restaurant.  If the menu is not already initialized,
     * creates a new menu record.
     *
     * @param $restaurantId
     * @return Menu
     */
    private function getMenu($restaurantId) {
        if (!array_key_exists($restaurantId, $this->menus)) {
            $this->menus[$restaurantId] = new Menu();
        }
        /** @var Menu $menu */
        return $this->menus[$restaurantId];
    }

    /**
     * Adds an entry to the menu, either new menu item or a combo deal.
     *
     * @param Menu $menu
     * @param array $items
     * @param float $price
     */
    private function addEntryToMenu($menu, $items, $price) {
        if (count($items) === 1) {
            $menu->addItem($price, $items[0]);
        } else {
            $menu->addComboDeal($price, $items);
        }
    }

    /**
     * For a specific menu, gets the total best price for an array of items.
     * If the menu cannot fulfill the requested items, returns $NOTFULFILLABLERESULT
     *
     * @param array $requestedItems
     * @param Menu $menu
     * @return mixed -
     */
    public function getPriceForRequestedItems(array $requestedItems, Menu $menu) {
        /** @var float $price */
        $price = INF;
        $comboPrice = 0;
        foreach ($requestedItems as $requestedItem) {
            if (!array_key_exists($requestedItem, $menu->getItems())) {
                // Since this item doesn't exist here, the order can't be fulfilled from the standard menu items
                $comboPrice = 0;
                break;
            }
            $comboPrice += $menu->getItems()[$requestedItem];
        }
        if ($comboPrice != 0) {
            $price = $comboPrice;
        }
        $packagePrice = $this->getPriceForComboDeal($price, $requestedItems, $menu);
        if ($packagePrice < $price) {
            $price = $packagePrice;
        }
        if ($price == INF) {
            return static::$NOTFULFILLABLERESULT;
        }
        return $price;
    }

    /**
     * Checks the packages and see if the requested items can be fulfilled from any combo meal or from
     * a combination of a combo meal and ala-carte items, and the price has to be better than the already
     * passed in price.
     * If it can be fulfilled, and the price is better, then returns the combination price.
     *
     * @param $bestPrice
     * @param array $requestedItems
     * @param menu $menu
     * @return float
     */
    private function getPriceForComboDeal($bestPrice, array $requestedItems, menu $menu) {
        $price = $bestPrice;
        /** @var  ComboDeal $comboDeal */
        foreach ($menu->getComboDeals() as $comboDeal) {
            if ($price < $comboDeal->getPrice()) {
                // This package can't be better than the best available price already found, so move onto the
                // next package
                continue;
            }
            $comboMealPrice = $comboDeal->getPrice();
            $canBeFulfilled = false;
            /** @var string $requestedItem */
            foreach ($requestedItems as $requestedItem) {
                if (array_key_exists($requestedItem, $comboDeal->getItems())) {
                    // The combo meal has this particular item.
                    // It can be overriden later in the process if any item is not available
                    $canBeFulfilled = true;
                } else if (array_key_exists($requestedItem, $menu->getItems())) {
                    // There is an ala-carte item that is necessary to completely fulfill the order, so
                    // add its price to the total meal.
                    $comboMealPrice += $menu->getItems()[$requestedItem];
                } else {
                    // this package cannot fulfill the requested items, since neither the package or
                    // the ala-carte items have the requested item.
                    $canBeFulfilled = false;
                    break;
                }
            }
            if ($canBeFulfilled && $comboMealPrice < $price) {
                $price = $comboMealPrice;
            }
        }
        return $price;
    }

} 