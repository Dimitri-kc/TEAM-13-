<?php
require_once __DIR__ . '/orderModel.php';

class OrderController {

    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    // POST: create order
    public function insert() {
        $user_ID = $_POST['user_ID'] ?? null;
        $total_price = $_POST['total_price'] ?? null;
        $address = $_POST['address'] ?? null;

        if (!$user_ID || !$total_price || !$address) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $success = $this->orderModel->createOrder($user_ID, $total_price, $address);

        echo json_encode([
            "status" => $success ? "success" : "error"
        ]);
    }

    // GET: fetch orders for user
    public function fetchByUser() {
        $user_ID = $_GET['user_ID'] ?? null;

        if (!$user_ID) {
            echo json_encode([
                "status" => "error",
                "message" => "User ID required"
            ]);
            return;
        }

        $orders = $this->orderModel->getOrdersByUser($user_ID);

        echo json_encode([
            "status" => "success",
            "data" => $orders
        ]);
    }
}
