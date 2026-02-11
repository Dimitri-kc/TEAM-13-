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
                <a href="Homepage.html">
                    <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.html">
                    <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
                </a>
                <a href="signin.html">
                    <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                </a>
                <a href="basket.html" class="basket-icon">
                    <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
                    <span id="basket-count">0</span>
                </a>
            </div>
        </div>

        <nav class="dropdown-panel" id="dropdown-nav">
            <ul class="nav-links">
                <li><a href="Categories.html">Living Room</a></li>
                <li><a href="bathroom.html">Bathroom</a></li>
                <li><a href="bedroom.html">Bedroom</a></li>
                <li><a href="office.html">Office</a></li>
                <li><a href="kitchen.html">Kitchen</a></li>
                <li class="nav-divider"><a href="login.html">My Account</a></li>
            </ul>
        </nav>
    </header>
    <a href="../html/homepage.html" style="text-decoration: none; color: inherit;">
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

            <div class="product-grid">
                <div class="item" data-keywords="modern smart bin" data-category="bins" data-colour="green"
                data-new="true" data-price="20" data-rating="4" onclick="location.href='../html/bin.html'">
                    <img src="../images/kitchen-images/bin.webp" alt="item 1"> 

                    <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
                    <div class="product-text">
                        <h2> Oval Stainless Steel Pedal Bin</h2>
                        <p> £20 </p>
                    </div>
                               <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
                </div>

                <div class="item" data-keywords="grey smart modern dining chairs" data-category="dining chairs" data-colour="beige"
                data-new="true" data-price="30" data-rating="3">
                    <img src="../images/kitchen-images/chairs.webp" alt="item 2">

                    <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
                    <div class="product-text">
                        <h2> Oakwood Set of 2 Dining Chairs</h2>
                        <p> £30 </p>
                    </div>
                               <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
                </div>

                <div class="item" data-keywords="gold cutlery" data-category="cutlery" data-colour="gold"
                data-new="true" data-price="20" data-rating="4">
                    <img src="../images/kitchen-images/cutlery.webp" alt="item 3">

                    <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
                    <div class="product-text">
                        <h2> Gold 18 Piece Cutlery Set</h2>
                        <p> £20 </p>
                    </div>
                               <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
                </div>

                <div class="item" data-keywords="modern pans" data-category="pans" data-colour="grey"
                data-new="true" data-price="50" data-rating="4">
                    <img src="../images/kitchen-images/pans.webp" alt="item 4" >

                    <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
                    <div class="product-text">
                        <h2> Stainless Steel 5 Piece Pan Set </h2>
                        <p> £50 </p>
                    </div>
                               <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
                </div>

                <div class="item" data-keywords="smart modern dining table" data-category="dining tables" data-colour="beige"
                data-new="false" data-price="150" data-rating="5">
                    <img src="../images/kitchen-images/table.webp" alt="item 5" >

                    <button class="fav-icon-btn" type="button" aria-label="Add to favourites">
        <img src="../images/header_footer_images/icon-heart.png" alt="">
    </button>
                    <div class="product-text">
                        <h2> Stone Dining Table </h2>
                        <p> £150 </p>
                    </div>
                               <!--Add to basket button -->
            <button class="add-to-basket" aria-label="Add to basket">
                    <img src="../images/add-button-icon.png" alt="Add to basket">
                </button>
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
                    <li><a href="Homepage.html">Homepage</a></li>
                    <li><a href="login.html">My Account</a></li>
                    <li><a href="favourites.html">Favourites</a></li>
                    <li><a href="Basket.html">Basket</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Categories</h4>
                <ul>
                    <li><a href="livi.html">Living Room</a></li>
                    <li><a href="office.html">Offices</a></li>
                    <li><a href="kitchen.html">Kitchen</a></li>
                    <li><a href="bathroom.html">Bathrooms</a></li>
                    <li><a href="bedroom.html">Bedrooms</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>More...</h4>
                <ul>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="About.html">About Us</a></li>
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