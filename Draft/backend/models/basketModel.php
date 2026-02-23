<?php //basketModel.php - database comms for basket

class Basket {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../config/db_connect.php'; // Adjusted path to include db_connect.php
        global $conn; //variable from db_connect.php for database connection
        $this->conn = $conn;
    }

    //creates or fetches existing basket
    public function fetchUserBasket($user_ID) { 
        //check if basket already exists for user_ID 
        $stmt = $this->conn->prepare("SELECT * FROM basket WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID); // user_ID int
        $stmt->execute();
        $result = $stmt->get_result();
        $basket = $result->fetch_assoc(); //only one basket per user so fetch assoc array
        $stmt->close();

        if ($basket) { //if basket exists, return it
            return $basket;
        } else {
            //create new basket
            $stmt = $this->conn->prepare("INSERT INTO basket (user_ID) VALUES (?)");
            $stmt->bind_param("i", $user_ID); //user_ID int
            $newBasket = $stmt->execute(); //
            $newID = $this->conn->insert_id; //get id of new basket
            $stmt->close(); 
            return $newBasket ? ['basket_ID' => $newID, 'user_ID' => $user_ID] : null; //return new basket details otherwise null if creation failed
        }
    }

    public function addItemToBasket($basket_ID, $product_ID, $quantity) {
        //check if item already in basket
        $stmt = $this->conn->prepare("INSERT INTO basket_items (basket_ID, product_ID, quantity) VALUES (?, ?, ?)
                                      ON DUPLICATE KEY UPDATE quantity = quantity + ?"); //if item exists, update quantity
        $stmt->bind_param("iiii", $basket_ID, $product_ID, $quantity, $quantity); //binding parameters for INSERT and UPDATE
        return $stmt->execute(); //add item or update quantity if item exists in basket
    }

    public function updateItemQuantity($basket_ID, $product_ID, $quantity) {
        //update quantity of specific basket item
        $stmt = $this->conn->prepare("UPDATE basket_items SET quantity = ? WHERE basket_ID = ? AND product_ID = ?");
        $stmt->bind_param("iii", $quantity, $basket_ID, $product_ID); //binding parameters for UPDATE
        return $stmt->execute(); //new quantity for specific item
    }

    public function removeItemFromBasket($basket_ID, $product_ID) {
        //remove specific basket item
        $stmt = $this->conn->prepare("DELETE FROM basket_items WHERE basket_ID = ? AND product_ID = ?"); 
        $stmt->bind_param("ii", $basket_ID, $product_ID); //binding parameters for DELETE
        return $stmt->execute(); 
    }

    //adjusted below to JOIN with products and return product details with quantity for each basket item
    public function fetchBasketItems($basket_ID) {
        //fetch all items in basket with product details
        $stmt = $this->conn->prepare("SELECT bi.product_ID, bi.quantity, p.name, p.price, p.image, p.stock, p.category_id 
        FROM basket_items bi JOIN products p ON bi.product_ID = p.product_ID WHERE bi.basket_ID = ?"); //JOIN to get prodcut details for each item in basket
        $stmt->bind_param("i", $basket_ID); //binding paramenter for SELECT
        $stmt->execute(); //execute with basket ID
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC); //return all associated items in basket
        $stmt->close();
        return $items;
    }

}
//Notes:
//communicates with basket table in database
//create basket or retreive existing basket 
//adds items to basket table
//updates item quantity in table
//removes items from " "
//fetches user basket items from " "
//Design allows for one basket per user, with multiple items in each basket, and supports both logged in users and guests via session management in controller and services
//MySQLi is used in db_connect.php, prepared statements changed to reflect this for consistency/funcitonality.
?>