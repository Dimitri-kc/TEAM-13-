<?php

require_once __DIR__ . '/OrderModel.php';

class OrderController {

    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    // Create new order
    public function insert() {

        $user_ID     = $_SESSION['user_ID'] ?? null;
        $total_price = $_POST['total_price'] ?? null;
        $address     = $_POST['address'] ?? null;

        if (!$user_ID) {
            echo json_encode([
                "status" => "error",
                "message" => "User not logged in"
            ]);
            return;
        }

        if (!$total_price || !$address) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $order_ID = $this->orderModel->createOrder(
            $user_ID,
            $total_price,
            $address
        );

        if ($order_ID) {
            echo json_encode([
                "status"   => "success",
                "order_ID" => $order_ID
            ]);
        } else {
            echo json_encode([
                "status"  => "error",
                "message" => "Order creation failed"
            ]);
        }
    }

    // Get orders for logged-in user
    public function fetchByUser() {

        $user_ID = $_SESSION['user_ID'] ?? null;

        if (!$user_ID) {
            echo json_encode([
                "status"  => "error",
                "message" => "User not logged in"
            ]);
            return;
        }

        $orders = $this->orderModel->getOrdersByUser($user_ID);

        echo json_encode([
            "status" => "success",
            "count"  => count($orders),
            "data"   => $orders
        ]);
    }

    // Get all orders (admin)
    public function fetchAll() {

        if (($_SESSION['role'] ?? '') !== 'admin') {
            echo json_encode([
                "status"  => "error",
                "message" => "Unauthorized"
            ]);
            return;
        }

        $orders = $this->orderModel->getAllOrders();

        echo json_encode([
            "status" => "success",
            "count"  => count($orders),
            "data"   => $orders
        ]);
    }

    // Update order status (admin)
    public function updateStatus() {

        if (($_SESSION['role'] ?? '') !== 'admin') {
            echo json_encode([
                "status"  => "error",
                "message" => "Unauthorized"
            ]);
            return;
        }

        $order_ID = $_POST['order_ID'] ?? null;
        $status   = $_POST['status'] ?? null;

        $allowedStatuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];

        if (!$order_ID || !$status || !in_array($status, $allowedStatuses)) {
            echo json_encode([
                "status"  => "error",
                "message" => "Invalid order ID or status"
            ]);
            return;
        }

        $success = $this->orderModel->updateOrderStatus($order_ID, $status);

        echo json_encode([
            "status" => $success ? "success" : "error"
        ]);
    }
}
