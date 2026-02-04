<?php
// orderItemRoutes.php
header('Content-Type: application/json');
require_once __DIR__ . '/orderItemsController.php';

$controller = new OrderItemsController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'fetch') {
        $controller->fetchByOrder();
    }
} elseif ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'insert') {
        $controller->insert();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid POST action"]);
    }
}
