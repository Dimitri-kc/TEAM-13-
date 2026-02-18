<?php 
include '../backend/config/db_connect.php'; 

// Get the ID from the URL link
$product_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;

// Fetch only the product that was clicked
$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

// If the product isn't found, go back to the living room
if (!$product) {
    header("Location: livingroom.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> | LOFT & LIVING</title>

    <link rel="stylesheet" href="../css/header_footer_style.css">
    <link rel="stylesheet" href="../css/sofa_style.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 

    <style>
        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh;
        }
        .site-header {
            z-index: 1000 !important;
            position: relative; 
            background-color: #fff;
        }
        main.container {
            margin-top: 20px; 
            flex: 1; 
            width: 100%;
            max-width: 1200px; 
            align-self: center; 
        }
        .site-footer {
            width: 100%;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header class="site-header">
        <div class="header-inner">
            <button class="menu-btn" id="menu-toggle-btn">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img"> 
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

    <main class="container">
        <section class="product-wrapper">
            <div class="product-image">
                <div class="wishlist-icon">
                    <i class="fa-regular fa-heart"></i>
                </div>
                <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            <div class="product-info">
                <h1><?php echo $product['name']; ?></h1>
                <div class="price">£<?php echo $product['price']; ?></div>
                <p class="tagline">Bold, Modern, Elegant</p>

                <div class="selectors">
                    <div class="select-group">
                        <label>Size</label>
                        <select>
                            <option>ONE SIZE</option>
                        </select>
                    </div>
                    <div class="select-group">
                        <label>Quantity</label>
                        <select>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                    </div>
                </div>

                <button class="add-to-basket" onclick="window.location.href='basket.php'">Add to Basket</button>

                <div class="description-box">
                    <div class="desc-header">
                        <span>About this Item</span>
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <p><?php echo $product['description']; ?></p>
                </div>
            </div>
        </section>

        <section class="reviews-section">
            <h2>Latest reviews</h2>
            <div class="reviews-slider-wrapper">
                <button class="nav-btn prev-btn" onclick="scrollReviews('left')"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="reviews-container" id="reviewsContainer">
                    <div class="review-card">
                        <div class="stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <h3>Love It.</h3>
                        <p>Happy with the quality.</p>
                        <div class="reviewer">
                            <img src="https://ui-avatars.com/api/?name=Bibi+Alaradi&background=random" alt="User">
                            <div>
                                <span class="name">Bibi Alaradi</span>
                                <span class="date">1st November 2025</span>
                            </div>
                        </div>
                    </div>
                    <div class="review-card">
                        <div class="stars">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <h3>The Beige Colour is Beautiful</h3>
                        <p>An excellent pop of colour.</p>
                        <div class="reviewer">
                            <img src="https://ui-avatars.com/api/?name=Amatullaah+S&background=random" alt="User">
                            <div>
                                <span class="name">Amatullaah Stevenson</span>
                                <span class="date">13th November 2025</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="nav-btn next-btn" onclick="scrollReviews('right')"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-section social-links">
                <a href="#"><img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon"></a>
                <a href="#"><img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon"></a>
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
                    <li><a href="office.php">Offices</a></li>
                    <li><a href="kitchen.php">Kitchen</a></li>
                    <li><a href="bathroom.php">Bathrooms</a></li>
                    <li><a href="bedroom.php">Bedrooms</a></li>
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

    <script src="../javascript/sofa_script.js"></script>
    <script src="../javascript/header_footer_script.js"></script>
</body>
</html>