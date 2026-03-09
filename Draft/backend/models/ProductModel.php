<?php

class ProductModel {

private $conn;

public function __construct() {
include_once __DIR__ . '/../config/db_connect.php';
$this->conn = $conn;
global $conn;

}

// Get ALL products
public function getAll() {
$sql = "SELECT * FROM products";
$result = $this->conn->query($sql);
return $result->fetch_all(MYSQLI_ASSOC);
}

// Get ONE product
public function getById($id) {
$stmt = $this->conn->prepare("SELECT * FROM products WHERE product_ID= ?");
$stmt->bind_param("i", $id);
$stmt->execute();
return $stmt->get_result()->fetch_assoc();
}

//get products by category name
public function getProductsByCategory($category) {
    $stmt = $this->conn->prepare("SELECT * FROM  products where category_id = ?");
    $stmt->bind_param("i", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// public function getProductsByCategoryName($category_name) {
// $stmt = $this->conn->prepare("
// SELECT p.*
// FROM products p
// JOIN categories c ON p.category_id = c.category_id
// WHERE c.name = ?
// ");
// $stmt->bind_param("s", $category_name);
// $stmt->execute();
// $result = $stmt->get_result();
// return $result->fetch_all(MYSQLI_ASSOC);
// }

// Create product
public function create($name, $description, $price, $stock, $category_id, $image) {
$stmt = $this->conn->prepare(
    "INSERT INTO products (name, description, price, stock, category_id, image) 
    VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category_id, $image);
return $stmt->execute();
}

// Update product
public function update($id, $name, $description, $price, $stock, $category_id, $image) {
    $stmt = $this->conn->prepare(
        "UPDATE products 
            SET name=?, description=?, price=?, stock=?, category_id=?, image =? 
            WHERE product_ID=?"
);
$stmt->bind_param("ssdissi", $name, $description, $price, $stock, $category_id, $image, $id);
return $stmt->execute();
}

// Delete product
public function delete($id) {
$stmt = $this->conn->prepare("DELETE FROM products WHERE product_ID = ?");
$stmt->bind_param("i", $id);
return $stmt->execute();
}
public function updateStock($product_ID, $quantityChange) {

    $product_ID = (int)$product_ID;
    $quantityChange = (int)$quantityChange;

    if ($product_ID <= 0 || $quantityChange === 0) {
        return ["success" => false, "message" => "Invalid product ID or quantity change"];
    }

    //Grabs current stock
    $stmt = $this->conn->prepare("SELECT stock FROM products WHERE product_ID = ?");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ["success" => false, "message" => "Product not found"];
    }

    $currentStock = (int)$result->fetch_assoc()['stock'];
    $newStock = $currentStock + $quantityChange;

    //Prevents nnegative stock
    if ($newStock < 0) {
        return ["success" => false, "message" => "Insufficient stock"];
    }

    //Updates stock
    $update = $this->conn->prepare(
        "UPDATE products SET stock = ? WHERE product_ID = ?"
    );
    $update->bind_param("ii", $newStock, $product_ID);

    if ($update->execute()) {
        return ["success" => true, "newStock" => $newStock];
    }

    return ["success" => false, "message" => "Failed to update stock"];
}

}

?>
