<?php
// Tell the browser / frontend that all responses are JSON
header('Content-Type: application/json');

// Load the OrderController class so we can call its methods
require_once __DIR__ . '/orderController.php';

// Create an instance of the controller
$orderController = new OrderController();

// Detect whether the request is GET or POST
$method = $_SERVER['REQUEST_METHOD'];

// Handle POST requests

if ($method === 'POST') {

    // Read the action sent from the frontend (e.g. insert)
    $action = $_POST['action'] ?? '';

    switch ($action) {

        // Create a new order
        case 'insert':
            $orderController->insert();
            break;

        // Catch invalid POST actions
        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid POST action"
            ]);
    }
}

// Handle GET requests
elseif ($method === 'GET') {

    // Read the action from the query string (?action=fetch)
    $action = $_GET['action'] ?? '';

    switch ($action) {

        // Fetch all orders belonging to a user
        case 'fetch':
            $orderController->fetchByUser();
            break;

        // Catch invalid GET actions
        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid GET action"
            ]);
    }
}
