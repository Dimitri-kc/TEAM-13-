<?php

include_once __DIR__ . '/../models/ProductModel.php';

class ProductController {
private $model;
public function __construct() {
    $this->model = new ProductModel();
}

// Get ALL products
public function index() {
return $this->model->getAll();
}

// Get ONE product
public function show($id) {
return $this ->model->getById($id);
}

// Create product
public function store($name, $desc, $price, $stock, $category_id, $image) {
return $this->model->create($name, $desc, $price, $stock, $category_id, $image);
}

// Update product
public function update($id, $name, $desc, $price, $stock, $category, $image) {
$model = new ProductModel();
return $model->update($id, $name, $desc, $price, $stock, $category, $image);
}

// Delete product
public function destroy($id) {
return $this->model->delete($id);
}
//Get product via category name
public function getByCategory($category) {
    return $this->model ->getProductsByCategory($category);
}
}

?>
