<?php //basketModel.php - database comms for basket

class Basket {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../config/db_connect.php'; // Adjusted path to include db_connect.php
        $this->conn = $dbConnection;
    }

    //creates or fetches existing basket
    public function fetchUserBasket($user_ID) { 
        //check if basket already exists for user_ID 
        $stmt = $this->conn->prepare("SELECT * FROM basket WHERE user_ID = ?");
        $stmt->execute([$user_ID]);
        $basket = $stmt->fetch(PDO::FETCH_ASSOC); //only one basket per user
        if ($basket) {
            //if basket exists, return it
            return $basket;
        } else {
            //create new basket
            $stmt = $this->conn->prepare("INSERT INTO basket (user_ID) VALUES (?)");
            $stmt->execute([$user_ID]);
            return $this->conn->lastInsertId(); //return new basket ID
        }
    }

    public function addItemToBasket($basket_ID, $product_ID, $quantity) {
        //check if item already in basket
        $stmt = $this->conn->prepare("INSERT INTO basket_items (basket_ID, product_ID, quantity) VALUES (?, ?, ?)
                                      ON DUPLICATE KEY UPDATE quantity = quantity + ?"); //if item exists, update quantity
        return $stmt->execute([$basket_ID, $product_ID, $quantity, $quantity]); //replace placeholders with actual values
    }

    public function updateItemQuantity($basket_ID, $product_ID, $quantity) {
        //update quantity of specific basket item
        $stmt = $this->conn->prepare("UPDATE basket_items SET quantity = ? WHERE basket_ID = ? AND product_ID = ?");
        return $stmt->execute([$quantity, $basket_ID, $product_ID]);
    }

    public function removeItemFromBasket($basket_ID, $product_ID) {
        //remove basket item
        $stmt = $this->conn->prepare("DELETE FROM basket_items WHERE basket_ID = ? AND product_ID = ?"); 
        return $stmt->execute([$basket_ID, $product_ID]);
    }

    public function fetchBasketItems($basket_ID) {
        //fetch all items in basket
        $stmt = $this->conn->prepare("SELECT * FROM basket_items WHERE basket_ID = ?");
        $stmt->execute([$basket_ID]); //execute with basket ID
        return $stmt->fetchAll(PDO::FETCH_ASSOC); //return all associated items
    }

}
//Notes:
//communicates with basket table in database
//create basket or retreive existing basket 
//adds items to basket table
//updates item quantity in table
//removes items from " "
//fetches user basket items from " "
?>