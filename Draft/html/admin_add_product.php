<?php include '../backend/config/db_connect.php'; ?>

<h1>Add New Product</h1>

<form method="POST" action="admin_insert_product.php" enctype="multipart/form-data">
    Name: <input type="text" name="name" required><br><br>
    Price: <input type="number" step="0.01" name="price" required><br><br>
    Stock: <input type="number" name="stock" required><br><br>
    Category ID: <input type="number" name="category_id" required><br><br>
    Image: <input type="file" name="image" required><br><br>
    <button type="submit">Add Product</button>
</form>