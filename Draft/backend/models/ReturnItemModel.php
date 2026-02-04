<?php

class ReturnItemModel {
    private $db;

public function __construct() {
$this->db = new mysqli("localhost", "root", "", "cs2team13_db");

if ($this->db->connect_error) {
die("Connection failed: " . $this->db->connect_error);
}
}

public function createMultiple($returnID, $items) {
        
$query = "INSERT INTO return_items (return_id, product_id, quantity, condition_note) VALUES (?, ?, ?, ?)";
$stmt = $this->db->prepare($query);

if (!$stmt) return false;

foreach ($items as $item) {
$productID = $item['product_id'];
$quantity = $item['quantity'];
$condition = $item['condition'] ?? 'Good';

$stmt->bind_param("iiis", $returnID, $productID, $quantity, $condition);
if (!$stmt->execute()) {
return false;
}
}
$stmt->close();
return true;
}

public function getByReturnId($returnID) {
$query = "SELECT * FROM return_items WHERE return_id = ?";
$stmt = $this->db->prepare($query);
        
if (!$stmt) return [];
$stmt->bind_param("i", $returnID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
        
$stmt->close();
return $data;
    }
}