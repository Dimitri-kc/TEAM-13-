<?php 
 include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bathroom | LOFT & LIVING</title>

  <!-- Header / Footer -->
  <link rel="stylesheet" href="../css/header_footer_style.css">

  <!-- Living Room CSS (REQUIRED – correct files) -->
  <link rel="stylesheet" href="../css/category-css/livingroom-base.css">
  <link rel="stylesheet" href="../css/category-css/livingroom-structure.css">
  <link rel="stylesheet" href="../css/category-css/livingroom-reusable.css">
  <link rel="stylesheet" href="../css/category-css/livingroom-page.css">
</head>

<body>

<header class="site-header">
  <div class="header-inner">
    <button class="menu-btn" id="menu-toggle-btn">
      <img src="../images/header_footer_images/icon-menu.png" class="ui-icon" alt="Menu">
    </button>

    <div class="logo-wrapper">
      <a href="homepage.php">
        <img src="../images/header_footer_images/logo.png" class="main-logo" alt="LOFT & LIVING">
      </a>
    </div>

    <div class="header-actions">
      <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" class="ui-icon"></a>
      <a href="signin.php"><img src="../images/header_footer_images/icon-user.png" class="ui-icon"></a>
      <a href="basket.php" class="basket-icon"><img src="../images/header_footer_images/icon-basket.png" class="ui-icon">
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
      <li class="nav-divider"><a href="login.php">My Account</a></li>
    </ul>
  </nav>
</header>

<h1 style="text-align:center; margin:20px 0;">BATHROOM</h1>

<div class="content-wrap">

  <!-- SIDEBAR -->
  <aside class="side-bar">

    <h3>Keywords</h3>
    <div class="tags">
      <input type="checkbox" id="grey-tag" hidden>
      <label for="grey-tag" class="tag">Grey <span class="X">✕</span></label>

      <input type="checkbox" id="modern-tag" hidden>
      <label for="modern-tag" class="tag">Modern <span class="X">✕</span></label>

      <input type="checkbox" id="soft-tag" hidden>
      <label for="soft-tag" class="tag">Soft <span class="X">✕</span></label>
    </div>

    <h3>Categories</h3>
    <label><input type="checkbox" class="category-filter" value="mirrors"> Mirrors</label>
    <label><input type="checkbox" class="category-filter" value="cabinets"> Cabinets</label>
    <label><input type="checkbox" class="category-filter" value="shower curtains"> Shower Curtains</label>
    <label><input type="checkbox" class="category-filter" value="towels"> Towels</label>
    <label><input type="checkbox" class="category-filter" value="bathmats"> Bathmats</label>

    <h3>Price</h3>
    <label id="price-num">£0 - 150</label>
    <div class="price-slider">
      <input type="range" id="price-min" min="0" max="150" value="0">
      <input type="range" id="price-max" min="0" max="150" value="150">
      <div class="track"></div>
      <div class="range" id="range-display"></div>
    </div>

    <h3>Colour</h3>
    <label><input type="checkbox" class="colour-filter" value="beige"> Beige</label>
    <label><input type="checkbox" class="colour-filter" value="grey"> Grey</label>
    <label><input type="checkbox" class="colour-filter" value="green"> Green</label>
    <label><input type="checkbox" class="colour-filter" value="gold"> Gold</label>
    <label><input type="checkbox" class="colour-filter" value="black"> Black</label>

    <h3>Size</h3>
    <label><input type="checkbox" class="size-filter" value="one-size"> ONE SIZE</label>

  </aside>

  <!-- MAIN -->
  <div class="main">

    <div class="top-bar">
      <input class="search" type="text" placeholder="Search..">
      <button class="button-sort btn-New">New</button>
      <button class="button-sort btn-PriceAsc">Price ascending</button>
      <button class="button-sort btn-PriceDesc">Price descending</button>
      <button class="button-sort btn-Rating">Rating</button>
    </div>

    <div class="product-grid">

      <div class="item" data-keywords="modern gold mirror" data-category="mirrors" data-colour="gold" data-price="35">
        <img src="../images/bathroom-images/mirror.png" alt="">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Round Wall Mirror</h2>
          <p>£35</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <div class="item" data-keywords="modern cabinet wood" data-category="cabinets" data-colour="beige" data-price="69">
        <img src="../images/bathroom-images/cabinets.png" alt="">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>  Oak Bathroom Cabinet</h2>
          <p>£69</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <div class="item" data-keywords="shower curtains black" data-category="shower curtains" data-colour="black" data-price="12">
        <img src="../images/bathroom-images/curtains.png" alt="">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>

        <div class="product-text">
          <h2>Grid Shower Curtains</h2>
          <p>£12</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <div class="item" data-keywords="soft towels green" data-category="towels" data-colour="green" data-price="18">
        <img src="../images/bathroom-images/towel.png" alt="">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Soft Cotton Towels Set</h2>
          <p>£18</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <div class="item" data-keywords="bathmat grey soft" data-category="bathmats" data-colour="grey" data-price="14">
        <img src="../images/bathroom-images/bathmat.png" alt="">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Textured Bathmat</h2>
          <p>£14</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

    </div>
  </div>
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
                    <li><a href="login.php">My Account</a></li>
                    <li><a href="favourites.php">Favourites</a></li>
                    <li><a href="Basket.php">Basket</a></li>
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