<!-- top line creates correct link to backend database connection -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../backend/config/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bedroom | LOFT & LIVING</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=10">

    <link rel="stylesheet" href="../css/category-css/livingroom-base.css">
    <link rel="stylesheet" href="../css/category-css/livingroom-structure.css">
    <link rel="stylesheet" href="../css/category-css/livingroom-reusable.css?v=4">
    <link rel="stylesheet" href="../css/category-css/livingroom-page.css">

    <style>
        /* Fixed Header Pill Style */
        body {
            padding-top: 120px;
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

        .site-footer {
            position: relative;
            background: white;
            border-top: 1px solid #e0e0e0;
            margin-top: 60px;
        }

        .header-left-tools {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .logo-wrapper {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .main-logo {
            height: 50px !important;
            width: auto !important;
            max-width: 280px;
            object-fit: contain;
            display: block;
            filter: invert(1);
            opacity: 0.95;
            transition: opacity 0.2s ease;
        }

        .main-logo:hover {
            opacity: 1;
        }

        .ui-icon {
            width: 20px;
            height: 20px;
            object-fit: contain;
            display: block;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .profile-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            z-index: 2000;
        }

        .profile-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
        }

        .profile-dropdown {
            position: absolute;
            top: 40px;
            right: 0;
            width: 260px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 18px;
            display: none;
            z-index: 3000;
            font-family: "Ibarra Real Nova", serif;
        }

        .profile-dropdown.open { display: block; }

        .profile-welcome {
            font-size: 14px;
            font-weight: 700;
            color: #2B2B2B;
            margin-bottom: 14px;
            font-family: "Ibarra Real Nova", serif !important;
        }

        .profile-link {
            display: block;
            font-size: 14px;
            color: #2B2B2B;
            padding: 10px 0;
            text-decoration: none;
            font-family: "Ibarra Real Nova", serif !important;
        }

        .profile-link + .profile-link {
            border-top: 1px solid #E5E1DB;
        }

        /* Dark mode overrides */
        html.dark-mode .site-header {
            background-color: #1a1a1a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        html.dark-mode .site-footer {
            background-color: #1a1a1a;
            border-top: 1px solid #333;
        }

        html.dark-mode .profile-dropdown {
            background-color: #242424 !important;
            border-color: #333 !important;
        }

        html.dark-mode .profile-welcome {
            color: #e0e0e0 !important;
        }

        html.dark-mode .profile-link {
            color: #e0e0e0 !important;
        }

        html.dark-mode .profile-link + .profile-link {
            border-top: 1px solid #444 !important;
        }

        html.dark-mode .ui-icon {
            filter: invert(1);
        }

        html.dark-mode .main-logo {
            filter: invert(0);
        }

        html.dark-mode .social-icon {
            filter: invert(1);
        }

        .social-icon {
            filter: invert(1) brightness(0.8) !important;
        }
    </style>
    <script src="../javascript/dark-mode.js"></script>
</head>
<body data-category="bedroom">

    <header class="site-header">
        <div class="header-inner">
            <div class="header-left-tools">
                <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
                    <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
                </button>
                <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
                <a class="mini-search" href="search.php" aria-label="Search">
                    <img src="../images/header_footer_images/icon-search.png" alt="Search" class="ui-icon" id="search-icon" style="vertical-align: middle;">
                </a>
            </div>

            <div class="logo-wrapper">
                <a href="homepage.php">
                    <img src="../images/header_footer_images/logo1.png" alt="LOFT &amp; LIVING" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.php">
                    <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
                </a>

                <div class="profile-wrapper" id="profile-wrapper">
                    <button class="profile-btn" id="profile-toggle-btn" type="button" aria-haspopup="true" aria-expanded="false">
                        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                    </button>

                    <div class="profile-dropdown" id="profile-dropdown">
                        <?php
                            if (session_status() === PHP_SESSION_NONE) session_start();
                            $isLoggedIn = !empty($_SESSION['user_ID']);
                            $headerName = $_SESSION['name'] ?? 'Guest';
                        ?>

                        <?php if ($isLoggedIn): ?>
                            <div class="profile-welcome">Welcome, <?php echo htmlspecialchars($headerName); ?></div>
                        <?php else: ?>
                            <div class="profile-welcome">Welcome to Loft & Living</div>
                        <?php endif; ?>

                        <?php if (!$isLoggedIn): ?>
                            <a class="profile-link" href="signin.php">Sign in</a>
                            <a class="profile-link" href="signup.php">Sign Up</a>
                        <?php endif; ?>

                        <a class="profile-link" href="user_dash.php">My account</a>

                        <?php if ($isLoggedIn): ?>
                            <a class="profile-link" href="user_order_history.php">My Orders</a>
                            <a class="profile-link" href="signout.php">Sign out</a>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="basket.php" class="basket-icon">
                    <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
                    <span id="basket-count">0</span>
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
    
    <div style="max-width: 800px; margin: 40px auto 50px; text-align: center; padding: 0 40px;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 600; margin-bottom: 20px; color: #2B2B2B; letter-spacing: 1px;">BEDROOM</h1>
        <p style="font-family: 'Ibarra Real Nova', serif; font-size: 16px; line-height: 1.7; color: #2B2B2B; font-weight: 400;">Create your dream bedroom sanctuary with our thoughtfully curated collection. From luxurious bedding and comfortable mattresses to elegant wardrobes and stylish nightstands, discover everything you need for the perfect night's rest.</p>
    </div>

    <div class="content-wrap">
        <aside class="side-bar">
            <h3>Keywords</h3>
            <div class="tags">
                <input type="checkbox" id="cosy-tag" value="cosy" hidden>
                <label for="cosy-tag" class="tag">Cosy<span class="X">✕</span></label>
                <input type="checkbox" id="modern-tag" value="modern" hidden>
                <label for="modern-tag" class="tag">Modern<span class="X">✕</span></label>
                <input type="checkbox" id="luxury-tag" value="luxury" hidden>
                <label for="luxury-tag" class="tag">Luxury<span class="X">✕</span></label>
            </div>

            <h3>Categories</h3>
            <label><input type="checkbox" class="category-filter" value="beds"> Beds</label>
            <label><input type="checkbox" class="category-filter" value="mattresses"> Mattresses</label>
            <label><input type="checkbox" class="category-filter" value="bedding"> Bedding</label>
            <label><input type="checkbox" class="category-filter" value="wardrobes"> Wardrobes</label>
            <label><input type="checkbox" class="category-filter" value="nightstands"> Nightstands</label>

            <h3>Price</h3>
            <div class="price-wrap">
                <label for="price" id="price-num">£0-295</label>
                <div class="price-slider">
                    <input type="range" id="price-min" min="0" max="295" value="0">
                    <input type="range" id="price-max" min="0" max="295" value="295">
                    <div class="track"></div>
                    <div class="range" id="range-display"></div>
                </div>
            </div>

            <h3>Colour</h3>
            <label><input type="checkbox" class="colour-filter" value="white"> White</label>
            <label><input type="checkbox" class="colour-filter" value="beige"> Beige</label>
            <label><input type="checkbox" class="colour-filter" value="grey"> Grey</label>
            <label><input type="checkbox" class="colour-filter" value="blue"> Blue</label>

            <h3>Size</h3>
            <label><input type="checkbox" class="size-filter" value="single"> Single</label>
            <label><input type="checkbox" class="size-filter" value="double"> Double</label>
            <label><input type="checkbox" class="size-filter" value="king"> King</label>
        </aside>

        <div class="main">
            <div class="top-bar">
                <input class="search" type="text" placeholder="Search..">
                <button class="btn-New button-sort">New <span class="X">✕</span></button>
                <button class="btn-PriceAsc button-sort">Price ascending <span class="X">✕</span></button>
                <button class="btn-PriceDesc button-sort">Price descending <span class="X">✕</span></button>
                <button class="btn-Rating button-sort">Rating <span class="X">✕</span></button>
            </div>

            <p id="no-results" style="display:none; font-size:20px; margin-top: 15px; padding-left: 48px; font-weight: 500;">
                Uh oh! No products matched your search.
            </p>

            <div class="product-grid" id="product-grid" style="display:grid!important;grid-template-columns:repeat(auto-fill,minmax(260px,1fr))!important;gap:24px!important;width:100%!important;">
                <?php
                $favouriteProductIds = [];
                if (!empty($_SESSION['user_ID'])) {
                    $favUserId = (int)$_SESSION['user_ID'];
                    $favIdsResult = mysqli_query($conn, "SELECT product_ID FROM favourites WHERE user_ID = {$favUserId}");
                    if ($favIdsResult instanceof mysqli_result) {
                        while ($favRow = mysqli_fetch_assoc($favIdsResult)) {
                            $favouriteProductIds[] = (int)$favRow['product_ID'];
                        }
                        mysqli_free_result($favIdsResult);
                    }
                }

                $query = "SELECT * FROM products WHERE category_id = 5";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $isFavourite = in_array((int)$row['product_ID'], $favouriteProductIds, true);
                        ?>
<div class="item" style="display:grid;grid-template-rows:280px auto 1fr;position:relative;"
     data-price="<?php echo $row['price']; ?>" 
     data-rating="<?php echo $row['rating']; ?>"     
     data-keywords="<?php echo $row['keywords']; ?>" 
     data-category="<?php echo $row['categories']; ?>"
     data-colour="<?php echo $row['colour']; ?>"
     data-new="<?php echo $row['is_new'] ? 'true' : 'false'; ?>"
     data-id="<?php echo $row['product_ID']; ?>">
     
     <a href="product.php?id=<?php echo $row['product_ID']; ?>" style="text-decoration: none; color: inherit; display: contents;">
         <img src="../images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="grid-row:1;width:100%;height:280px;object-fit:cover;">
         <h2 style="grid-row:2;"><?php echo $row['name']; ?></h2>
         <p style="grid-row:3;">£<?php echo $row['price']; ?></p>
     </a>

     <div style="position: absolute; top: 8px; left: 8px; z-index: 20;">
         <button class="fav-icon-btn<?= $isFavourite ? ' is-favourite' : '' ?>" data-product-id="<?= $row['product_ID'] ?>" title="Add to Favourites" onclick="toggleFavourite(this, event)">
             <span class="fav-heart" aria-hidden="true"><?= $isFavourite ? '♥' : '♡' ?></span>
         </button>
     </div>

     <button class="add-to-basket" onclick="addToBasket(<?= $row['product_ID'] ?>, 1, this)" title="Add to basket">
         <img src="../images/add-button-icon.png" alt="Add" style="width:18px!important;height:18px!important;">
     </button>
</div>
<?php
                    }
                } else {
                    echo "<p>No products found in the database.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <div id="basket-modal" class="basket-modal">
        <div class="basket-modal-content">
            <p>Item added to basket!</p>
            <div class="basket-modal-buttons">
                <button id="go-to-basket">Proceed to Basket</button>
                <button id="continue-shopping">Continue Shopping</button>
            </div>
        </div>
    </div>

    <footer class="site-footer"> 
        <div class="footer-inner">
            <div class="footer-section social-links">
                <a href="#">
                    <img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon">
                </a>
                <a href="#">
                    <img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon">
                </a>
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

  <script src="../javascript/header_footer_script.js"></script>
  <script>
    // Simple favourite toggle handler
    window.toggleFavourite = async function(button, event) {
        event.preventDefault();
        event.stopPropagation();
        
        const productId = button.getAttribute('data-product-id');
        const isFavourite = button.classList.contains('is-favourite');
        const heart = button.querySelector('.fav-heart');
        
        if (!productId) {
            alert('Error: Product ID not found');
            return;
        }
        
        try {
            const endpoint = isFavourite ? './favourite_remove.php' : './favourites_add.php';
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId + '&redirect=false'
            });
            
            if (response.ok) {
                if (isFavourite) {
                    button.classList.remove('is-favourite');
                    heart.textContent = '♡';
                    showToast('Removed from favourites');
                } else {
                    button.classList.add('is-favourite');
                    heart.textContent = '♥';
                    showToast('Added to favourites');
                }
            } else {
                alert('Error updating favourite: ' + response.status);
            }
        } catch (error) {
            console.error("Error:", error);
            alert('Error: ' + error.message);
        }
    };
    
    function showToast(message) {
        let toast = document.getElementById('fav-toggle-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'fav-toggle-toast';
            toast.style.cssText = 'position: fixed; top: 110px; right: 24px; background: rgba(33, 33, 33, 0.95); color: #fff; padding: 10px 14px; border-radius: 10px; font-size: 13px; z-index: 5000; box-shadow: 0 6px 16px rgba(0,0,0,0.2);';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 2000);
    }
  </script>
  <script src="../javascript/global/basketIcon.js"></script>
    <script type="module" src="../javascript/bedroom-js/filters.js"></script>
    <script type="module" src="../javascript/bedroom-js/sorting.js"></script>
    <script type="module" src="../javascript/bedroom-js/price-range.js"></script>
    <script type="module" src="../javascript/bedroom-js/search.js"></script>
</body>
</html>
