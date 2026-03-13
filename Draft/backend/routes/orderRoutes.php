<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/orderController.php';

$orderController = new OrderController();

$method = $_SERVER['REQUEST_METHOD'];

$action = $method === 'POST'
    ? ($_POST['action'] ?? '')
    : ($_GET['action'] ?? '');


/* POST REQUESTS*/

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
                "status"=>"error",
                "message"=>"Invalid POST action"
            ]);
    }
}


/*  REQUESTS*/

elseif ($method === 'GET') {

    switch ($action) {

        case 'fetch':
            $orderController->fetchByUser();
            break;

        case 'fetchAll':
            $orderController->fetchAll();
            break;

        case 'dashboardStats':
            $orderController->dashboardStats();
            break;

        default:
            echo json_encode([
                "status"=>"error",
                "message"=>"Invalid GET action"
            ]);
    }
}

else {

    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid request method"
    ]);
}