<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/FavouriteController.php';

$favouriteController = new FavouriteController();

$method = $_SERVER['REQUEST_METHOD'];

$action = $method === 'POST'
    ? ($_POST['action'] ?? '')
    : ($_GET['action'] ?? '');

/* POST REQUESTS*/

if ($method === 'POST') {

    switch ($action) {

        case 'clear':
            $favouriteController->clear();
            break;

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid POST action"
            ]);
    }

}

/* GET REQUESTS*/

elseif ($method === 'GET') {

    switch ($action) {

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Invalid GET action"
            ]);
    }

}

/* INVALID METHOD */

else {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);

}