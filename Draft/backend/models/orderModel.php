<?php

require_once __DIR__ . '/../config/db_connect.php';

class OrderModel {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Insert new order
    public function createOrder($user_ID, $total_price, $address) {

        $stmt = $this->conn->prepare(
            "INSERT INTO orders (user_ID, total_price, address)
             VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ids", $user_ID, $total_price, $address);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    // Get orders by user
    public function getOrdersByUser($user_ID) {

        $stmt = $this->conn->prepare(
            "SELECT * FROM orders
             WHERE user_ID = ?
             ORDER BY order_date DESC"
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $user_ID);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all orders (admin)
    public function getAllOrders() {

        $stmt = $this->conn->prepare(
            "SELECT * FROM orders
             ORDER BY order_date DESC"
        );

        if (!$stmt) {
            return [];
        }

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update order status
    public function updateOrderStatus($order_ID, $status) {

        $stmt = $this->conn->prepare(
            "UPDATE orders
             SET order_status = ?
             WHERE order_ID = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $status, $order_ID);

        return $stmt->execute();
    }
}
