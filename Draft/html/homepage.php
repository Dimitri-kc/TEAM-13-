<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';

$showWelcomeToast = $isLoggedIn && empty($_SESSION['welcome_toast_shown']);
if ($showWelcomeToast) {
    $_SESSION['welcome_toast_shown'] = true; // only once per session
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/about.css">
    <link rel="stylesheet" href="../css/homepage-css/homepage-contact.css">
    <link rel="stylesheet" href="../css/homepage-css/homepage.css?v=21">
     <link rel="stylesheet" href="../css/reviews.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">

    <script src="../javascript/dark-mode.js"></script>
</head>

<script src="//code.tidio.co/39jephe3cplamvoahaopa21ssco3ywxf.js" async></script>

<body class="ll-homepage">

<header class="site-header">
    <div class="header-inner">

        <!-- LEFT: menu + muted search pill -->
        <div class="header-left-tools">
            <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
            </button>
            <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
            <!-- Replace search pill with search icon -->
            <a class="mini-search" href="search.php" aria-label="Search" data-search-trigger="modal">
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
                        $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
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

                    <?php if ($isAdmin): ?>
                        <a class="profile-link" href="admin_dash.php">Admin Dashboard</a>
                    <?php endif; ?>

                    <a class="profile-link" href="user_dash.php">My Account</a>

                    <?php if ($isLoggedIn): ?>
                        <a class="profile-link" href="user_order_history.php">My Orders</a>
                        <a class="profile-link" href="signout.php">Sign Out</a>
                        
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
        </ul>
    </nav>
</header>

<!-- HERO -->
<section class="homepage-hero" aria-label="Homepage hero">
    <div
        class="hero-media"
        id="hero-media"
        role="img"
        aria-label="Hero background"
        data-light-src="../images/homepage-images/light.jpg"
        data-dark-src="../images/homepage-images/dark-home.jpg"
        data-light-size="cover"
        data-dark-size="cover"
        data-light-position="center"
        data-dark-position="center"
    ></div>
</section>

<!-- CATEGORIES -->
<div class="category-row">
    <a class="category living" href="livingroom.php">
        <div class="title-box">LIVING ROOM</div>
        <div class="image-box">
            <img
                src="../images/homepage-images/livingroom.png?v=3"
                alt="Living Room"
                data-light-src="../images/homepage-images/livingroom.png?v=3"
                data-dark-src="../images/homepage-images/dark-living.png"
            >
        </div>
    </a>

    <a class="category kitchen" href="kitchen.php">
        <div class="title-box">KITCHEN</div>
        <div class="image-box">
            <img
                src="../images/homepage-images/kitchen.png?v=3"
                alt="Kitchen"
                data-light-src="../images/homepage-images/kitchen.png?v=3"
                data-dark-src="../images/homepage-images/dark-kitchen.png"
            >
        </div>
    </a>

    <a class="category office" href="office.php">
        <div class="title-box">OFFICE</div>
        <div class="image-box">
            <img
                src="../images/homepage-images/officefinal.png?v=3"
                alt="Office"
                data-light-src="../images/homepage-images/officefinal.png?v=3"
                data-dark-src="../images/homepage-images/dark-office.png"
            >
        </div>
    </a>

    <a class="category bathroom" href="bathroom.php">
        <div class="title-box">BATHROOM</div>
        <div class="image-box">
            <img
                src="../images/homepage-images/bathroom.png?v=3"
                alt="Bathroom"
                data-light-src="../images/homepage-images/bathroom.png?v=3"
                data-dark-src="../images/homepage-images/dark-bathroom.png"
            >
        </div>
    </a>

    <a class="category bedroom" href="bedroom.php">
        <div class="title-box">BEDROOM</div>
        <div class="image-box">
            <img
                src="../images/homepage-images/bedroom.png?v=3"
                alt="Bedroom"
                data-light-src="../images/homepage-images/bedroom.png?v=3"
                data-dark-src="../images/homepage-images/dark-bedroom.png"
            >
        </div>
    </a>
</div>

<section class="homepage-quote" aria-label="Brand quote">
    <p class="homepage-quote-text">"Modern Living Made Simple"</p>
    <p class="homepage-quote-caption">From timeless designs to everyday essentials, we make modern living effortless.</p>
</section>

<section class="grey-section">
    <div class="grey-inner">
        <h2>OUR FAVOURITES</h2>

        <div class="collection-cards">
            <div class="card">
                <a href="product.php?id=1">
                    <img src="../images/livingroom-images/sofa.jpg" alt="Sofa">
                    <h3>VENICE CREAM SOFA</h3>
                </a>
            </div>
            <div class="card">
                <a href="product.php?id=5">
                    <img src="../images/livingroom-images/consoletable.png" alt="Console Table">
                    <h3>NY CONSOLE TABLE</h3>
                </a>
            </div>
            <div class="card">
                <a href="product.php?id=2">
                    <img src="../images/livingroom-images/throwpillow3.jpg" alt="Throw Pillow">
                    <h3>OXFORD THROW PILLOW</h3>
                </a>
            </div>
            <div class="card">
                  <a href="product.php?id=4">
                    <img src="../images/livingroom-images/rug.png" alt="Faux Fur Rug">
                    <h3>FAUX FUR RUG</h3>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="reviews-section" style="position:relative;">
<div class="reviews-header">
    <h2>LATEST REVIEWS</h2>
    <button class="add-review-btn" type="button" aria-label="Add Review"></button>
</div>

    <div class="reviews-slider-wrapper">
        <button class="nav-btn prev-btn" type="button" onclick="scrollReviews(-1)">‹</button>
        <div class="reviews-container" id="reviewsContainer"></div>
        <button class="nav-btn next-btn" type="button" onclick="scrollReviews(1)">›</button>
    </div>
</section>

<div id="reviewModal" class="review-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Add a Review</h2>

        <form id="reviewForm">
            <label>Stars:</label>
            <div class="star-rating">
                <span data-value="1">★</span>
                <span data-value="2">★</span>
                <span data-value="3">★</span>
                <span data-value="4">★</span>
                <span data-value="5">★</span>
            </div>

            <input type="hidden" id="reviewStars" name="stars">

            <label>Title:</label>
            <input type="text" id="reviewTitle" required>

            <label>Review:</label>
<textarea id="reviewText" maxlength="200" required></textarea>
<small id="charCounter">0 / 200 characters</small>

            <label>Your Name:</label>
            <input type="text" id="reviewName" required>

            <button type="submit" class="submit-review-btn">Submit Review</button>
        </form>
    </div>
</div>

<!-- ABOUT & CONTACT -->
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
        <div class="about-logo-container">
            <img src="../images/homepage-images/aboutus.png" alt="Loft & Living Logo" class="about-logo">
        </div>
    </div>
    
    <div class="right">
        <h1>CONTACT US</h1>
        <div class="form-container">
            <form id="contact-form" action="https://formspree.io/f/xzzlerol" method="POST">
                <input type="text" name="_gotcha" style="display: none;" />

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
    </div>
</div>

<!-- FOOTER -->
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
<script src="../javascript/global/basketIcon.js"></script>
<script src="../javascript/global/search-modal.js"></script>

<script>
    // Review modal logic
    const modal = document.getElementById("reviewModal");
    const addBtn = document.querySelector(".add-review-btn");
    const closeModal = document.querySelector(".close-modal");

    if (addBtn) addBtn.onclick = () => modal.style.display = "block";
    if (closeModal) closeModal.onclick = () => modal.style.display = "none";
    modal.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });
    document.getElementById("reviewForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const stars = document.getElementById("reviewStars").value;
        const title = document.getElementById("reviewTitle").value;
        const text = document.getElementById("reviewText").value;


        const name = document.getElementById("reviewName").value;

        fetch("../html/submit_general_review.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `stars=${stars}&title=${encodeURIComponent(title)}&text=${encodeURIComponent(text)}&name=${encodeURIComponent(name)}`
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                loadReviewsFromDB();
                modal.style.display = "none";
                document.getElementById("reviewForm").reset();
                document.querySelectorAll(".star-rating span").forEach(s => s.classList.remove("active"));
            } else {
                alert("Error saving review");
            }
        });
    });

    function loadReviewsFromDB() {
        fetch("../html/get_general_reviews.php")
        .then(response => response.json())
        .then(reviews => {
            const container = document.getElementById("reviewsContainer");
            container.innerHTML = "";

            if (!reviews || reviews.length === 0) {
                container.innerHTML = `
                    <div class="no-reviews-message">
                        <p>No reviews yet.</p>
                        <p>Be the first to review this product!</p>
                    </div>
                `;
                return;
            }

            reviews.forEach(review => {
                const card = document.createElement("div");
                card.classList.add("review-card");

                const date = new Date(review.created_at).toLocaleDateString("en-GB", {
                    day: "numeric",
                    month: "long",
                    year: "numeric"
                });

                card.innerHTML = `
                    <div class="stars">${"★".repeat(review.stars)}</div>
                    <h3>${review.title}</h3>
                    <p>${review.review_text}</p>
                    <div class="reviewer">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(review.name)}&background=random" alt="User">
                        <div>
                            <span class="name">${review.name}</span>
                            <span class="date">${date}</span>
                        </div>
                    </div>
                `;

                container.appendChild(card);
            });
        });
    }

    loadReviewsFromDB();

    document.querySelectorAll(".star-rating span").forEach(star => {
        star.addEventListener("click", () => {
            const value = star.getAttribute("data-value");
            document.getElementById("reviewStars").value = value;

            document.querySelectorAll(".star-rating span").forEach(s => {
                s.classList.toggle("active", s.getAttribute("data-value") <= value);
            });
        });
    });

    function scrollReviews(direction) {
        const container = document.getElementById("reviewsContainer");
        const scrollAmount = 280;
        container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    }

    // Homepage hero/category image rotation (every 7s, with slower fade)
    (function startHomepageImageRotation() {
        const heroMedia = document.getElementById('hero-media');
        const categoryImages = document.querySelectorAll('.category-row .image-box img[data-light-src][data-dark-src]');

        if (!heroMedia || !categoryImages.length) {
            return;
        }

        const ROTATION_MS = 7000;
        const FADE_MS = 900;
        let showDark = false;

        heroMedia.style.transition = `opacity ${FADE_MS}ms ease-in-out`;
        categoryImages.forEach((imageElement) => {
            imageElement.style.transition = `opacity ${FADE_MS}ms ease-in-out`;
        });

        const fadeSwapHero = (nextSrc) => {
            heroMedia.style.opacity = '0';
            window.setTimeout(() => {
                heroMedia.style.backgroundImage = `url("${nextSrc}")`;
                heroMedia.style.opacity = '1';
            }, FADE_MS);
        };

        const fadeSwapImage = (imageElement, nextSrc) => {
            imageElement.style.opacity = '0';
            window.setTimeout(() => {
                imageElement.src = nextSrc;
                imageElement.style.opacity = '1';
            }, FADE_MS);
        };

        const applyImageSet = (useDarkSet) => {
            const heroSrc = useDarkSet
                ? heroMedia.dataset.darkSrc
                : heroMedia.dataset.lightSrc;

            const heroSize = useDarkSet
                ? (heroMedia.dataset.darkSize || 'cover')
                : (heroMedia.dataset.lightSize || 'cover');

            const heroPosition = useDarkSet
                ? (heroMedia.dataset.darkPosition || 'center')
                : (heroMedia.dataset.lightPosition || 'center');

            heroMedia.style.backgroundSize = heroSize;
            heroMedia.style.backgroundPosition = heroPosition;

            fadeSwapHero(heroSrc);

            categoryImages.forEach((imageElement) => {
                const nextSrc = useDarkSet
                    ? imageElement.dataset.darkSrc
                    : imageElement.dataset.lightSrc;

                fadeSwapImage(imageElement, nextSrc);
            });
        };

        heroMedia.style.backgroundImage = `url("${heroMedia.dataset.lightSrc}")`;
        heroMedia.style.backgroundSize = heroMedia.dataset.lightSize || 'cover';
        heroMedia.style.backgroundPosition = heroMedia.dataset.lightPosition || 'center';

        setInterval(() => {
            showDark = !showDark;
            applyImageSet(showDark);
        }, ROTATION_MS);
    })();
const reviewText = document.getElementById("reviewText");
const charCounter = document.getElementById("charCounter");
const MAX_CHARS = 200;

reviewText.addEventListener("input", function () {
    const count = reviewText.value.length;

    charCounter.textContent = `${count} / ${MAX_CHARS} characters`;

    if (count >= MAX_CHARS) {
        charCounter.style.color = "red";
    } else if (count >= 160) {
        charCounter.style.color = "orange";
    } else {
        charCounter.style.color = "";
    }
});
</script>

</body>
</html>