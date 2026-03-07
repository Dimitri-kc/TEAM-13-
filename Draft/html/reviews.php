<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../backend/config/db_connect.php';

$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Leave a Review | LOFT & LIVING</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/header_footer_style.css?v=12">
<link rel="stylesheet" href="../css/dark-mode.css?v=9">
<script src="../javascript/dark-mode.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    background: #d9d6cf;
    margin: 0;
    padding: 0;
    padding-top: 120px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.site-header {
    position: fixed;
    top: 20px;
    left: 40px;
    right: 40px;
    z-index: 1000;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-radius: 50px;
    height: 80px;
}
.header-inner {
    max-width: 1400px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 40px;
}
.header-left-tools { display: flex; align-items: center; gap: 25px; }
.logo-wrapper { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
.main-logo { height: 50px !important; width: auto !important; max-width: 280px; object-fit: contain; display: block; filter: invert(1); opacity: 0.95; }
.ui-icon { width: 20px; height: 20px; object-fit: contain; display: block; }
.header-actions { display: flex; align-items: center; gap: 25px; }
html.dark-mode .site-header { background-color: #1a1a1a; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); }
html.dark-mode .ui-icon { filter: invert(1); }
html.dark-mode .main-logo { filter: invert(0); }
html.dark-mode body { background-color: #1a1a1a; color: #e0e0e0; }
html.dark-mode .review-container { background-color: #242424; color: #e0e0e0; }
html.dark-mode .review-container input, html.dark-mode .review-container textarea, html.dark-mode .review-container select { background-color: #1a1a1a; border-color: #444; color: #e0e0e0; }

/* Compact Container */
.review-container {
    width: 340px;
    background: #fff;
    padding: 18px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.07);
}

/* Titles & messages */
.review-container h2 {
    text-align: center;
    font-size: 1.2rem;
    margin-bottom: 10px;
}
.message {
    text-align:center;
    margin-bottom:8px;
    color:green;
    font-size:12px;
}

/* Inputs */
.review-container input,
.review-container textarea,
.review-container select {
    width:100%;
    padding:6px;
    margin-bottom:8px;
    font-size:12px;
    border:1px solid #ccc;
    border-radius:4px;
    box-sizing:border-box;
}

.review-container textarea {
    resize:none;
    height:70px;
}

/* Labels */
.review-container label {
    font-size: 14px;
    margin-bottom: 5px;
    display:block;
}

/* Button */
.review-container button {
    padding:6px;
    font-size:12px;
    background:#2C2C2C;
    color:white;
    border:none;
    cursor:pointer;
    width: 100%;
}
.review-container button:hover {
    background:#1a5ec8;
}

/* Rating stars */
.rating-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}
.rating-row label {
    margin: 0;
    font-size: 14px;
}
.star-rating {
    display: flex;
    gap: 3px;
    cursor: pointer;
    font-size: 19px;
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
</style>
</head>
<body>

<div class="review-container">
    <h2>Leave a Review</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Select Service:</label>
        <select name="category" required>
            <option value="">-- Select Service Type --</option>
            <option value="delivery">Interior Design Consultation</option>
            <option value="installation">Returns</option>
            <option value="customer_support">Customer Support</option>
            <option value="product_quality">Product Quality</option>
            <option value="overall_experience">Overall Experience</option>
        </select>

        <input type="text" name="name" placeholder="Your Name" maxlength="100" required>

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
