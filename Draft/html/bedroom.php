<?php  include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bedroom | LOFT & LIVING</title>

  <!-- Header / Footer CSS -->
  <link rel="stylesheet" href="../css/header_footer_style.css">

  <!-- Living Room CSS (shared layout system) -->
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
      <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" class="ui-icon" alt="Favourites"></a>
      <a href="signin.php"><img src="../images/header_footer_images/icon-user.png" class="ui-icon" alt="My Account"></a>
      <a href="basket.php" class="basket-icon"><img src="../images/header_footer_images/icon-basket.png" class="ui-icon" alt="Basket">
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

<h1 style="text-align:center; margin:20px 0;">BEDROOM</h1>

<div class="content-wrap">

  <!-- SIDEBAR -->
  <aside class="side-bar">

    <h3>Keywords</h3>
    <div class="tags">
      <input type="checkbox" id="soft-tag" hidden>
      <label for="soft-tag" class="tag">Soft <span class="X">✕</span></label>

      <input type="checkbox" id="modern-tag" hidden>
      <label for="modern-tag" class="tag">Modern <span class="X">✕</span></label>

      <input type="checkbox" id="comfort-tag" hidden>
      <label for="comfort-tag" class="tag">Comfort <span class="X">✕</span></label>
    </div>

    <h3>Categories</h3>
    <label><input type="checkbox" class="category-filter" value="bed frames"> Bed Frames</label>
    <label><input type="checkbox" class="category-filter" value="mattresses"> Mattresses</label>
    <label><input type="checkbox" class="category-filter" value="duvets"> Duvets</label>
    <label><input type="checkbox" class="category-filter" value="pillows"> Pillows</label>
    <label><input type="checkbox" class="category-filter" value="side tables"> Side Tables</label>

    <h3>Price</h3>
    <label id="price-num">£0 - 500</label>
    <div class="price-slider">
      <input type="range" id="price-min" min="0" max="500" value="0">
      <input type="range" id="price-max" min="0" max="500" value="500">
      <div class="track"></div>
      <div class="range" id="range-display"></div>
    </div>

    <h3>Colour</h3>
    <label><input type="checkbox" class="colour-filter" value="beige"> Beige</label>
    <label><input type="checkbox" class="colour-filter" value="white"> White</label>
    <label><input type="checkbox" class="colour-filter" value="grey"> Grey</label>
    <label><input type="checkbox" class="colour-filter" value="wood"> Wood</label>

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

    <p id="no-results" style="display:none; font-size:20px; margin-top: 15px; padding-left: 48px; font-weight: 500;">
      Uh oh! No products matched your search.
    </p>

    <div class="product-grid">

      <!-- Bed Frame -->
      <div class="item"
           data-keywords="bed frame beige modern"
           data-category="bed frames"
           data-colour="beige"
           data-new="true"
           data-price="499"
           data-rating="5"
           onclick="location.href='../html/bed-frame.php'">
        <img src="../images/bedroom-images/bed-frame.png" alt="Upholstered Bed Frame">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Upholstered Bed Frame</h2>
          <p>£499</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <!-- Mattress -->
      <div class="item"
           data-keywords="mattress comfort sleep"
           data-category="mattresses"
           data-colour="white"
           data-new="true"
           data-price="269"
           data-rating="4"
           onclick="location.href='../html/mattress.php'">
        <img src="../images/bedroom-images/mattress.png" alt="Luxury Comfort Mattress">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Luxury Comfort Mattress</h2>
          <p>£269</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <!-- Duvet -->
      <div class="item"
           data-keywords="duvet soft bedding"
           data-category="duvets"
           data-colour="white"
           data-new="false"
           data-price="25"
           data-rating="4"
           onclick="location.href='../html/duvet.php'">
        <img src="../images/bedroom-images/duvet.png" alt="All-Season Duvet">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>All-Season Duvet</h2>
          <p>£25</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <!-- Pillow -->
      <div class="item"
           data-keywords="pillow comfort sleep"
           data-category="pillows"
           data-colour="white"
           data-new="false"
           data-price="20"
           data-rating="5"
           onclick="location.href='../html/pillow.php'">
        <img src="../images/bedroom-images/pillow.png" alt="Medium Support Pillow">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Medium Support Pillow</h2>
          <p>£20</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

      <!-- Side Table -->
      <div class="item"
           data-keywords="side table wood modern"
           data-category="side tables"
           data-colour="wood"
           data-new="false"
           data-price="79"
           data-rating="4"
           onclick="location.href='../html/side-table.php'">
        <img src="../images/bedroom-images/side-table.png" alt="Modern Side Table">

        <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
        <div class="product-text">
          <h2>Modern Side Table</h2>
          <p>£79</p>
        </div>
                   <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
      </div>

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
        <li><a href="login.php">My Account</a></li>
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

<!-- JS (same as living room) -->
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
