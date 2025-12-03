
<?php //basket functions - > will also contain some helpers for checkout process 

if(session_status() === PHP_SESSION_NONE) { //start session if not already started
    session_start();
}

//session-based basket functions for guest users
function addToSessionBasket($product_ID, $quantity) { //add item to sesion basket aka guest basket
    if (!isset($_SESSION['guest_basket'])) { //isset > check if variable is set, not null
        $_SESSION['guest_basket'] = []; //if session basket not set then create empty array
    }
   
    if (isset($_SESSION['guest_basket'][$product_ID])) {
        $_SESSION['guest_basket'][$product_ID] += $quantity; //if items already in basket, update quantity
    } else {
        $_SESSION['guest_basket'][$product_ID] = $quantity; //otherwise add new product with quantity to session (user basket)
    }
}

//get session basket function > user/guest
function getSessionBasket() {
    if (!isset($_SESSION['guest_basket'])) { //check if guest basket exists
        return []; //return empty array if not
    } else {
        return $_SESSION['guest_basket']; //return guest basket
    }
}

function removeFromSessionBasket($product_ID) { 
    //remove item from session basket
    if (isset($_SESSION['guest_basket'][$product_ID])) { //check if item exists in guest basket
        unset($_SESSION['guest_basket'][$product_ID]); //remove item from guest basket
    }
}

//clear session basket function > after checkout complete for guests so no orphaned data or after merge
function clearSessionBasket() {
    unset($_SESSION['guest_basket']); //remove guest basket from session
}

//-Helpers for checkout process- Some also used in basketController.php

//merge basket function (userID and basketModel) > upon login/register during checkout
function mergeBaskets($user_ID) {
    $basketModel = new Basket(); //create Basket
    $userBasket = $basketModel->fetchUserBasket($user_ID); //fetch user basket from database
    $basket_ID = $userBasket['basket_ID']; //get basket ID

    $sessionBasket = getSessionBasket(); //get guest basket from session

    //loop through session basket and add all items to user basket in database
    foreach ($sessionBasket as $product_ID => $quantity) { //for each item in guest basket
        $basketModel->addItemToBasket($basket_ID, $product_ID, $quantity); //add each item to user basket
    }

    clearSessionBasket(); //clear guest basket from session after merging - no orphaned data
}

function basket_total($basketItems) { //calculate total cost of items in basket
    $total = 0.0; 
    foreach ($basketItems as $item) { //for each item in basket
        $total += $item['price'] * $item['quantity']; //add price * quantity to total
    }
    return $total;
}

//Notes:
//functions called by basketController.php and checkout/payment Controller
//guest basket needs to merge with user basket upon login/register during checkout process
//using session to store guest basket items > user basket items held in database
//using isset to check if session variable exists or null

?>
