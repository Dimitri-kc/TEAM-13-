<?php
// categoryRoutes.php
include_once '../../controllers/CategoryController.php';

$categoryController = new CategoryController();

// --- GET Requests ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($action === 'index') {
// Fetch all categories
$categories = $categoryController->index(); 
        
} elseif ($action === 'show' && $id) {
// Fetch one category by ID
$category = $categoryController->show($id);
// print_r($category->fetch_assoc());
}

} 

// --- POST Requests (Create, Update, Delete Operations) ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
$action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : null;

if ($action === 'store' && $name) {
$categoryController->store($name); 
echo "Category created.";
    
} elseif ($action === 'update' && $id && $name) {
$categoryController->update($id, $name);
echo "Category updated.";
        
} elseif ($action === 'destroy' && $id) {
$categoryController->destroy($id);
echo "Category deleted.";
}

}
?>