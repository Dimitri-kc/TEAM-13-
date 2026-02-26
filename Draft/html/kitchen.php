<!-- did the push work? -->
<?php include '../backend/config/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen | LOFT & LIVING</title>

    <link rel="stylesheet" href="../css/header_footer_style.css">

    <link rel="stylesheet" href="../css/category-css/livingroom-base.css">
    <link rel="stylesheet" href="../css/category-css/livingroom-structure.css">
    <link rel="stylesheet" href="../css/category-css/livingroom-reusable.css">
    <link rel="stylesheet" href="../css/category-css/livingroom-page.css">
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
    <a href="../html/homepage.php" style="text-decoration: none; color: inherit;">
        <h1 style="text-align: center; margin-top: 20px;">KITCHEN</h1>
    </a>

    <div class="content-wrap">
        
        <aside class="side-bar">

            <h3>Keywords</h3>
            <div class="tags">
                <input type="checkbox" id="grey-tag" value="grey" hidden>
                <label for="grey-tag" class="tag"> Grey<span class="X">✕</span></label>

                <input type="checkbox" id="smart-tag" value="smart" hidden>
                <label for="smart-tag" class="tag">Smart <span class="X">✕</span></label>

                <input type="checkbox" id="modern-tag" value="modern" hidden>
                <label for="modern-tag" class="tag">Modern <span class="X">✕</span></label>
            </div>

            <h3>Categories</h3>
            <label><input type="checkbox" class="category-filter" value="bins" > Bins </label>
            <label><input type="checkbox" class="category-filter" value="dining tables" > Dining Tables </label>
            <label><input type="checkbox" class="category-filter" value="dining chairs" > Dining Chairs</label>
            <label><input type="checkbox" class="category-filter" value="pans" > Pots and Pans</label>
            <label><input type="checkbox" class="category-filter" value="cutlery"> Cutlery </label>

            <h3> Price </h3>
            <div class="price-wrap">
                <label for="price" id="price-num">£0-150</label>
                <div class="price-slider">
                    <input type="range" id="price-min" min="0" max="150" value="0">
                    <input type="range" id="price-max" min="0" max="150" value="150">
                    <div class="track"></div>
                    <div class="range" id="range-display"></div>
                </div>
            </div>

            <h3> Colour </h3>
            <label><input type="checkbox" class="colour-filter" value="beige"> Beige </label>
            <label><input type="checkbox" class="colour-filter" value="grey" > Grey </label>
            <label><input type="checkbox" class="colour-filter" value="green" > Green </label>
            <label><input type="checkbox" class="colour-filter" value="gold" > Gold </label>

            <h3> Size </h3>
            <label><input type="checkbox" class="size-filter" value="one-size" > ONE SIZE </label>

        </aside>

        <div class="main">

            <div class="top-bar">
                <input class="search" type="text" placeholder="Search..">
                <button class="btn-New button-sort">New</button>
                <button class="btn-PriceAsc button-sort">Price ascending</button>
                <button class="btn-PriceDesc button-sort">Price descending</button>
                <button class="btn-Rating button-sort">Rating</button>
            </div>

            <p id="no-results" style="display:none; font-size:20px; margin-top: 15px; padding-left: 48px; font-weight: 500;">
                Uh oh! No products matched your search.
            </p>

            <!-- link added to connect to database -->
 <div class="product-grid" id="product-grid">
    <?php
    // category_id = 2 for Kitchen. 
    $query = "SELECT * FROM products WHERE category_id = 2";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="item" 
     data-price="<?php echo $row['price']; ?>" 
     data-rating="<?php echo $row['rating']; ?>"     
     data-keywords="<?php echo $row['keywords']; ?>" 
     data-category="<?php echo ($row['categories']); ?>"
     data-colour="<?php echo $row['colour']; ?>">


 <a href="product.php?id=<?php echo $row['product_ID']; ?>" style="text-decoration: none; color: inherit;">
                     <img src="../images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                     <div class="product-text">
                         <h2><?php echo $row['name']; ?></h2>
                         <p>£<?php echo $row['price']; ?></p>
                     </div>
                 </a>

<div class="action-buttons">
<form method="post" action="favourites_add.php" style="position: absolute; top: 15px; left: 15px; z-index: 999; margin: 0; padding: 0; pointer-events: auto;">
    <input type="hidden" name="product_id" value="<?= $row['product_ID'] ?>">
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
    <input type="hidden" name="product_price" value="<?= htmlspecialchars($row['price']) ?>">
    <input type="hidden" name="product_image" value="../images/<?= htmlspecialchars($row['image']) ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <button type="submit" title="Add to Favourites" style="background: rgb(217, 217, 222); border: none; border-radius: 60%; width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; cursor: pointer; font-size: 30px;">♡</button>
</form>

<form method="post" action="basket.php?action=add" style="position: absolute; bottom: 15px; right: 15px; z-index: 999; margin: 0; padding: 0; pointer-events: auto;">
    <input type="hidden" name="product_id" value="<?= $row['product_ID'] ?>">
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
    <input type="hidden" name="product_price" value="<?= htmlspecialchars($row['price']) ?>">
    <input type="hidden" name="product_image" value="../images/livingroom-images/<?= htmlspecialchars($row['image']) ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <button type="submit" title="Add to basket" style="background: rgba(0,0,0,0.08); border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; cursor: pointer; font-size: 30px;">+</button>
</form>
</div>


            </div>
            <?php
        }
    } else {
        echo "<p style='padding: 20px;'>No kitchen products found in the database.</p>";
    }
    ?>
</div>


        </div> </div> <footer class="site-footer">
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
    <script type="module" src="../javascript/livingroom-js/main.js"></script>
    <script src="../javascript/header_footer_script.js"></script>
    <script src="../javascript/global/basketIcon.js"></script>

    <!-- Basket -->
    <div id="basket-modal" class="basket-modal">
    <div class="basket-modal-content">
        <p>Item added to basket!</p>
        <div class="basket-modal-buttons">
            <button id="go-to-basket">Proceed to Basket</button>
            <button id="continue-shopping">Continue Shopping</button>

        </div>
    </div>
</div>
</body>
</html>