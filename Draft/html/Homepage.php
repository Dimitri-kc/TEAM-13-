<?php
session_start();
 include '../backend/config/db_connect.php';
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    
    <link rel="stylesheet" href="../css/header_footer_style.css">
    <link rel="stylesheet" href="../css/reviews.css">

    <link rel="stylesheet" href="../css/homepage-css/homepage.css">
    <link rel="stylesheet" href="../css/homepage-css/homepage-about.css">
    <link rel="stylesheet" href="../css/homepage-css/homepage-contact.css">
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
    <div class="row">
        <a class="category living" href="livingroom.php">
            <div class="title-box">LIVING ROOM</div> 
            <div class="image-box">
                <img src="../images/homepage-images/livingroom.png" alt="Living Room">
            </div>
        </a>

        <a class="category kitchen" href="kitchen.php">
            <div class="title-box">KITCHEN</div>
            <div class="image-box">
                <img src="../images/homepage-images/kitchen.png" alt="Kitchen">
            </div>
        </a>

        <a class="category office" href="office.php">
            <div class="title-box">OFFICE</div>
            <div class="image-box">
                <img src="../images/homepage-images/office_final.png" alt="Office">
            </div>
        </a>

        <a class="category bathroom" href="bathroom.php">
            <div class="title-box">BATHROOM</div>
            <div class="image-box">
                <img src="../images/homepage-images/bathroom.png" alt="Bathroom">
            </div>
        </a>

        <a class="category bedroom" href="bedroom.php">
            <div class="title-box">BEDROOM</div>
            <div class="image-box">
                <img src="../images/homepage-images/bedroom.png" alt="Bedroom">
            </div>
        </a>
    </div>

 <section class="reviews-section">
    <div class="reviews-header">
        <h2>Latest Reviews</h2>
        <button class="add-review-btn"></button>
    </div>

    <div class="reviews-slider-wrapper">
        <button class="nav-btn prev-btn" onclick="scrollReviews(-1)">&#10094;</button>

        <div class="reviews-container" id="reviewsContainer">

            <div class="review-card">
                <div class="stars">★★★★★</div>
                <h3>Love It.</h3>
                <p>Happy with the quality.</p>
                <div class="reviewer">
                    <img src="https://ui-avatars.com/api/?name=Bibi+Alaradi&background=random" alt="User">
                    <div>
                        <span class="name">Bibi Alaradi</span>
                        <span class="date">1 November 2025</span>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <div class="stars">★★★★★</div>
                <h3>The Beige Colour is Beautiful</h3>
                <p>An excellent pop of colour.</p>
                <div class="reviewer">
                    <img src="https://ui-avatars.com/api/?name=Amatullaah+S&background=random" alt="User">
                    <div>
                        <span class="name">Amatullaah Stevenson</span>
                        <span class="date">13 November 2025</span>
                    </div>
                </div>
            </div>

        </div>

        <button class="nav-btn next-btn" onclick="scrollReviews(1)">&#10095;</button>
    </div>
</section>

    <div class="split-page">
        <div class="left">
            <h1>ABOUT US</h1>
            <p>
            Welcome to Loft & Living, your new breakthrough in
            modern and contemporary living. We are building a brand
            dedicated to putting the customer first, offering products that
            showcase a fresh way of living without breaking the bank.
            Our goal is to help you transform houses into comfortable,
            calm, and inspiring homes. We meticulously select every
            item for both its practicality and beautiful design. From the
            living room to the bedroom, and home offices to kitchens, Loft
            & Living is here to help you create your dream space.
            Established in 2025, our commitment since day one has been
            to focus on exceptional design and quality while maintaining
            an affordable cost.
            </p>
        </div>

        <div class="right">
            <div class="image-place">
                <img src="../images/about-logo.png" alt="logo">
            </div>
        </div>
    </div>
    
    <header id="main-header">
        <h1 class="title">CONTACT US</h1>
    </header>

    <main>
        <section>
            <div class="form-container">
            <form id="contact-form" action="https://formspree.io/f/xzzlerol" method="POST">
                <input type="text" name="_gotcha" style="display: none;" />
            <!-- <form id="contact-form" onsubmit="return validateForm()"> -->
                <label for="first">Name<span class="required">*</span> </label>
                <input type="text" id="first" name="first" placeholder="First Name" required>
                
                <label for="last">Surname<span class="required">*</span></label>
                <input type="text" id="last" name="last" placeholder="Last Name" required>
                
                <label for="email">Email<span class="required">*</span></label>
                <input type="email" id="email" name="email" placeholder="Email Address" required>

                <label for="order">Order Number (If Applicable)</label>
                <input type="text" id="order" name="order" placeholder="Enter Order Number">

                <label for="message">Message<span class="required">*</span></label>
                <textarea id="message" name="message" placeholder="Enter message or enquiry" required></textarea>

                <button type="submit">Submit</button>
            </form>
            </div>
        </section>
    </main>

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
</body>
</html>

