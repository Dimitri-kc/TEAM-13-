<?php

require_once __DIR__ . '/../models/orderModel.php';

class OrderController {

    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    /* DASHBOARD REPORT*/

    public function dashboardStats() {

        $allTime = $this->orderModel->getTotalOrders();
        $thisMonth = $this->orderModel->getMonthlyOrders();
        $pending = $this->orderModel->getPendingOrders();
        $chart = $this->orderModel->getOrdersPerMonth();

        echo json_encode([
            "status" => "success",
            "orders" => [
                "all_time" => $allTime,
                "this_month" => $thisMonth,
                "pending" => $pending,
                "chart" => $chart
            ]
        ]);
    }

    /* CREATE ORDER*/

    public function insert() {

        $user_ID = $_SESSION['user_ID'] ?? null;
        $total_price = $_POST['total_price'] ?? null;
        $address = $_POST['address'] ?? null;

        if (!$user_ID) {
            echo json_encode(["status"=>"error","message"=>"User not logged in"]);
            return;
        }

        if (!$total_price || !$address) {
            echo json_encode(["status"=>"error","message"=>"Missing required fields"]);
            return;
        }

        $order_ID = $this->orderModel->createOrder($user_ID,$total_price,$address);

        if ($order_ID) {

            echo json_encode([
                "status"=>"success",
                "order_ID"=>$order_ID
            ]);

        } else {

            echo json_encode([
                "status"=>"error",
                "message"=>"Order creation failed"
            ]);
        }
    }

    /* USER ORDERS*/

    public function fetchByUser() {

        $user_ID = $_SESSION['user_ID'] ?? null;

        if (!$user_ID) {
            echo json_encode(["status"=>"error","message"=>"User not logged in"]);
            return;
        }

        $orders = $this->orderModel->getOrdersByUser($user_ID);

        echo json_encode([
            "status"=>"success",
            "count"=>count($orders),
            "data"=>$orders
        ]);
    }

    /* ADMIN FETCH ALL ORDERS*/

    public function fetchAll() {

        if (($_SESSION['role'] ?? '') !== 'admin') {
            echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
            return;
        }

        $orders = $this->orderModel->getAllOrders();

        echo json_encode([
            "status"=>"success",
            "count"=>count($orders),
            "data"=>$orders
        ]);
    }

    /* UPDATE ORDER STATUS*/

    public function updateStatus() {

        if (($_SESSION['role'] ?? '') !== 'admin') {
            echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
            return;
        }

        $order_ID = $_POST['order_ID'] ?? null;
        $status = $_POST['order_status'] ?? null;

        $allowed = ['Pending','Shipped','Delivered','Cancelled'];

        if (!$order_ID || !$status || !in_array($status,$allowed)) {
            echo json_encode(["status"=>"error","message"=>"Invalid order ID or status"]);
            return;
        }

        $success = $this->orderModel->updateOrderStatus($order_ID,$status);

        echo json_encode([
            "status" => $success ? "success" : "error"
        ]);
    }
}