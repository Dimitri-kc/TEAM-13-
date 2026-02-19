<?php
include '../backend/config/db_connect.php';

$message = "";
$product_id = $_GET['product_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $category = $_POST['category'] ?? null;

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

    if (empty($name) || empty($comment) || empty($category) || empty($rating)) {
        $message = "Please fill in all fields.";
    } elseif ($rating < 1 || $rating > 5) {
        $message = "Rating must be between 1 and 5.";
    } elseif (strlen($comment) > 500) {
        $message = "Review must be under 500 characters.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO reviews 
            (user_name, product_ID, category, rating, comment, review_date) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("sisis", $name, $product_id, $category, $rating, $comment);

        if ($stmt->execute()) {
            $message = "Review submitted successfully!";
        } else {
            $message = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}

$reviews = $conn->query("SELECT * FROM reviews ORDER BY review_date DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave a Review | LOFT & LIVING</title>
    <link rel="stylesheet" href="../css/header_footer_style.css">
    <style>
        body { 
    font-family: Arial, sans-serif; 
    background: #d9d6cf; 
    color:#111; 
}

/* 🔹 Smaller Container */
.review-container {
    width: 340px;              /* smaller width */
    margin: 25px auto;
    background: white;
    padding: 18px;             /* less padding */
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.07);
}

.review-container h2 {
    text-align:center;
    font-size:1.2rem;          /* smaller title */
    margin-bottom:10px;
}

.review-container form {
    display:flex;
    flex-direction:column;
}

/* 🔹 Smaller Inputs */
.review-container input,
.review-container textarea,
.review-container select {
    width:100%;
    padding:6px;               /* reduced padding */
    margin-bottom:8px;         /* tighter spacing */
    font-size:12px;            /* smaller text */
    border:1px solid #ccc;
    border-radius:4px;
    box-sizing:border-box;
}

.review-container textarea {
    resize:none;
    height:70px;               /* shorter textarea */
}

/* 🔹 Smaller Button */
.review-container button {
    padding:6px;
    font-size:12px;
    background:#2C2C2C;
    color:white;
    border:none;
    cursor:pointer;
}

.review-container button:hover {
    background:#1a5ec8;
}
.review-container label {
    font-size: 14px;   /* change this size */
    margin-bottom: 5px;
}

.message {
    text-align:center;
    margin-bottom:8px;
    color:green;
    font-size:12px;
}

/* 🔹 Rating Row Compact */
.rating-row {
    display: flex;
    align-items: center;
    gap: 6px;                  /* smaller gap */
    margin-bottom: 8px;
}

.rating-row label {
    margin: 0;
    font-size: 14px;
}

/* 🔹 Smaller Stars */
.star-rating {
    display: flex;
    gap: 3px;
    cursor: pointer;
    font-size: 19px;           /* smaller stars */
    position: relative;
    top: -2px;
}

.star {
    color: #ccc;
    transition: color 0.2s ease;
}

.star.active {
    color: #2f2f2f;
}

/* 🔹 Reviews Section (if enabled later) */
.reviews-section {
    width: 340px;
    margin:20px auto;
}

.review-card {
    background:#fff;
    border:1px solid #ccc;
    padding:10px;
    margin-bottom:10px;
    border-radius:4px;
    font-size:12px;
}

.review-card small {
    color:#666;
}


    </style>
</head>
<body>

<header class="site-header">
    <!-- Header here -->
</header>

<main style="padding:40px 0; min-height:600px;">

    <div class="review-container">
        <h2>Leave a Review</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Select Category:</label>
            <select name="category" required>
                <option value="">-- Select Service Type --</option>
                <option value="delivery">Interior Design Consultation</option>
                <option value="installation">Returns</option>
                <option value="customer_support">Customer Support</option>
                <option value="product_quality">Product Quality</option>
                <option value="overall_experience">Overall Experience</option>
            </select>

            <input type="text" name="name" placeholder="Your Name" maxlength="100" required>

            <!-- ⭐ Star Rating -->
<div class="rating-row">
    <label>Rating:</label>

    <div class="star-rating">
        <input type="hidden" name="rating" id="rating-value" required>

        <span class="star" data-value="1">&#9733;</span>
        <span class="star" data-value="2">&#9733;</span>
        <span class="star" data-value="3">&#9733;</span>
        <span class="star" data-value="4">&#9733;</span>
        <span class="star" data-value="5">&#9733;</span>
    </div>
</div>


            <textarea name="comment" placeholder="Write your review here..." maxlength="500" required></textarea>

            <button type="submit">Submit Review</button>
        </form>
    </div>

    <!-- Reviews section remains untouched (commented as you had it) -->
    <!--
    <div class="reviews-section">
        <h3>Reviews</h3>
        ...
    </div>
    -->

</main>

<footer class="site-footer">
    <!-- Footer here -->
</footer>

<!-- ⭐ Star Rating Script -->
<script>
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating-value');

stars.forEach(star => {
    star.addEventListener('click', function () {
        const value = this.getAttribute('data-value');
        ratingInput.value = value;

        stars.forEach(s => {
            s.classList.remove('active');
            if (s.getAttribute('data-value') <= value) {
                s.classList.add('active');
            }
        });
    });
});
</script>

</body>
</html>
