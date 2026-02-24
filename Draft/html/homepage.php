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
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css">

    <link rel="stylesheet" href="../css/homepage-css/homepage.css">
    <link rel="stylesheet" href="../css/about.css">
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

<!-- New Grey Container Section -->
<section class="grey-section">
    <div class="grey-inner">
        <h2>OUR FAVOURITES</h2>

        <div class="collection-cards">
            <div class="card">
                <a href="product.php?id=1"> <!-- Replace 1 with actual product_id of sofa -->
                    <img src="../images/livingroom-images/sofa.jpg" alt="Sofa">
                    <h3>VENICE CREAM SOFA</h3>
                </a>
            </div>
            <div class="card">
                <a href="product.php?id=5"> <!-- Replace 2 with actual product_id of console table -->
                    <img src="../images/livingroom-images/consoletable.png" alt="Console Table">
                    <h3>NY CONSOLE TABLE</h3>
                </a>
            </div>
            <div class="card">
                <a href="product.php?id=2"> <!-- Replace 3 with actual product_id of throw pillow -->
                    <img src="../images/livingroom-images/throwpillow3.jpg" alt="Throw Pillow">
                    <h3>OXFORD THROW PILLOW</h3>
                </a>
            </div>
        </div>
    </div>
</section>

 <section class="reviews-section">
    <div class="reviews-header">
        <h2>LATEST REVIEWS</h2>
        <button class="add-review-btn"></button>
    </div>

    <div class="reviews-slider-wrapper">
        <button class="nav-btn prev-btn" onclick="scrollReviews(-1)">‹</button>

        <div class="reviews-container" id="reviewsContainer"></div>
        <button class="nav-btn next-btn" onclick="scrollReviews(1)">›</button>
    </div>
</section>

<!-- Add Review Modal -->
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
            <textarea id="reviewText" required></textarea>

            <label>Your Name:</label>
            <input type="text" id="reviewName" required>

            <button type="submit" class="submit-review-btn">Submit Review</button>
        </form>
    </div>
</div>

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
    <img src="../images/about-logo.png" alt="logo">
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
    

<script>
// ---------- Modal Controls ----------
const modal = document.getElementById("reviewModal");
const addBtn = document.querySelector(".add-review-btn");
const closeModal = document.querySelector(".close-modal");

addBtn.onclick = () => modal.style.display = "block";
closeModal.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };


document.getElementById("reviewForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const stars = document.getElementById("reviewStars").value;
    const title = document.getElementById("reviewTitle").value;
    const text = document.getElementById("reviewText").value;
    const name = document.getElementById("reviewName").value;

    fetch("../html/submit_review.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
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
    fetch("../html/get_reviews.php")
    .then(response => response.json())
    .then(reviews => {
        const container = document.getElementById("reviewsContainer");
        container.innerHTML = "";

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

// ---------- STAR RATING CLICK LOGIC ----------
document.querySelectorAll(".star-rating span").forEach(star => {
    star.addEventListener("click", () => {
        const value = star.getAttribute("data-value");
        document.getElementById("reviewStars").value = value;
        // Update UI
        document.querySelectorAll(".star-rating span").forEach(s => {
            s.classList.toggle("active", s.getAttribute("data-value") <= value);
        });
    });
});

function scrollReviews(direction) {
    const container = document.getElementById("reviewsContainer");
    const scrollAmount = 280; // width of one card + gap
    container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}
</script>
</body>
</html>


   <!-- REVIEWS SECTION -->
<style>
.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: none;
}

.reviews-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;   
    letter-spacing: 1px;  
}

.add-review-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: transparent;     
    background-image: url('../images/plus.png');
    background-size: 60%;               
    background-repeat: no-repeat;
    background-position: center;
    border: none;                     
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
    width: 100%;         
    overflow: visible;
   
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
    top: 50%;
    transform: translateY(-50%);
    background: white;
    color: black;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    cursor: pointer;
    font-size: 22px;
    font-weight: bold;

    display: flex;              /* center arrow */
    align-items: center;
    justify-content: center;

    z-index: 2;
}

.nav-btn:hover {
    transform: translateY(-50%) scale(1.08);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
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

/* modal section  */
.review-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
}

.modal-content {
    background: #fff;
    width: 88%;
    max-width: 300px;       
    margin: 3% auto;       
    padding: 10px 14px;    
    border-radius: 14px;    
    box-shadow: 0 8px 28px rgba(0,0,0,0.15); 
}

.modal-content h2 {
    margin: 10 0 8px 0;  
    font-size: 22px;    
    font-weight: 700;
}


/* Close button */
.close-modal {
    float: right;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
}
.close-modal:hover {
    opacity: 1;
}

/* Labels */
#reviewForm label {
    font-size: 12px;
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
    display: block;
}

/* Inputs */
#reviewForm input,
#reviewForm textarea {
    width: 100%;
    margin-bottom: 6px;
    padding: 6px 8px;
    border-radius: 8px;          
    border: 1px solid #ddd;
    font-size: 13px;
    background: #f7f7f7;       
}

/* Shorter textarea */
#reviewForm textarea {
    height: 55px;
    resize: vertical;
}

/* Submit button */
.submit-review-btn {
    width: 100%;
    padding: 8px;
    background: #111;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    margin-top: 4px;
    transition: background 0.2s;
}
.submit-review-btn:hover {
    background: #000;
}

/* Star rating */
.star-rating {
    display: flex;
    gap: 3px;
    font-size: 22px;       
    cursor: pointer;
    margin-bottom: 8px;
}

.star-rating span {
    color: #ccc;
    transition: color 0.2s, transform 0.15s;
}

.star-rating span.active {
    color: #111;            
    transform: scale(1.1);  
            }

/* OUR FAVOURITES SECTION */
.grey-section {
    background-color: #B6B6B6;  /* same grey as reviews */
    padding: 25px 30px;
    margin: 60px auto;          /* space above and below */
    border-radius: 25px;
    max-width: 1100px;
    width: 100%;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
}

.grey-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
}

.grey-section p {
    font-size: 14px;
    margin-bottom: 20px;
    color: #333;
}

/* Collections cards */
.collection-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
}

.collection-cards .card {
    background: #B6B6B6;
    border-radius: 10px;
    padding: 10px;
    flex: 1 1 calc(33% - 20px); /* 3 cards per row */
    text-align: center;
    max-width: 33%;
    /* box-shadow: 0 4px 12px rgba(0,0,0,0.05); */
    transition: transform 0.2s ease;
}

.collection-cards .card:hover {
    transform: translateY(-4px);
}

.collection-cards .card img {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 8px;
}

.collection-cards .card h3 {
    font-size: 16px;
    font-weight: 600;
}
</style>
