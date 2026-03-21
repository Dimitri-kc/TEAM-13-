<?php

require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

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

// Get form data
$name        = mysqli_real_escape_string($conn, $_POST['name']);
$price       = floatval($_POST['price']);
$stock       = intval($_POST['stock']);
$category_id = intval($_POST['category_id']);
$description = mysqli_real_escape_string($conn, $_POST['description']);

// Handle image upload
$image = '';
if(isset($_FILES['image']) && $_FILES['image']['name'] != '') {
    $uploadError = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        echo "Error uploading image: " . getUploadErrorMessage($uploadError);
        exit();
    }

    $targetDir = "../images/";
    if (!is_dir($targetDir) || !is_writable($targetDir)) {
        echo "Error uploading image: target directory is missing or not writable.";
        exit();
    }

    $originalName = basename($_FILES['image']['name']);
    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
    $image = uniqid('product_', true) . '_' . $safeName;
    $target = $targetDir . $image;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "Error uploading image: failed to move uploaded file.";
        exit();
    }
}

// Insert into database
$query = "INSERT INTO products (name, price, stock, category_id, image, description) 
          VALUES ('$name', $price, $stock, $category_id, '$image', '$description')";

if(mysqli_query($conn, $query)) {
    header("Location: admin_product_inventory.php");
    exit();
} else {
    echo "Error adding product: " . mysqli_error($conn);
}
?>