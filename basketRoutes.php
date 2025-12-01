<?php //basket Routes - receives route requests from frontend html

session_start();

include_once '../../controllers/basketController.php'; //path to basketContrller.php
include_once '../../services/basketFunctions.php' //>guest specific

$basketController = new BasketController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $action = $_POST['action'] ?? ''; //get action from input

    //switch to call relevant controller method based on action in .html
    switch ($action) {
        case 'add': //add action called
            $basketController->addItem();//call addItem method in controller
            break;

        case 'update': //update ation called
            $basketController->updateItem(); //calling updateItem method in controller
            break;

        case 'remove': // called
            $basketController->removeItem(); //method called
            break;

        default:
            echo "Invalid action.";
            break;
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //if retrieving basket info
    $action = $_GET['action']?? '';

    switch ($action) {
        case 'view':
            $items = $basketController->viewBasket();
            break;

        default:
        echo "Invalid action";
        break;
    }

}
?>