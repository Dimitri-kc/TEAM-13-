<?php //basket functions - > will also contain some helpers for checkout process 
//session-based basket helpers + merge logic
require_once __DIR__ . '/../models/basketModel.php'; //include basketModel for user's basket interactions 

//get session basket function > user/guest
function getSessionBasket(): array { //fetch session guest basket > for users, merge with DB basket upon register/login
    return $_SESSION['guest_basket'] ?? []; //return guest basket if exists or empty array if not
}

//session-based basket functions for guest users
function addToSessionBasket($product_ID, $quantity): void { //add item to session basket. void - no return needed, just update variable
    //int for security
    $product_ID = (int)$product_ID;  
    $quantity = (int)$quantity;  
    if ($product_ID <=0 || $quantity <=0) {
        return; //invalid input, exit with no modification
    }
    if (!isset($_SESSION['guest_basket']) || !is_array($_SESSION['guest_basket'])) { //initialise guest basket
        $_SESSION['guest_basket'] = []; //if session basket not set then create empty array
    }
    //if items already in basket, update quantity, otherwise add new product with quantity to session
    $_SESSION['guest_basket'][$product_ID] = ($_SESSION['guest_basket'][$product_ID] ?? 0) + $quantity; 
}

//update item quantity in session basket - guest specific as DB updated for users in controller
function updateSessionBasket($product_ID, $quantity): void {
    //int for security
    $product_ID = (int)$product_ID;
    $quantity = (int) $quantity;
    if ($product_ID <=0) return; //invalid id > exit
    if ($quantity <=0) { // < MAY NEED TO CHANGE IN FUTURE FOR STOCK REFLECTION TO USER?
        removeFromSessionBasket($product_ID);
        return; //invalid quantity > remove from basket
    }
    if (!isset($_SESSION['guest_basket']) || !is_array($_SESSION['guest_basket'])) {
        $_SESSION['guest_basket'] = []; //if session basket not set then create empty array > initialise basket
    }
    $_SESSION['guest_basket'][$product_ID] = $quantity; //otherwise add new product with quantity to session (user basket)
    
}

function removeFromSessionBasket($product_ID): void { 
    $product_ID = (int)$product_ID; //int security
    if ($product_ID <=0) return; //invalid id so exit    
    //remove item from session basket
    if (isset($_SESSION['guest_basket'][$product_ID])) { //check if item exists in guest basket
        unset($_SESSION['guest_basket'][$product_ID]); //remove item from guest basket
    }
}

//clear session basket > after checkout complete for guests or after merge so no orphaned data.
function clearSessionBasket(): void {
    unset($_SESSION['guest_basket']); //remove guest basket from session
}

//-Helpers for checkout process- Some also used in basketController.php

//merge basket function (userID and basketModel) > upon login/register during checkout
function mergeBaskets($user_ID): void {
    //security constraints
    $user_ID = (int)$user_ID; //int security
    if ($user_ID <=0) return; //invalid user Id so exit
    $sessionBasket = getSessionBasket();
    if (empty($sessionBasket)) return; //if no items to merge then exit

    $basketModel = new Basket(); //create Basket
    $userBasket = $basketModel->fetchUserBasket($user_ID); //fetch user basket from database

    //if no basket exists for user, then fetch new basket details from one created in basketModel.php fetchUserBasket
    if (!$userBasket || empty($userBasket['basket_ID'])) { //don't clear guest basket, let it merge next login
    return; //added for error handling - if basket creation/fetching fails then exit merging process - user can still shop, add items to session basket -> merge upon next login/register
    }
    $basket_ID = (int)$userBasket['basket_ID']; //int prevents SQL injection, ensures correct data type by forcfully converting ID to int

    //loop through session basket and add all items to user basket in database
    foreach ($sessionBasket as $product_ID => $quantity) { //for each item in guest basket
        $product_ID = (int)$product_ID;
        $quantity = (int) $quantity;
        if ($product_ID < 0 || $quantity < 0) { //if invalid, skip
            $basketModel->addItemToBasket($basket_ID, $product_ID, $quantity); //add each item to user basket
        }
    }

    clearSessionBasket(); //clear guest basket from session after merging - no orphaned data
}

function basket_total(array $basketItems): float { //calculate total cost of items in basket
    $total = 0.0; 
    foreach ($basketItems as $item) { //for each item in basket
    $price = (float)($item['price'] ?? 0); //get price, default to 0 if not set
    $quantity = (int)($item['quantity'] ?? 0); //get quantity, default to 0 if not set    
    $total += $price * $quantity; //add price * quantity to total
    }
    return $total;
}

//Notes:
//functions called by basketController.php and checkout/payment Controller
//guest basket needs to merge with user basket upon login/register during checkout process
//using session to store guest basket items > user basket items held in database
//using isset to check if session variable exists or null

?>
