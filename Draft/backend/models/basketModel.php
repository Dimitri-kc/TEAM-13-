<?php //basketModel.php - database comms for basket
error_log("THIS IS BASKET MODEL: " . __FILE__);
error_log("Basket model loaded from: " . realpath(__DIR__ . '/../config/db_connect.php')); //log to confirm file is being loaded and path is correct

class Basket {
    private $conn;

    public function __construct() {
        global $conn; // Use the global $conn variable from db_connect.php
        require_once __DIR__ . '/../config/db_connect.php'; // Adjusted path to include db_connect.php
        if (!isset($conn) || !$conn) { //prevent errors if not established connection
            throw new Exception("Database connection failed in basketModel.php.");
        } 
        $this->conn = $conn;
        error_log("BasketModel constructor conn is: " . (isset($conn) && $conn ? "SET" : "NULL"));
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

    //get current stock for a product > used in controller to validate stock before updating
    public function getProductStock(int $product_ID) : ?int {
        $stmt = $this->conn->prepare("SELECT stock FROM products WHERE product_ID = ?"); //get stock for specific product
        $stmt->bind_param("i", $product_ID); //product_ID int
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$result) {
            return null; //product not found
        }
        return (int)$result['stock']; //return stock as int
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

//JOIN with products and return product details with quantity for each basket item
    public function fetchBasketItems($basket_ID) {
        //fetch all items in basket with product details
        $stmt = $this->conn->prepare("SELECT bi.product_ID, bi.quantity, p.name, p.price, p.image, p.stock, p.category_id 
        FROM basket_items bi 
        JOIN products p ON bi.product_ID = p.product_ID 
        WHERE bi.basket_ID = ?"); //JOIN to get prodcut details for each item in basket

        $stmt->bind_param("i", $basket_ID); //binding paramenter for SELECT
        $stmt->execute(); //execute with basket ID
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC); //return all associated items in basket
        $stmt->close();
        return $items;
    }
//fetch product details for given product IDs - used to get details for guest basket items stored in session
    public function fetchGuestBasketProducts(array $productIDs) : array {
        if (empty($productIDs)) return [];
        //int security
        $productIDs = array_values(array_filter(array_map('intval', $productIDs), fn($id) => $id > 0)); //filter out non int values - array indexed for prep-statements
        if (empty($productIDs)) return []; //if no valid ids, return empty
        $placeholders = implode(',', array_fill(0, count($productIDs), '?')); //placeholders for prep-statement
        $types = str_repeat('i', count($productIDs)); //strin of i for count of IDs for binding parameters
        $stmt = $this->conn->prepare("SELECT product_ID, name, price, image, stock, category_id
                FROM products
                WHERE product_ID IN ($placeholders)"); //select product details for given IDs
        $stmt->bind_param($types, ...$productIDs); //bind parametes
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products; 
    }

    //clear basket items after checkout
    public function clearBasket($basket_ID) {
        $stmt = $this->conn->prepare("DELETE FROM basket_items WHERE basket_ID = ?");
        $stmt->bind_param("i", $basket_ID); //binding parameter for DELETE
        return $stmt->execute(); //clear all items from specific basket
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