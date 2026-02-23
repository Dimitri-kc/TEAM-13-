<?php //basket Routes - receives route requests from frontend 
//JSON API for basket interactions

header('Content-Type: application/json'); //respond with JSON for frontend js
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/basketController.php'; //path to basketContrller.php
require_once __DIR__ . '/../services/basketFunctions.php'; //>guest specific

$basketController = new BasketController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $raw = file_get_contents("php://input"); //get raw POST data
    $data = json_decode($raw, true); //decode json to array
    if (!$data || empty($data['action'])) { //if no data/action specified, return msg
        echo json_encode(['success' => false, 'message' => "No action specified.", 'data' => null]);
        exit;
    }
    //sanitise action input for security -> XSS prevention
    $action = is_string($data['action']) ? htmlspecialchars($data['action']) : '';

    //switch to call relevant controller method based on action in .html
    switch ($action) {
        case 'add': //add action called
            $basketController->addItem($data);//call addItem method in controller
            break;

        case 'update': //update ation called
            $basketController->updateItem($data); //calling updateItem method in controller
            break;

        case 'remove': // called
            $basketController->removeItem($data); //method called
            break;

        default:
            echo json_encode(['success' => false, 'message' => "Invalid action.", 'data' => null]);
            break;
    }
    exit; //exit after handling POST request to prevent further processing
} 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //if retrieving basket info
    $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

    switch ($action) {
        case 'view':
            $items = $basketController->viewBasket(); //return array for both users/guests
            if (empty($items)) { //if no items in basket, return empty
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