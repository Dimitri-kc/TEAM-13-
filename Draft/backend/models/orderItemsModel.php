<?php

require_once __DIR__ . '/db_connect.php';

class OrderItem {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

   
     //Fetch all items for a specific order
     
    public function getItemsByOrder($order_ID) {

        $stmt = $this->conn->prepare(
            "SELECT * FROM order_items 
             WHERE order_ID = ?"
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $order_ID);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

      //Insert a single order item
    
    public function addOrderItem($order_ID, $product_ID, $unit_price, $quantity) {

        $stmt = $this->conn->prepare(
            "INSERT INTO order_items 
             (order_ID, product_ID, unit_price, quantity)
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iidi", 
            $order_ID, 
            $product_ID, 
            $unit_price, 
            $quantity
        );

        return $stmt->execute();
    }

    
     //Delete all items for an order admin
     
    public function deleteItemsByOrder($order_ID) {

        $stmt = $this->conn->prepare(
            "DELETE FROM order_items 
             WHERE order_ID = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $order_ID);

        return $stmt->execute();
    }
}