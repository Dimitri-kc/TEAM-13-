<?php //basket Routes - receives route requests from frontend 
//JSON API for basket interactions
ini_set('display_errors', 1); //t
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
            $basketController->viewBasket(); //return array for both users/guests
            exit;
        /* default:
            echo json_encode(['success' => false, 'message' => 'Invalid action', 'data' => null]); //
            break; */
    }
    exit; 
}
echo json_encode(['success' => false, 'message' => 'Invalid request method', 'data' => null]);

//Note:
//for frontend path: href="../../routes/basketRoutes.php?action=view">View Basket
?>