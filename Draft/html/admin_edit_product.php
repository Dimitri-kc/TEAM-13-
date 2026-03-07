<?php
include '../backend/config/db_connect.php';
include "header.php";

$id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE product_ID = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<div class="admin-wrapper">
    <h1 class="title">Edit Product</h1>
    <p class="subtitle">Update the product information below</p>

    <form class="admin-form" method="POST" action="admin_update_product.php" enctype="multipart/form-data">
        <input type="hidden" name="product_ID" value="<?= $product['product_ID'] ?>">

        <div class="form-grid">
            <!-- Left Column: Product Details -->
            <div class="form-left">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

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

                <label>Description:</label>
                <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Update Product</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='admin_product_inventory.php'">Cancel</button>
                </div>
            </div>

            <!-- Right Column: Image Preview -->
            <div class="form-right">
                <label>Product Image:</label>
                <div class="image-preview-wrapper">
                    <?php if($product['image']): ?>
                        <img id="currentImage" src="../images/<?= htmlspecialchars($product['image']) ?>" alt="Current Image">
                    <?php else: ?>
                        <div class="no-image">No Image</div>
                    <?php endif; ?>
                    <button type="button" id="changeImageBtn" class="change-image-btn">Change Image</button>
                </div>
                <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;">
            </div>
        </div>
    </form>
</div>

<script>
    // Trigger file input
    const changeBtn = document.getElementById('changeImageBtn');
    const imageInput = document.getElementById('imageInput');
    changeBtn?.addEventListener('click', () => imageInput.click());

    // Preview new image
    imageInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if(file) {
            const img = document.getElementById('currentImage');
            img.src = URL.createObjectURL(file);
        }
    });
</script>

<?php include 'footer.php'; ?>
<style>
/* Wrapper */
.admin-wrapper {
    max-width: 900px;
    margin: 50px auto;
    padding: 30px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

/* Titles */
.title { font-size: 28px; margin-bottom: 5px; color: #333; }
.subtitle { font-size: 15px; color: #666; margin-bottom: 25px; }

/* Form grid */
.form-grid {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
}
.form-left {
    flex: 2;
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.form-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}

/* Inputs */
.admin-form input[type="text"],
.admin-form input[type="number"],
.admin-form select,
.admin-form textarea {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
}
.admin-form textarea { min-height: 120px; resize: vertical; }

/* Buttons */
.submit-btn {
    background: #5c5c5c;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}
.submit-btn:hover { background: #1f8438; }
.cancel-btn {
    background: #f0f0f0;
    color: #333;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}
.cancel-btn:hover { background: #ddd; }

/* Image Preview */
.image-preview-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.image-preview-wrapper img {
    max-width: 100%;
    border: 1px solid #ccc;
    border-radius: 6px;
    object-fit: contain;
}
.change-image-btn {
    background: #5c5c5c;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
}
.change-image-btn:hover { background: ##393939; }

/* Responsive */
@media (max-width: 768px) {
    .form-grid { flex-direction: column; }
}
</style>