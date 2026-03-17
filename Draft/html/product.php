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
     <link rel="stylesheet" href="../css/reviews.css">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">

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

            <?php
                $stock = (int)$product['stock'];
                if ($stock <= 0) {
                    $stockLabel = 'Out of Stock';
                    $stockClass = 'stock-out';
                } elseif ($stock <= 5) {
                    $stockLabel = 'Low Stock (' . $stock . ' left)';
                    $stockClass = 'stock-low';
                } else {
                    $stockLabel = 'In Stock';
                    $stockClass = 'stock-in';
                }
            ?>
            <div class="stock-indicator <?php echo $stockClass; ?>">
                <span class="stock-dot"></span>
                <span class="stock-text"><?php echo $stockLabel; ?></span>
            </div>

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
    <button class="add-to-basket basket-btn"
            data-id="<?= $product['product_ID'] ?>"
            <?= $stock <= 0 ? 'disabled' : '' ?>>
        <?= $stock <= 0 ? 'Out of Stock' : 'Add to Basket' ?>
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
                <label>Title:</label><script></script>
                <input type="text" id="reviewTitle" required>
                <label>Review:</label>
               <textarea id="reviewText" maxlength="200" required></textarea>
               <small id="charCount">0 / 200 characters</small>
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
modal.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });
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

// ---------- Character Counter ----------
const reviewText = document.getElementById("reviewText");
const charCount = document.getElementById("charCount");

if (reviewText && charCount) {
    reviewText.addEventListener("input", () => {
        charCount.textContent = `${reviewText.value.length} / 200`;
    });
}

// ---------- Scroll Reviews ----------
function scrollReviews(direction) {
    const container = document.getElementById("reviewsContainer");
    container.scrollBy({ left: direction * 280, behavior: "smooth" });
}
</script>



</body>
</html>