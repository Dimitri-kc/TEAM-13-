<?php
include '../backend/config/db_connect.php';

$message = "";
$product_id = $_GET['product_id'] ?? null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $category = $_POST['category'] ?? null;
    $product_name = $_POST['product'] ?? null;

    // Optional: validate product exists if product_id is given
    if ($product_id) {
        $check = $conn->prepare("SELECT product_ID FROM products WHERE product_ID = ?");
        $check->bind_param("i", $product_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            die("Invalid product.");
        }
        $check->close();
    } else {
        $product_id = NULL;
    }

    // Validation
    if (empty($name) || empty($comment)) {
        $message = "Please fill in all fields.";
    } elseif ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (strlen($comment) > 500) {
        $message = "Review must be under 500 characters.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO reviews 
            (user_name, product_ID, category, product_name, rating, comment, review_date) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("sissis", $name, $product_id, $category, $product_name, $rating, $comment);

        if ($stmt->execute()) {
            $message = "Review submitted successfully!";
        } else {
            $message = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}

// Fetch all reviews
$reviews = $conn->query("SELECT * FROM reviews ORDER BY review_date DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave a Review | LOFT & LIVING</title>
    <link rel="stylesheet" href="../css/header_footer_style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #d9d6cf; color:#111; }
        .review-container {
            width: 540px; margin: 40px auto;
            background: white; padding: 35px; border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .review-container h2 { text-align:center; font-size:1.7rem; margin-bottom:15px; }
        .review-container form { display:flex; flex-direction:column; }
        .review-container input, .review-container textarea, select {
            width:100%; padding:10px; margin-bottom:15px; font-size:14px;
            border:1px solid #ccc; border-radius:4px; box-sizing:border-box;
        }
        .review-container textarea { resize:none; height:120px; }
        .review-container button { padding:10px; background:#2C2C2C; color:white; border:none; cursor:pointer; font-size:15px; }
        .review-container button:hover { background:#1a5ec8; }
        .message { text-align:center; margin-bottom:15px; color:green; }
        .review-card { background:#fff; border:1px solid #ccc; padding:15px; margin-bottom:15px; border-radius:6px; }
        .review-card small { color:#666; }
    </style>
</head>
<body>

<header class="site-header">
    <!-- LOFT & LIVING header HTML here (same as your previous code) -->
</header>

<main style="padding:50px; min-height:600px;">
    <div class="review-container">
        <h2>Leave a Review</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="category">Select Category:</label>
            <select name="category" id="category" required>
                <option value="">-- Select Category --</option>
                <option value="livingroom">Living Room</option>
                <option value="kitchen">Kitchen</option>
                <option value="bedroom">Bedroom</option>
                <option value="bathroom">Bathroom</option>
                <option value="office">Office</option>
            </select>

            <label for="product">Select Item:</label>
            <select name="product" id="product">
                <option value="">-- Select Item --</option>
            </select>

            <input type="text" name="name" placeholder="Your Name" maxlength="100" required>
            <input type="number" name="rating" min="1" max="5" value="5" required>
            <textarea name="comment" placeholder="Write your review here..." maxlength="500" required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <!-- Display all reviews -->
    <div style="width: 540px; margin:40px auto;">
        <h3>Reviews</h3>
        <?php if(empty($reviews)): ?>
            <p>No reviews yet. Be the first to leave one!</p>
        <?php else: ?>
            <?php foreach($reviews as $r): ?>
                <div class="review-card">
                    <strong><?php echo htmlspecialchars($r['user_name']); ?></strong> 
                    <span style="float:right;">Rating: <?php echo $r['rating']; ?>/5</span>
                    <p style="margin-top:8px;"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
                    <small>
                        <?php 
                            echo $r['category'] ? ucfirst($r['category']) : 'Service';
                            echo $r['product_name'] ? ' - '.$r['product_name'] : '';
                        ?>
                        | <?php echo date("M d, Y H:i", strtotime($r['review_date'])); ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer class="site-footer">
    <!-- LOFT & LIVING footer HTML here -->
</footer>

<script>
// Example products for each category
const productsByCategory = {
    livingroom: ["Modern Grey Sofa", "Velvet Throw Pillow", "Chunky Knit Throw Blanket"],
    kitchen: ["Stainless Steel Knife Set", "Non-stick Frying Pan", "Coffee Maker"],
    bedroom: ["Queen Bed Frame", "Memory Foam Mattress", "Nightstand Lamp"],
    bathroom: ["Bath Towel Set", "Shower Caddy", "Soap Dispenser"],
    office: ["Ergonomic Chair", "Standing Desk", "LED Desk Lamp"]
};

// Update products when category changes
const categorySelect = document.getElementById('category');
const productSelect = document.getElementById('product');

categorySelect.addEventListener('change', function() {
    const category = this.value;
    productSelect.innerHTML = '<option value="">-- Select Item --</option>';
    if(category && productsByCategory[category]) {
        productsByCategory[category].forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            productSelect.appendChild(option);
        });
        productSelect.disabled = false;
    } else {
        productSelect.disabled = true;
    }
});
</script>
</body>
</html>