<!-- /* ========================= -->
   <!-- REVIEWS SECTION -->
<!-- ========================= */ -->

<style>

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: none;
}

.reviews-header h2 {
    font-size: 22px;
    font-weight: 700;     /* bold */
    text-transform: uppercase;  /* caps */
    letter-spacing: 1px;  /* optional: makes it look cleaner */
}

.add-review-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: transparent;      /* no background */
    background-image: url('../images/plus.png');
    background-size: 60%;               /* adjust icon size */
    background-repeat: no-repeat;
    background-position: center;
    border: none;                       /* remove border */
    cursor: pointer;
    padding: 0;
    position: relative;
}



/* Tooltip */
.add-review-btn::after {
    content: "Add Review";
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    background: black;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

/* Tooltip arrow */
.add-review-btn::before {
    content: "";
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    border-width: 6px;
    border-style: solid;
    border-color: black transparent transparent transparent;
    opacity: 0;
    transition: opacity 0.2s ease;
}

/* Show tooltip on hover */
.add-review-btn:hover::after,
.add-review-btn:hover::before {
    opacity: 1;
}


.add-review-btn:hover {
    background-color: #d3d3d3;
}

/* Slider Wrapper */
.reviews-slider-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;          /* ensures full width */
    overflow: visible;
    /* keeps cards inside frame */
}

/* Horizontal Scroll Container */
.reviews-container {
    display: flex;
    flex-wrap: nowrap;
    gap: 20px;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    padding: 10px 0;

    /* hide scrollbar */
    scrollbar-width: none;
}

.reviews-container::-webkit-scrollbar {
    display: none;
}

/* Review Card */
.review-card {
    min-width: 260px;
    max-width: 260px;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    flex-shrink: 0;       /* prevents collapsing */
    transition: transform 0.2s ease;
}

.review-card:hover {
    transform: translateY(-4px);
}

.stars {
    font-size: 28px;
    color: #373737;
    margin-bottom: 8px;
    background: linear-gradient(90deg, #555, #222);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.review-card h3 {
    margin: 8px 0;
    font-size: 16px;
}

.review-card p {
    font-size: 13px;
    color: #555;
    margin-bottom: 15px;
}

/* Reviewer */
.reviewer {
    display: flex;
    align-items: center;
    gap: 10px;
}

.reviewer img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
}

.name {
    display: block;
    font-weight: 600;
    font-size: 13px;
}

.date {
    font-size: 11px;
    color: #777;
}

/* Arrows */
.nav-btn {
    position: absolute;
    top: 45%;
    transform: translateY(-50%);
    background: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    cursor: pointer;
    font-size: 16px;
    z-index: 2;
}

.nav-btn:hover {
    background: black;
    color: white;
}

.prev-btn {
    left: 5px;     /* inside the container */
}

.next-btn {
    right: 5px;    /* inside the container */
}


.reviews-section {
    background-color: #B6B6B6;
    padding: 25px 30px;
    margin: 90px auto 90px auto;   /* top, left/right, bottom */
    border-radius: 25px;
    max-width: 1100px;
    width: 100%;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
}



/* Mobile */
@media (max-width: 768px) {
    .review-card {
        min-width: 220px;
        max-width: 220px;
    }



}
</style>