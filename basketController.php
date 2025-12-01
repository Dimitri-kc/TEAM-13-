<?php //basketController.php - requests from hmtl pages

session_start();
//included so controller can connect to database and relevant methods
include_once '../../models/basketModel.php';//state file path
include_once '../../services/userFucntions.php'; //for require_login() & require_role() & guest basket functions

//Handling basket-related operations
class BasketController {


    public function addItem() {

        //get product details from session
        $product_ID = trim($_POST['product_ID'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');

        if (!$product_ID || !$quantity) { //check valid input
            echo "Product ID and quantity are required.";
            exit;
        }

        //if user logged in, use database basket
        if (isset($_SESSION['user_ID'])) { 
            $user_ID = $_SESSION['user_ID']; //retreive user ID from session
        
            //create Basket model instance
            $basketModel = new Basket(); //for database connection via basket model Basket.php holding basket class <
            //fetch or create user basket
            $basket = $basketModel->fetchUserBasket($user_ID);
            $basket_ID = $basket['basket_ID']; //get basket ID
            return $basketModel->addItemToBasket($basket_ID, $product_ID, $quantity);//adds item into database basket
            echo "Item added to basket.";
        } else {
            //guest user - use session-based basket
            addToSessionBasket($product_ID, $quantity); 
            echo "Item added to guest basket.";
        }

    }

    //update quantity of specific item in basket
    public function updateItem() {

        //get session details 
        $product_ID = trim($_POST['product_ID'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');

        if (!$product_ID || !$quantity) { //check valid input
            echo "Invalid request.";
            exit;
        }
        //if user logged in, update database basket
        if(isset($_SESSION['user_ID'])) {
            $user_ID = $_SESSION['user_ID'];

            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
            $basket_ID = $basket['basket_ID'];
            return $basketModel->updateItemQuantity($basket_ID, $product_ID, $quantity);
        } else {
            //if guest user, update session basket
            if (isset($_SESSION['basket'][$product_ID])) {
                $_SESSION['guest_basket'][$product_ID] = $quantity; //update quantity in session basket not user basket
            }
        }
        echo "Quantity updated.";
    }

    public function removeItem() {

        //get session details 
        $product_ID = trim($_POST['product_ID'] ?? '');

        if (!$product_ID) {//validate product ID
            echo "This item doesn't exist.";
            exit;
        }

        //if user logged in, remove from database basket
        if (isset($_SESSION['user_ID'])) {
            $user_ID = $_SESSION['user_ID'];

            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
            $basket_ID = $basket['basket_ID']; //get basket ID
            return $basketModel->removeItemFromBasket($basket_ID, $product_ID);//remove item by calling model method
        } else {
            //if guest user, remove from session basket
            removeFromSessionBasket($product_ID);
        }
        echo "Item removed from basket.";
    }

    //view basket contents
    public function viewBasket() {

        //if user logged in, fetch database basket items
        if (isset($_SESSION['user_ID'])) {
            //get session details
            $user_ID = $_SESSION['user_ID']; 

            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID);
            $basket_ID = $basket['basket_ID'];
            return $basketModel->fetchBasketItems($basket_ID);
        } else {
            //if guest user, fetch session basket items
            return getSessionBasketItems();
        }
    }
}


//Notes: 
//calls basketModel methods to interact with database
//adds to basket
//updates quantity 
//removes items from basket
//fetches user's basket items

//Update: wrapped logic in if() so basket can be handled based on login status (user/guest)
?>
