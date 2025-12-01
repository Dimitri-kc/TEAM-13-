<?php

class CategoryModel {

private $conn;

public function __construct() {
include 'backend/db_connect.php';
global $conn;
$this->conn = $conn;
}

public function getAll() {
return $this->conn->query("SELECT * FROM categories");
}

public function getById($id) {
$stmt = $this->conn->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
return $stmt->get_result();
}

public function create($name) {
$stmt = $this->conn->prepare("INSERT INTO categories (name) VALUES (?)");
$stmt->bind_param("s", $name);
return $stmt->execute();
}

public function update($id, $name) {
$stmt = $this->conn->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
$stmt->bind_param("si", $name, $id);
return $stmt->execute();
    }

public function delete($id) {
$stmt = $this->conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $id);
return $stmt->execute();
}
}
?>
