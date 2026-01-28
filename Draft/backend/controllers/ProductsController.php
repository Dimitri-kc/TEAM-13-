<?php

include_once 'backend/models/ProductsModel.php';

class ProductController {

// Get ALL products
public function index() {
$model = new ProductsModel();
return $model->getAll();
}

// Get ONE product
public function show($id) {
$model = new ProductsModel();
return $model->getById($id);
}

// Create product
public function store($name, $desc, $price, $stock, $category, $image) {
$model = new ProductsModel();
return $model->create($name, $desc, $price, $stock, $category, $image);
}

// Update product
public function update($id, $name, $desc, $price, $stock, $category, $image) {
$model = new ProductsModel();
return $model->update($id, $name, $desc, $price, $stock, $category, $image);
}

// Delete product
public function destroy($id) {
$model = new ProductsModel();
return $model->delete($id);
}
//Get product via category name
public function getByCategory($category) {
    $model = new ProductsModel();
    return $model ->getProductsByCategory($category);
}
}

?>
