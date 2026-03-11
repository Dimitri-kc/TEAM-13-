<?php 
session_start();
include '../backend/config/db_connect.php'; 

$product_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;

$query = "SELECT * FROM products WHERE product_ID = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: livingroom.php");
    exit();
}

$isFavourited = false;
if (isset($_SESSION['user_ID'])) {
    $uid = (int)$_SESSION['user_ID'];
    $pid = (int)$product_id;
    $fav_check = $conn->prepare("SELECT 1 FROM favourites WHERE user_ID = ? AND product_ID = ? LIMIT 1");
    $fav_check->bind_param("ii", $uid, $pid);
    $fav_check->execute();
    $isFavourited = (bool)$fav_check->get_result()->fetch_row();
    $fav_check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | LOFT & LIVING</title>
    <link rel="stylesheet" href="../css/header_footer_style.css">
    <link rel="stylesheet" href="../css/sofa_style.css">
    <link rel="stylesheet" href="../css/favourites-toggle.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh;
            padding: 0;
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

<?php include 'header.php'; ?>

<main class="container">
    <section class="product-wrapper">

        <div class="product-image zoom-container">
            <img
                id="zoom-image"
                src="../images/<?php echo htmlspecialchars($product['image']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
            >
        </div>

        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="price">£<?php echo htmlspecialchars($product['price']); ?></div>
            <p class="tagline">Bold, Modern, Elegant</p>

            <div class="selectors">
                <div class="select-group">
                    <label>Size</label>
                    <select><option>ONE SIZE</option></select>
                </div>
                <div class="select-group">
                    <label>Quantity</label>
                    <select id="quantity">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
            </div>

<div class="action-buttons">
    <button class="add-to-basket basket-btn" data-id="<?= $product['product_ID'] ?>">
        Add to Basket
    </button>

    <form method="post" action="favourite_toggle.php" 
          class="favourite-toggle-form js-favourite-form"
          style="display: contents;">
        <input type="hidden" name="product_id" value="<?= $product['product_ID'] ?>">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
        <button
            type="submit"
            class="favourite-toggle-btn js-favourite-button <?= $isFavourited ? 'is-active' : '' ?>"
            data-favourite-state="<?= $isFavourited ? 'true' : 'false' ?>"
            aria-pressed="<?= $isFavourited ? 'true' : 'false' ?>"
            title="<?= $isFavourited ? 'Remove from favourites' : 'Add to favourites' ?>"
        ><?= $isFavourited ? '♥' : '♡' ?></button>
    </form>
</div>

            <div class="description-box">
                <div class="desc-header">
                    <span>About this Item</span>
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        </div>

    </section>

    <!-- REVIEWS SECTION -->
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

    <!-- REVIEW MODAL -->
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
                <input type="hidden" id="reviewProductID" name="product_ID">
            </form>
        </div>
    </div>

</main>

<!-- BASKET POPUP MODAL -->
<div id="basket-modal" class="basket-modal">
    <div class="basket-modal-content">
        <p>Item added to basket!</p>
        <div class="basket-modal-buttons">
            <button id="go-to-basket">Proceed to Basket</button>
            <button id="continue-shopping">Continue Shopping</button>
        </div>
    </div>
</div>

<!-- FAVOURITE POPUP MODAL -->
<div id="favourite-modal" class="favourite-modal">
    <div class="favourite-modal-content">
        <p id="favourite-modal-msg">Added to favourites!</p>
        <div class="favourite-modal-buttons">
            <button id="go-to-favourites">View Favourites</button>
            <button id="continue-browsing">Continue Browsing</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- IMAGE ZOOM MODAL -->
<div id="imageModal" class="image-modal">
    <span class="close-modal-image">&times;</span>
    <div class="modal-image-wrapper">
        <img id="modalZoomImage" src="" alt="">
        <img id="zoomIcon" class="zoom-icon" src="../images/zoom_in.png" alt="Zoom">
    </div>
</div>

<script src="../javascript/sofa_script.js"></script>
<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>
<script src="../javascript/image_zoom.js"></script>
<script src="../javascript/favourites-toggle.js"></script>

<script>
// ---------- Add to Basket ----------
document.querySelectorAll(".add-to-basket").forEach(button => {
    button.addEventListener("click", async () => {
        const productID = parseInt(button.dataset.id);
        const quantity = parseInt(document.getElementById("quantity")?.value || 1);
        await addToBasket(productID, quantity, button);
        document.getElementById("basket-modal").classList.add("active");
    });
});

document.getElementById("go-to-basket").addEventListener("click", () => {
    window.location.href = "basket.php";
});

document.getElementById("continue-shopping").addEventListener("click", () => {
    document.getElementById("basket-modal").classList.remove("active");
});

document.getElementById("basket-modal").addEventListener("click", (e) => {
    if (e.target === document.getElementById("basket-modal")) {
        document.getElementById("basket-modal").classList.remove("active");
    }
});

    

document.getElementById("go-to-favourites").addEventListener("click", () => {
    window.location.href = "favourites.php";
});

document.getElementById("continue-browsing").addEventListener("click", () => {
    document.getElementById("favourite-modal").classList.remove("active");
});

document.getElementById("favourite-modal").addEventListener("click", (e) => {
    if (e.target === document.getElementById("favourite-modal")) {
        document.getElementById("favourite-modal").classList.remove("active");
    }
});

// ---------- Review Modal ----------
const modal = document.getElementById("reviewModal");
const addBtn = document.querySelector(".add-review-btn");
const closeModal = document.querySelector(".close-modal");
const productId = <?php echo (int)$product['product_ID']; ?>;

addBtn.onclick = () => {
    document.getElementById("reviewProductID").value = productId;
    modal.style.display = "block";
};
closeModal.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };

