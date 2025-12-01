<?php //basketController.php - requests from hmtl pages

session_start();
//included so controller can connect to database and relevant methods
include_once '../../models/basketModel.php';//state file path
//include_once '../../services/userFucntions.php'; //for require_login() & require_role() - commented out for now b/c guest checkout option

//Handling basket-related operations
class BasketController {


    public function addItem() {
        //require_login(); //ensure user is logged in

        //get user/product details from session
        $user_ID = $_SESSION['user_ID']; 
        $product_ID = trim($_POST['product_ID'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');

        if (!$product_ID || !$quantity) { //check valid input
            echo "Product ID and quantity are required.";
            exit;
        }
        //create Basket model instance
        $basketModel = new Basket(); //for database connection via basket model Basket.php holding basket class <
        //fetch or create user basket
        $basket = $basketModel->fetchUserBasket($user_ID);
        $basket_ID = $basket['basket_ID']; //get basket ID
        return $basketModel->addItemToBasket($basket_ID, $product_ID, $quantity);
        echo "Item added to basket.";
    }

    //update quantity of specific item in basket
    public function updateItem() {
        //require_login(); 
        //get session details
        $user_ID = $_SESSION['user_ID']; 
        $product_ID = trim($_POST['product_ID'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');

        if (!$product_ID || !$quantity) { //check valid input
            echo "Invalid request.";
            exit;
        }
        $basketModel = new Basket();
        $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
        $basket_ID = $basket['basket_ID'];
        return $basketModel->updateItemQuantity($basket_ID, $product_ID, $quantity);
        echo "Quantity updated.";
    }

    public function removeItem() {
        //require_login();
        //get session details
        $user_ID = $_SESSION['user_ID']; 
        $product_ID = trim($_POST['product_ID'] ?? '');

        if (!$product_ID) {//validate product ID
            echo "This item doesn't exist.";
            exit;
        }

        $basketModel = new Basket();
        $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
        $basket_ID = $basket['basket_ID']; //get basket ID
        return $basketModel->removeItemFromBasket($basket_ID, $product_ID);//remove item by calling model method
        echo "Item removed from basket.";
    }

    //view basket contents
    public function viewBasket() {
        //require_login();
        //get session details
        $user_ID = $_SESSION['user_ID']; 

        $basketModel = new Basket();
        $basket = $basketModel->fetchUserBasket($user_ID);
        $basket_ID = $basket['basket_ID'];
        return $basketModel->fetchBasketItems($basket_ID);
    }
}


//Notes: 
//calls basketModel methods to interact with database
//adds to basket
//updates quantity 
//removes items from basket
//fetches user's basket items

//commented out userFunctions links for now as not using require_login() or require_role() in basket functions yet due to guest checkout
//however adjustment needed, for once user login is required for basket access (upon sign-in)

?>
