<?php
include '../backend/config/db_connect.php';

$id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE product_ID = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>
<!-- 
<div class="admin-wrapper">
    <h1>Edit Product</h1>
    <p class="subtitle">Update the product information below</p>

    <form method="POST" action="admin_update_product.php">
        <input type="hidden" name="product_ID" value="<?= $product['product_ID'] ?>">

        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" value="<?= $product['name'] ?>">

        <label for="price">Price (£):</label>
        <input type="number" id="price" name="price" value="<?= $product['price'] ?>">

        <label for="stock">Stock Quantity:</label>
        <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?>">

        <button type="submit">Update Product</button>
    </form>
</div>
<style>
    /* General Page Layout */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 0;
}

.admin-wrapper {
    max-width: 500px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h1 {
    font-size: 28px;
    color: #333;
    margin-bottom: 10px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Labels & Inputs */
form label {
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
}

form input[type="text"],
form input[type="number"] {
    padding: 10px 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    transition: 0.2s ease;
}

form input[type="text"]:focus,
form input[type="number"]:focus {
    outline: none;
    border-color: #888;
    box-shadow: 0 0 5px rgba(136,136,136,0.3);
}

/* Buttons */
button[type="submit"] {
    background-color: #333;
    color: #fff;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.2s ease;
}

button[type="submit"]:hover {
    background-color: #555;
}

/* Small description text */
.subtitle {
    font-size: 14px;
    color: #777;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 650px) {
    .admin-wrapper {
        width: 90%;
        padding: 20px;
    }

    form input[type="text"],
    form input[type="number"] {
        font-size: 14px;
    }

    button[type="submit"] {
        font-size: 14px;
        padding: 10px;
    }
}
</style> -->

<div class="admin-wrapper">
    <h1 class="title">Edit Product</h1>
    <p class="subtitle">Update the product information below</p>

    <form class="admin-form" method="POST" action="admin_update_product.php" enctype="multipart/form-data">
        <input type="hidden" name="product_ID" value="<?= $product['product_ID'] ?>">

        <label>Name:</label>
        <input type="text" name="name" value="<?= $product['name'] ?>" required>

        <label>Price (£):</label>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" required>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <option value="1" <?= $product['category_id']==1 ? 'selected' : '' ?>>Living Room</option>
            <option value="2" <?= $product['category_id']==2 ? 'selected' : '' ?>>Kitchen</option>
            <option value="3" <?= $product['category_id']==3 ? 'selected' : '' ?>>Office</option>
            <option value="4" <?= $product['category_id']==4 ? 'selected' : '' ?>>Bathroom</option>
            <option value="5" <?= $product['category_id']==5 ? 'selected' : '' ?>>Bedroom</option>
        </select>

        <label>Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="submit-btn">Update Product</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='admin_product_inventory.php'">
    Cancel</button>
    </form>
</div>

<style>
/* Wrapper */
.admin-wrapper {
    max-width: 600px;
    margin: 50px auto;
    padding: 25px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}

/* Titles */
.title {
    font-size: 24px;
    margin-bottom: 5px;
    color: #333;
}
.subtitle {
    font-size: 14px;
    color: #777;
    margin-bottom: 20px;
}

/* Form */
.admin-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.admin-form label {
    font-weight: 600;
    color: #555;
}

/* Inputs and select */
.admin-form input[type="text"],
.admin-form input[type="number"],
.admin-form select,
.admin-form input[type="file"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 100%;
    box-sizing: border-box;
    transition: border 0.2s ease, box-shadow 0.2s ease;
}
.admin-form input:focus,
.admin-form select:focus {
    border-color: #259131;
    box-shadow: 0 0 5px rgba(37,145,49,0.3);
    outline: none;
}

/* Submit Button */
.submit-btn {
    background: #2c2c2c;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}
.submit-btn:hover {
    background: #259131;
}
.cancel-btn {
    color: #333;
    border: none;
    padding: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}

.cancel-btn:hover {
    background: #d0d0d0;
}
</style>