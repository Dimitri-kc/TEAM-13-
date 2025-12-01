<?php
// orderItemsModel.php - database class for order items
include_once __DIR__ . '/../../config/db_connect.php';

class OrderItem {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Add an item to an order
    public function addOrderItem($order_ID, $product_ID, $unit_price) {
        $stmt = $this->conn->prepare("INSERT INTO order_items (order_ID, product_ID, unit_price) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $order_ID, $product_ID, $unit_price);
        return $stmt->execute();
    }

    // Fetch items for a given order
    public function getItemsByOrder($order_ID) {
        $stmt = $this->conn->prepare("SELECT * FROM order_items WHERE order_ID = ?");
        $stmt->bind_param("i", $order_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Remove an item from an order
    public function deleteOrderItem($order_item_ID) {
        $stmt = $this->conn->prepare("DELETE FROM order_items WHERE order_item_ID = ?");
        $stmt->bind_param("i", $order_item_ID);
        return $stmt->execute();
    }
}
?>
