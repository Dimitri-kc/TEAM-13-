<?php
include_once __DIR__ . '/../../config/db_connect.php';

class Order {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // CREATE: Inserts a new order record
    public function createOrder($user_ID, $total_price, $address) {
        $stmt = $this->conn->prepare("INSERT INTO orders (user_ID, total_price, address) VALUES (?, ?, ?)");
        // i = integer, d = double (price), s = string
        $stmt->bind_param("ids", $user_ID, $total_price, $address);
        return $stmt->execute();
    }

    // READ (All): Finds all orders for a specific customer
    public function getOrdersByUser($user_ID) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // UPDATE: Changes price or address for an existing order
    public function updateOrder($order_ID, $total_price, $address) {
        $stmt = $this->conn->prepare("UPDATE orders SET total_price = ?, address = ? WHERE order_ID = ?");
        $stmt->bind_param("dsi", $total_price, $address, $order_ID);
        return $stmt->execute();
    }

    // DELETE: Removes an order record completely
    public function deleteOrder($order_ID) {
        $stmt = $this->conn->prepare("DELETE FROM orders WHERE order_ID = ?");
        $stmt->bind_param("i", $order_ID);
        return $stmt->execute();
    }
}
