<?php //basket Routes - receives route requests from frontend html

session_start();

include_once '../../controllers/basketController.php'; //path to basketContrller.php
include_once '../../services/basketFunctions.php'; //>guest specific

$basketController = new BasketController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : ''; //get action from input

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
            echo "Invalid action."; //more info in controller echo
            break;
    }

} 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //if retrieving basket info
    $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

    switch ($action) {
        case 'view':
            $items = $basketController->viewBasket(); //return array for both users/guests
            if (!empty($items)) {
                $items = [];
            }
            echo json_encode(['success' => true, 'items' => $items]); 
            break;

            default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']); //unknown action
        break;
    }

}
//Note:confirm json as js to be implemented
//for frontend path: href="../../routes/basketRoutes.php?action=view">View Basket

?>