<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/orderItemsController.php';

$controller = new OrderItemsController();
$method = $_SERVER['REQUEST_METHOD'];

$action = $method === 'POST'
    ? ($_POST['action'] ?? '')
    : ($_GET['action'] ?? '');

// HANDLE POST REQUESTS

if ($method === 'POST') {

    switch ($action) {

        case 'insert':
            $controller->insert();
            break;

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid POST action"
            ]);
    }
}
// HANDLE GET REQUESTS
elseif ($method === 'GET') {

    switch ($action) {

        case 'fetch':
            $controller->fetchByOrder();
            break;

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid GET action"
            ]);
    }
}
// INVALID METHOD

else {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
