<?php
// orderModel.php - Handles all database operations for orders
include_once __DIR__ . '/../../config/db_connect.php';

class OrderModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /*
     * Add a new order
     */
    public function addOrder($user_ID, $total_price, $address) {
        $stmt = $this->conn->prepare("
            INSERT INTO orders (user_ID, total_price, address)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ids", $user_ID, $total_price, $address);

        return $stmt->execute();
    }

    /*
     * Get all orders
     */
    public function getAllOrders() {
        $query = "SELECT * FROM orders ORDER BY order_date DESC";
        $result = $this->conn->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Get all orders by a specific user
     */
    public function getOrdersByUser($user_ID) {
        $stmt = $this->conn->prepare("
            SELECT * FROM orders WHERE user_ID = ? ORDER BY order_date DESC
        ");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /*
     * Update an order’s status (Pending → Shipped → Delivered)
     */
    public function updateOrderStatus($order_ID, $order_status) {
        $stmt = $this->conn->prepare("
            UPDATE orders SET order_status = ? WHERE order_ID = ?
        ");
        $stmt->bind_param("si", $order_status, $order_ID);

        return $stmt->execute();
    }

    /*
     Delete an order */
    public function deleteOrder($order_ID) {
        $stmt = $this->conn->prepare("
            DELETE FROM orders WHERE order_ID = ?
        ");
        $stmt->bind_param("i", $order_ID);

        return $stmt->execute();
    }
}
?>
