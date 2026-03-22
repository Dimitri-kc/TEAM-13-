<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

//REMOVED THIS
// require_once __DIR__ . '/../models/ProductModel.php';
// $productModel = new ProductModel();

// $category = isset($_GET['category']) ? (int)$_GET['category'] : null;

// if ($category) {
//     $products = $productModel->getProductsByCategory($category);
//     echo json_encode($products);
// } else {
//     echo json_encode(["error" => "Category ID missing"]);
// }


// Product routes - handles HTTP requests for product management
header("Content-Type: application/json");

// Start session if not already started 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Path to ProductController.php - added . __DIR__
require_once __DIR__ . '/../controllers/productController.php'; 
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
$category = isset($_GET['category']) ? (int)$_GET['category'] : null;
switch ($action) {
// Get all products 
case 'index': 
    echo json_encode($productController->index());
    exit;
// Output or render view with $products data
break;

case 'byCategory':
    if ($category > 0 ) {
        $products = $productController->getByCategory($category);
        echo json_encode($products);
        // echo json_encode($productController->getByCategory($category));
    } else {
        echo json_encode([]);
    }
    exit;

// Get a single product 
case 'show': 
if ($id) {
echo json_encode($productController->show($id));
exit;
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