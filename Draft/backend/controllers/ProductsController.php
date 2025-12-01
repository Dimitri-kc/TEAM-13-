<?php

include_once 'backend/models/productModel.php';

class ProductController {

// Get ALL products
public function index() {
$model = new ProductModel();
return $model->getAll();
}

// Get ONE product
public function show($id) {
$model = new ProductModel();
return $model->getById($id);
}

// Create product
public function store($name, $desc, $price, $stock, $category, $image) {
$model = new ProductModel();
return $model->create($name, $desc, $price, $stock, $category, $image);
}

// Update product
public function update($id, $name, $desc, $price, $stock, $category, $image) {
$model = new ProductModel();
return $model->update($id, $name, $desc, $price, $stock, $category, $image);
}

// Delete product
public function destroy($id) {
$model = new ProductModel();
return $model->delete($id);
}
}

?>
