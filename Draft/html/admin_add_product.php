<?php include '../backend/config/db_connect.php';

require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

include "header.php";?>
<div class="admin-wrapper">
    <h1 class="title">Add New Product</h1>
    <p class="subtitle">Fill in the details below to add a new product</p>

<form class="admin-form" method="POST" action="admin_insert_product.php" enctype="multipart/form-data">
    
    <label>Name:</label>
    <input type="text" name="name" placeholder="Enter product name" required>

    <label>Price (£):</label>
    <input type="number" step="0.01" name="price" placeholder="0.00" required>

    <label>Stock:</label>
    <input type="number" name="stock" placeholder="Quantity in stock" required>

    <label>Category:</label>
    <select name="category_id" required>
        <option value="">-- Select Category --</option>
        <option value="1">Living Room</option>
        <option value="2">Kitchen</option>
        <option value="3">Office</option>
        <option value="4">Bathroom</option>
        <option value="5">Bedroom</option>
    </select>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <label>Description:</label>
    <textarea name="description" placeholder="Enter product description" required></textarea>

    <button type="submit" class="submit-btn">Add Product</button>
    <button type="button" class="cancel-btn" onclick="window.location.href='admin_product_inventory.php'">
        Cancel
    </button>
</form>
</div>

<?php include 'footer.php'; ?>

<style>
/* Center wrapper like a card, smaller than before */
.admin-wrapper {
    max-width: 500px;          /* smaller width */
    margin: 50px auto;         /* vertical spacing + center */
    padding: 30px 25px;        /* inner padding */
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* Titles */
.title {
    font-size: 22px;
    margin-bottom: 8px;
    color: #333;
    text-align: center;       /* centered title */
}

.subtitle {
    font-size: 14px;
    color: #777;
    margin-bottom: 25px;
    text-align: center;       /* centered subtitle */
}

/* Form styling */
.admin-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.admin-form label {
    font-weight: 600;
    margin-bottom: 5px;
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

.admin-form input[type="text"]:focus,
.admin-form input[type="number"]:focus,
.admin-form select:focus,
.admin-form input[type="file"]:focus {
    border-color: #259131;
    box-shadow: 0 0 5px rgba(37, 145, 49, 0.3);
    outline: none;
}

.admin-form select {
    background: #fff;
    appearance: none;
    cursor: pointer;
}

/* File input smaller padding */
.admin-form input[type="file"] {
    padding: 6px;
    cursor: pointer;
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
    margin-top: 10px;
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