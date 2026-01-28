<?php

class ReturnItemModel {
    private $db;

public function __construct() {
        
$this->db = new PDO("mysql:host=localhost;dbname=cs2team13_db", "root", "");
}


public function createMultiple($returnID, $items) {
ry {
$query = "INSERT INTO return_items (return_id, product_id, quantity, condition_note) 
VALUES (:return_id, :product_id, :quantity, :condition)";
$stmt = $this->db->prepare($query);

foreach ($items as $item) {
$stmt->execute([
':return_id' => $returnID,
':product_id' => $item['product_id'],
':quantity'   => $item['quantity'],
':condition'  => $item['condition'] ?? 'Good'
]);
}
return true;
} catch (PDOException $e) {
return false;
}
}

public function getByReturnId($returnID) {
$query = "SELECT * FROM return_items WHERE return_id = :return_id";
$stmt = $this->db->prepare($query);
$stmt->execute([':return_id' => $returnID]);
return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}