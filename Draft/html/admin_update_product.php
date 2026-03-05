<?php
include '../backend/config/db_connect.php';

// Get the product ID and other form data
$id = intval($_POST['product_ID']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$category_id = intval($_POST['category_id']); // Assuming you are submitting category_id

// Get description (if any)
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

// Image handling
$image = $_FILES['image']['name']; // Get image name from the file input

if ($image) {
    // New image is uploaded, move it to the target directory
    $target_dir = "../images/";
    $target_file = $target_dir . basename($image);
    
    // Make sure the image doesn't already exist, or rename it
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Image successfully uploaded, proceed
    } else {
        echo "Error uploading image.";
        exit;
    }
} else {
    // If no new image is uploaded, keep the existing one
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM products WHERE product_ID=$id"));
    $image = $existing['image'];
}

// Update query
$query = "UPDATE products 
          SET name='$name', price=$price, stock=$stock, 
              category_id=$category_id, image='$image', description='$description'
          WHERE product_ID=$id";

if (mysqli_query($conn, $query)) {
    // Redirect to the inventory page after successful update
    header("Location: admin_product_inventory.php");
    exit;
} else {
    // If there is an error, display it
    echo "Error updating product: " . mysqli_error($conn);
}
?>