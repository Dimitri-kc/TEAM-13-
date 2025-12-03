<?php
// Product routes - handles HTTP requests for product management

// Start session if not already started 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Path to ProductController.php
include_once '../../controllers/ProductController.php'; 

$productController = new ProductController();

// Helper function to safely get integer IDs
function getProductId() {
// Check GET first, then POST for IDs
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
return $id !== null ? (int)$id : null;
}

// --- Handle GET Requests (Read/View Operations) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
$id = getProductId();

switch ($action) {
// Get all products 
case 'index': 
$products = $productController->index();
// Output or render view with $products data
break;

// Get a single product 
case 'show': 
if ($id) {
$product = $productController->show($id);
// Output or render view with $product data
} else {
echo "Error: Product ID is required to view details.";
}
break;
}
} 

// --- Handle POST Requests (Create, Update, Delete Operations) ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Assuming POST actions are administrative and should be secure
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : '';
// May be used for update/delete
$id = getProductId(); 

// Collect and sanitize common product data
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : null;
$desc = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
// Note: Price should be validated to ensure it's a valid decimal/float before model call
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0.00;
$stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
$image = isset($_POST['image']) ? htmlspecialchars($_POST['image']) : null;


switch ($action) {
// Create a new product 
case 'store': 
// Basic required fields check
if ($name && $price && $category_id) { 
$success = $productController->store($name, $desc, $price, $stock, $category_id, $image);
$message = $success ? "Product created successfully." : "Error creating product.";
echo $message;
} else {
echo "Error: Missing required fields for creation.";
}
break;

// Update an existing product 
case 'update': 
if ($id && $name && $price && $category_id) {
$success = $productController->update($id, $name, $desc, $price, $stock, $category_id, $image);
$message = $success ? "Product updated successfully." : "Error updating product.";
echo $message;
} else {
echo "Error: Missing required fields for update.";
}
break;

// Delete a product
case 'destroy': 
if ($id) {
$success = $productController->destroy($id);
$message = $success ? "Product deleted successfully." : "Error deleting product.";
echo $message;
} else {
echo "Error: Missing Product ID for deletion.";
}
break;

default:
echo "Invalid POST action.";
break;
}
}
?>