<?php

class ReturnItemModel {

private $conn;

public function __construct() {
include_once __DIR__ . '/../db_connect.php';
$this->conn = $conn;
}

public function createItems(int $returnId, array $items) {
$stmt = $this->conn->prepare(
"INSERT INTO return_items (return_ID, product_ID, quantity, item_reason)
VALUES (?, ?, ?, ?)"
);

foreach ($items as $item) {
$productId = $item['product_id'];
$qty       = $item['quantity'];
$reason    = $item['reason'];

$stmt->bind_param("iiis", $returnId, $productId, $qty, $reason);

if (!$stmt->execute()) {
return false; 
}
}

return true; 
}
}
