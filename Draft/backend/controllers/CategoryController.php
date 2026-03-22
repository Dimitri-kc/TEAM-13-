<?php

// CATEGORY CONTROLLER

// Connect to DB
require_once __DIR__ . '/../backend/db_connect.php';  
// Load model
require_once __DIR__ . '/../backend/models/categoryModel.php';  
class CategoryController {

// Get ALL categories
public function index() {
$categoryModel = new CategoryModel();
 return $categoryModel->getAll();
}

// Get ONE category
public function show($id) {
$categoryModel = new CategoryModel();
return $categoryModel->getById($id);
}

// Create category
public function store($name) {
$categoryModel = new CategoryModel();
return $categoryModel->create($name);
}

// Update category
public function update($id, $name) {
$categoryModel = new CategoryModel();
return $categoryModel->update($id, $name);
}

// Delete category
public function destroy($id) {
$categoryModel = new CategoryModel();
return $categoryModel->delete($id);
}
}
?>

