<?php
include '../backend/config/db_connect.php';

function getUploadErrorMessage(int $errorCode): string {
    return match ($errorCode) {
        UPLOAD_ERR_INI_SIZE => 'File is larger than upload_max_filesize in php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'File is larger than the form MAX_FILE_SIZE limit.',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on server.',
        UPLOAD_ERR_CANT_WRITE => 'Server failed to write the file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.',
        default => 'Unknown upload error.'
    };
}

// Get the product ID and other form data
$id = intval($_POST['product_ID']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$category_id = intval($_POST['category_id']); // Assuming you are submitting category_id

// Get description (if any)
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

// Image handling
$image = '';
if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
    $uploadError = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        echo "Error uploading image: " . getUploadErrorMessage($uploadError);
        exit;
    }

    $target_dir = "../images/";
    if (!is_dir($target_dir) || !is_writable($target_dir)) {
        echo "Error uploading image: target directory is missing or not writable.";
        exit;
    }

    $originalName = basename($_FILES['image']['name']);
    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
    $image = uniqid('product_', true) . '_' . $safeName;
    $target_file = $target_dir . $image;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        echo "Error uploading image: failed to move uploaded file.";
        exit;
    }
} else {
    // If no new image is uploaded, keep the existing one.
    $image = mysqli_real_escape_string($conn, $_POST['current_image'] ?? '');

    if ($image === '') {
        $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM products WHERE product_ID=$id"));
        $image = $existing['image'] ?? '';
    }
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
