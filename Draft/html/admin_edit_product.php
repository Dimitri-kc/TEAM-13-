<?php
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

include '../backend/config/db_connect.php';

$id = intval($_GET['id'] ?? 0);
$query = "SELECT * FROM products WHERE product_ID = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Loft & Living</title>
    <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <link rel="stylesheet" href="../css/admin_edit_product.css?v=1">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body class="admin-edit-product-page">
    <?php $headerPartialOnly = true; include 'header.php'; ?>

    <main class="admin-wrapper">
        <div class="page-header">
            <div class="page-header-copy">
                <h1 class="title">Edit Product</h1>
                <p class="subtitle">Update product information, pricing, stock, category, and imagery while keeping the existing update route and product-save behavior unchanged.</p>
            </div>

            <div class="page-header-actions">
                <a href="admin_product_inventory.php" class="back-btn">Back to Inventory</a>
                <a href="product.php?id=<?= $product['product_ID'] ?>" class="view-product-btn">View Product Page</a>
            </div>
        </div>

        <div class="content-shell">
            <form id="editProductForm" class="admin-form" method="POST" action="admin_update_product.php" enctype="multipart/form-data">
                <input type="hidden" name="product_ID" value="<?= $product['product_ID'] ?>">
                <input type="hidden" name="current_image" value="<?= htmlspecialchars($product['image'] ?? '') ?>">

                <div class="form-grid">
                    <div class="field-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                    <div class="field-group">
                        <label for="price">Price (£)</label>
                        <input type="number" id="price" step="0.01" name="price" value="<?= $product['price'] ?>" required>
                    </div>

                    <div class="field-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?>" required>
                    </div>

                    <div class="field-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <option value="1" <?= $product['category_id']==1 ? 'selected' : '' ?>>Living Room</option>
                            <option value="2" <?= $product['category_id']==2 ? 'selected' : '' ?>>Kitchen</option>
                            <option value="3" <?= $product['category_id']==3 ? 'selected' : '' ?>>Office</option>
                            <option value="4" <?= $product['category_id']==4 ? 'selected' : '' ?>>Bathroom</option>
                            <option value="5" <?= $product['category_id']==5 ? 'selected' : '' ?>>Bedroom</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;">
                </div>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Update Product</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='admin_product_inventory.php'">Cancel</button>
                </div>
            </form>

            <aside class="image-panel">
                <h2 class="image-panel-title">Product Image</h2>
                <p class="image-panel-copy">Preview the current product image and replace it if needed before saving your changes.</p>

                <div class="image-preview-wrapper">
                    <div class="image-frame">
                        <?php if ($product['image']): ?>
                            <img id="currentImage" src="../images/<?= htmlspecialchars($product['image']) ?>" alt="Current Image">
                        <?php else: ?>
                            <div class="no-image">No Image</div>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="changeImageBtn" class="change-image-btn">Change Image</button>
                </div>
            </aside>
        </div>
    </main>

    <?php $footerPartialOnly = true; include 'footer.php'; ?>

    <script>
        const changeBtn = document.getElementById('changeImageBtn');
        const imageInput = document.getElementById('imageInput');

        changeBtn?.addEventListener('click', () => imageInput.click());

        imageInput?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const img = document.getElementById('currentImage');
                if (img) {
                    img.src = URL.createObjectURL(file);
                    return;
                }

                const frame = document.querySelector('.image-frame');
                if (frame) {
                    frame.innerHTML = `<img id="currentImage" src="${URL.createObjectURL(file)}" alt="New Product Image">`;
                }
            }
        });
    </script>
</body>
</html>