// ---------- Submit Review ----------
document.getElementById("reviewForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const stars = document.getElementById("reviewStars").value;
    const title = document.getElementById("reviewTitle").value;
    const text  = document.getElementById("reviewText").value;
    const name  = document.getElementById("reviewName").value;

    fetch("../html/submit_product_review.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_ID=${productId}&stars=${stars}&title=${encodeURIComponent(title)}&text=${encodeURIComponent(text)}&name=${encodeURIComponent(name)}`
    })
    .then(r => r.text())
    .then(data => {
        if (data.trim() === "success") {
            loadReviewsFromDB();
            document.getElementById("reviewForm").reset();
            modal.style.display = "none";
        } else {
            alert("Error saving review");
        }
    });
});

// ---------- Load Reviews ----------
function loadReviewsFromDB() {
    fetch(`../html/get_product_reviews.php?product_ID=${productId}`)
    .then(r => r.json())
    .then(reviews => {
        const container = document.getElementById("reviewsContainer");
        container.innerHTML = "";

        if (!reviews || reviews.length === 0) {
            container.innerHTML = `
                <div class="no-reviews-message">
                    <p>No reviews yet.</p>
                    <p>Be the first to review this product!</p>
                </div>`;
            return;
        }

        reviews.forEach(review => {
            const card = document.createElement("div");
            card.classList.add("review-card");
            const date = new Date(review.created_at).toLocaleDateString("en-GB", {
                day: "numeric", month: "long", year: "numeric"
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
                </div>`;
            container.appendChild(card);
        });
    });
}

loadReviewsFromDB();

// ---------- Star Rating ----------
document.querySelectorAll(".star-rating span").forEach(star => {
    star.addEventListener("click", () => {
        const value = star.getAttribute("data-value");
        document.getElementById("reviewStars").value = value;
        document.querySelectorAll(".star-rating span").forEach(s => {
            s.classList.toggle("active", s.getAttribute("data-value") <= value);
        });
    });
});

// ---------- Scroll Reviews ----------
function scrollReviews(direction) {
    const container = document.getElementById("reviewsContainer");
    container.scrollBy({ left: direction * 280, behavior: "smooth" });
}
</script>

<style>
/* IMAGE MODAL */
.image-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease;
    z-index: 9999;
}
.image-modal.active { opacity: 1; pointer-events: auto; }
.modal-image-wrapper {
    position: relative;
    width: 60vw;
    max-width: 600px;
    display: flex;
    justify-content: center;
    align-items: center;
}
#modalZoomImage {
    width: 100%;
    height: auto;
    object-fit: contain;
    transition: transform .25s ease;
    pointer-events: none;
}
.close-modal-image {
    position: absolute;
    top: 20px; right: 30px;
    font-size: 40px;
    color: white;
    cursor: pointer;
    z-index: 10000;
    font-weight: bold;
}
.close-modal-image:hover { opacity: 0.7; }
.zoom-icon {
    position: absolute;
    bottom: 20px; right: 20px;
    width: 40px;
    opacity: 0.85;
    pointer-events: none;
}
#zoom-image { cursor: zoom-in; }
.modal-image-wrapper.zoom-ready { cursor: zoom-in; }
.modal-image-wrapper.zoomed { cursor: zoom-out; }

/* REVIEWS SECTION */
.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.reviews-header h2 { font-size: 22px; font-weight: 700; letter-spacing: 1px; }
.add-review-btn {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: transparent url('../images/plus.png') center/60% no-repeat;
    border: none; cursor: pointer; position: relative;
}
.add-review-btn::after {
    content: "Add Review";
    position: absolute;
    top: -40px; left: 50%;
    transform: translateX(-50%);
    background: black; color: white;
    padding: 6px 10px; border-radius: 6px;
    font-size: 12px; white-space: nowrap;
    opacity: 0; pointer-events: none;
    transition: opacity 0.2s ease;
}
.add-review-btn::before {
    content: "";
    position: absolute;
    top: -12px; left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: black;
    opacity: 0;
    transition: opacity 0.2s ease;
}
.add-review-btn:hover::after,
.add-review-btn:hover::before { opacity: 1; }
.add-review-btn:hover { background-color: #d3d3d3; }

.reviews-slider-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    overflow: visible;
}
.reviews-container {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 10px 0;
    scrollbar-width: none;
}
.reviews-container::-webkit-scrollbar { display: none; }
.review-card {
    min-width: 260px; max-width: 260px;
    background: white; padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.review-card:hover { transform: translateY(-4px); }
.stars {
    font-size: 28px; margin-bottom: 8px;
    background: linear-gradient(90deg, #555, #222);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.review-card h3 { margin: 8px 0; font-size: 16px; }
.review-card p { font-size: 13px; color: #555; margin-bottom: 15px; }
.reviewer { display: flex; align-items: center; gap: 10px; }
.reviewer img { width: 35px; height: 35px; border-radius: 50%; }
.name { display: block; font-weight: 600; font-size: 13px; }
.date { font-size: 11px; color: #777; }
.nav-btn {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    background: white; color: black;
    border: none; width: 40px; height: 40px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    cursor: pointer; font-size: 22px; font-weight: bold;
    display: flex; align-items: center; justify-content: center;
    z-index: 2;
}
.nav-btn:hover { transform: translateY(-50%) scale(1.08); }
.prev-btn { left: -20px; }
.next-btn { right: -20px; }
.reviews-section {
    background-color: #B6B6B6;
    padding: 25px 30px;
    margin: 90px auto;
    border-radius: 25px;
    max-width: 1100px;
    width: 100%;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
}

/* REVIEW MODAL */
.review-modal {
    display: none; position: fixed;
    z-index: 9999; inset: 0;
    background: rgba(0,0,0,0.45);
}
.modal-content {
    background: #fff;
    width: 88%; max-width: 300px;
    margin: 3% auto; padding: 10px 14px;
    border-radius: 14px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.15);
}
.modal-content h2 { margin: 10px 0 8px; font-size: 22px; font-weight: 700; }
.close-modal { float: right; font-size: 20px; cursor: pointer; opacity: 0.6; }
.close-modal:hover { opacity: 1; }
#reviewForm label { font-size: 12px; font-weight: 600; color: #333; margin-bottom: 2px; display: block; }
#reviewForm input,
#reviewForm textarea {
    width: 100%; margin-bottom: 6px;
    padding: 6px 8px; border-radius: 8px;
    border: 1px solid #ddd; font-size: 13px; background: #f7f7f7;
}
#reviewForm textarea { height: 55px; resize: vertical; }
.submit-review-btn {
    width: 100%; padding: 8px;
    background: #111; color: white;
    border: none; border-radius: 8px;
    cursor: pointer; font-size: 14px; font-weight: 600; margin-top: 4px;
}
.submit-review-btn:hover { background: #000; }
.star-rating { display: flex; gap: 3px; font-size: 22px; cursor: pointer; margin-bottom: 8px; }
.star-rating span { color: #ccc; transition: color 0.2s, transform 0.15s; }
.star-rating span.active { color: #111; transform: scale(1.1); }
.no-reviews-message { padding-left: 80px; font-size: 1.1rem; }
.no-reviews-message p:first-child { font-weight: 600; margin-bottom: 4px; }
.no-reviews-message p:last-child { opacity: 0.8; }

/* BASKET MODAL */
.basket-modal {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 9998;
    justify-content: center; align-items: center;
}
.basket-modal.active { display: flex; }
.basket-modal-content {
    background: #fff; border-radius: 12px;
    padding: 28px 32px; text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    min-width: 280px;
}
.basket-modal-content p { font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #111; }
.basket-modal-buttons { display: flex; gap: 10px; justify-content: center; }
.basket-modal-buttons button {
    padding: 10px 18px; border: none; border-radius: 6px;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;
}
#go-to-basket { background: #111; color: #fff; }
#go-to-basket:hover { background: #333; }
#continue-shopping { background: #f0f0f0; color: #111; }
#continue-shopping:hover { background: #e0e0e0; }

/* FAVOURITE MODAL */
.favourite-modal {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 9998;
    justify-content: center; align-items: center;
}
.favourite-modal.active { display: flex; }
.favourite-modal-content {
    background: #fff; border-radius: 12px;
    padding: 28px 32px; text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    min-width: 280px;
}
.favourite-modal-content p { font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #111; }
.favourite-modal-buttons { display: flex; gap: 10px; justify-content: center; }
.favourite-modal-buttons button {
    padding: 10px 18px; border: none; border-radius: 6px;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s;
}
#go-to-favourites { background: #111; color: #fff; }
#go-to-favourites:hover { background: #333; }
#continue-browsing { background: #f0f0f0; color: #111; }
#continue-browsing:hover { background: #e0e0e0; }
</style>

</body>
</html>