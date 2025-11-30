<?php //basket functions - > will also contain some helpers for checkout process in future

if(session_status() === PHP_SESSION_NONE) { //start session if not already started
    session_start();
}

//session-based basket functions for guest users
function addToSessionBasket($product_ID, $quantity) { //add item to sesion basket aka guest basket
    if (!isset($_SESSION['basket'])) { //isset > check if variable is set, not null
        $_SESSION['basket'] = []; //if session basket not set then create empty array
    }
   
    if (isset($_SESSION['basket'][$product_ID])) {
        $_SESSION['basket'][$product_ID] += $quantity; //if items already in basket, update quantity
    } else {
        $_SESSION['basket'][$product_ID] = $quantity; //otherwise add new product with quantity to session (user basket)
    }
}

//get session basket function > user/guest

//merge basket function (userID and basketModel) > upon login/register during checkout

//clear session basket function > after checkout complete for guests so no orphaned data

function basket_total($basketItems) { //calculate total cost of items in basket
    $total = 0.0; 
    foreach ($basketItems as $item) { //for each item in basket
        $total += $item['price'] * $item['quantity']; //add price * quantity to total
    }
    return $total;
}

//Notes:
//functions called by basketController.php and possibly checkout/payment Controller
//guest basket needs to merge with user basket upon login/register during checkout process
//using session to store guest basket items
//using isset to check if session variable exists or null
?>
