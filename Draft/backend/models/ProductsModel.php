<?php

class ProductModel {

private $conn;

public function __construct() {
include_once __DIR__ . '/../db_connect.php';
$this->conn = $conn;
}

// Get ALL products
public function getAll() {
$sql = "SELECT * FROM products";
$result = $this->conn->query($sql);
return $result->fetch_all(MYSQLI_ASSOC);
}

// Get ONE product
public function getById($id) {
$stmt = $this->conn->prepare("SELECT * FROM products WHERE product_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
return $stmt->get_result()->fetch_assoc();
}

// Create product
public function create($name, $description, $price, $stock, $category, $image) {
$stmt = $this->conn->prepare(
    "INSERT INTO products (name, description, price, stock, category, image_path) 
    VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category, $image);
return $stmt->execute();
}

// Update product
public function update($id, $name, $description, $price, $stock, $category, $image) {
    $stmt = $this->conn->prepare(
        "UPDATE products 
            SET name=?, description=?, price=?, stock=?, category=?, image_path=? 
            WHERE product_ID=?"
);
$stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $category, $image, $id);
return $stmt->execute();
}

// Delete product
public function delete($id) {
$stmt = $this->conn->prepare("DELETE FROM products WHERE product_ID = ?");
$stmt->bind_param("i", $id);
return $stmt->execute();
}
}

?>
