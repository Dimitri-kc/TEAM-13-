<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/OrderController.php';

$orderController = new OrderController();

$method = $_SERVER['REQUEST_METHOD'];

$action = $method === 'POST'
    ? ($_POST['action'] ?? '')
    : ($_GET['action'] ?? '');

// Handle POST requests
if ($method === 'POST') {

    switch ($action) {

        case 'insert':
            $orderController->insert();
            break;

        case 'updateStatus':
            $orderController->updateStatus();
            break;

        default:
            echo json_encode([
                "status"  => "error",
                "message" => "Invalid POST action"
            ]);
    }
}

// Handle GET requests
elseif ($method === 'GET') {

    switch ($action) {

        case 'fetch':
            $orderController->fetchByUser();
            break;

        case 'fetchAll':
            $orderController->fetchAll();
            break;

        default:
            echo json_encode([
                "status"  => "error",
                "message" => "Invalid GET action"
            ]);
    }
}

// Invalid request method
else {

    echo json_encode([
        "status"  => "error",
        "message" => "Invalid request method"
    ]);
}
