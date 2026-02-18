<?php
include '../backend/config/db_connect.php';

$message = "";
$product_id = $_GET['product_id'] ?? null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    // If product review, validate product exists
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
        // Service review
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
            (user_ID, product_ID, rating, comment, review_date) 
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param("siis", $name, $product_id, $rating, $comment);

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
    <link rel="stylesheet" href="../css/header_footer_style.css">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Josefin+Slab:ital,wght@0,400;0,700;1,400;1,700&display=swap');



    /* <!-- font-family: 'Josefin Slab',serif;
  font-family: 'Ibarra Real Nova', serif; --> */
        .review-container {
            width: 540px;
            margin: 40px auto;
            background: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .review-container h2 {
            text-align: center;
            font-family:  'Ibarra Real Nova', serif;
            font-size: 1.7rem;
            margin-bottom: 15px;
        }
        .review-container form {
            display: flex;
            flex-direction: column;
        }
        .review-container input,
        .review-container textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .review-container textarea {
            resize: none;
            height: 120px;
        }
        .review-container button {
            padding: 10px;
            background-color: #2C2C2C;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .review-container button:hover {
            background-color: #1a5ec8;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <button class="menu-btn" id="menu-toggle-btn">
            <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon">
        </button>

        <div class="logo-wrapper">
            <a href="homepage.php">
                <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo">
            </a>
        </div>

        <div class="header-actions">
            <a href="favourites.php">
                <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
            </a>
            <a href="signin.php">
                <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
            </a>
            <a href="basket.php">
                <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
            </a>
        </div>
    </div>

    <nav class="dropdown-panel" id="dropdown-nav">
        <ul class="nav-links">
            <li><a href="livingroom.php">Living Room</a></li>
            <li><a href="bathroom.php">Bathroom</a></li>
            <li><a href="bedroom.php">Bedroom</a></li>
            <li><a href="office.php">Office</a></li>
            <li><a href="kitchen.php">Kitchen</a></li>
            <li class="nav-divider"><a href="signin.php">My Account</a></li>
        </ul>
    </nav>
</header>

<main style="padding: 50px; background-color: #d9d6cf;; min-height: 600px;">

    <div class="review-container">
        <h2>Leave a Review</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input 
                type="text" 
                name="name" 
                placeholder="Your Name" 
                maxlength="100"
                required
            >

            <input 
                type="number" 
                name="rating" 
                min="1" 
                max="5" 
                value="5"
                required
            >

            <textarea 
                name="comment" 
                placeholder="Write your review here..."
                maxlength="500"
                required
            ></textarea>

            <button type="submit">Submit Review</button>
        </form>
    </div>

</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-section social-links">
            <a href="#"><img src="../images/header_footer_images/icon-twitter.png" class="social-icon"></a>
            <a href="#"><img src="../images/header_footer_images/icon-instagram.png" class="social-icon"></a>
        </div>

        <div class="footer-section">
            <h4>Navigation</h4>
            <ul>
                <li><a href="homepage.php">Homepage</a></li>
                <li><a href="signin.php">My Account</a></li>
                <li><a href="favourites.php">Favourites</a></li>
                <li><a href="basket.php">Basket</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Categories</h4>
            <ul>
                <li><a href="livingroom.php">Living Room</a></li>
                <li><a href="office.php">Office</a></li>
                <li><a href="kitchen.php">Kitchen</a></li>
                <li><a href="bathroom.php">Bathroom</a></li>
                <li><a href="bedroom.php">Bedroom</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>More...</h4>
            <ul>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="about.php">About Us</a></li>
            </ul>
        </div>
    </div>
</footer>

<script src="../javascript/header_footer_script.js"></script>
</body>
</html>
