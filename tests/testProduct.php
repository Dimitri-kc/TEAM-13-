<?php // DEVELOPMENT TESTING ONLY - DO NOT DEPLOY THIS FILE
if ($_SERVER['HTTP_HOST'] !== 'localhost') {
    http_response_code(403);
    exit('Forbidden');
}
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

// 1. Test Get All Products
echo "All Products:\n";
print_r($controller->index());

// 2. Test Create Product
echo "Creating Product:\n";
$created = $controller->store("Test Product", "A sample product", 19.99, 10, 1, "test.jpg");
echo $created ? "Product Created!\n" : "Failed to create product\n";

// 3. Test Get One Product
echo "Get Product with ID 1:\n";
print_r($controller->show(1));

// 4. Test Update Product
echo "Updating Product ID 1:\n";
$updated = $controller->update(1, "Updated Product", "Updated description", 25.50, 5, 1, "updated.jpg");
echo $updated ? "Product Updated!\n" : "Failed to update product\n";

// 5. Test Get Products By Category
echo "Products in Category 1:\n";
print_r($controller->getByCategory(1));

// 6. Test Delete Product
echo "Deleting Product ID 1:\n";
$deleted = $controller->destroy(1);
echo $deleted ? "Product Deleted!\n" : "Failed to delete product\n";
?>