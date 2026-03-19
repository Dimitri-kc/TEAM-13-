<?php
include '../backend/config/db_connect.php';
session_start();

//fetch user details for pre-filling form
$userName = '';
$userEmail = '';
$userPhone = '';
$userAddress1 = '';
$userAddress2 = '';
$userCity = '';
$userPostcode = '';

if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit();
}

$user_ID = (int)$_SESSION['user_ID'];
$stmt = $conn->prepare("SELECT name, surname, email, phone, address FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {

$userName = trim(($user['name'] ?? '') . ' ' . ($user['surname'] ?? ''));
$userEmail = $user['email'] ?? '';
$userPhone = $user['phone'] ?? '';

$addressParts = array_map('trim', explode(',', $user['address'] ?? '',3));
$userAddress1 = $addressParts[0] ?? ''; 
$userAddress2 = '';
$userCity = $addressParts[1] ?? '';
$userPostcode = $addressParts[2] ?? '';
}
$stmt->close();

// fetch default card if one exists
$defaultCard = null;
$cardStmt = $conn->prepare(
    "SELECT last_four, expiry_month, expiry_year, cardholder_name
     FROM user_payment_cards
     WHERE user_ID = ? AND is_default = 1
     LIMIT 1"
);
$cardStmt->bind_param("i", $user_ID);
$cardStmt->execute();
$defaultCard = $cardStmt->get_result()->fetch_assoc();
$cardStmt->close();

function money($n){
    return "£" . number_format((float)$n, 2);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit();
}

$user_ID = isset($_SESSION["user_ID"]) ? (int)$_SESSION["user_ID"] : null;
$cart = ($user_ID > 0) ? ($_SESSION["cart"] ?? []) : ($_SESSION["guest_basket"] ?? []);

$subtotal = 0.0;
$delivery = 0.0;
$tax = 0.0;
$total = 0.0;
$basketItems = []; 


$ids = array_keys($cart);
if (count($ids) > 0) {
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $sql = "SELECT product_ID, name, price, image FROM products WHERE product_ID IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $pid = (int)$row["product_ID"];
            $qty = (int)($cart[$pid] ?? 0);
            $price = (float)$row["price"];
            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;

           
            $basketItems[] = [
                'product_ID' => $pid,
                'name' => $row['name'] ?? '',
                'price' => $price,
                'qty' => $qty,
                'image' => $row['image'] ?? ''
            ];
        }
        $stmt->close();
    }
}

$tax = $subtotal * 0.10;
$total = $subtotal + $tax + $delivery;
}
?>
<?php
$pageTitle = 'Checkout | LOFT &amp; LIVING';
$extraHeadContent = <<<'HTML'
<link rel="stylesheet" href="../css/checkout.css?v=6">

<style>
input[name="postcode"]{
    margin-bottom: 28px !important;
}
</style>
HTML;

include 'header.php';
?>

<main class="checkout-page">
<div class="checkout-page-head">
    <div class="checkout-head-copy">
        <header class="checkout-header">
            <h1>Checkout</h1>
        </header>
        <p class="checkout-caption">Complete your order details and payment to bring your home together.</p>
        <a href="#" class="view-basket-btn checkout-top-basket-btn" id="openBasketBtn">View Basket</a>
    </div>
    <a href="basket.php" class="return-basket-btn">Return to Basket</a>
</div>

<section class="checkout-layout">

<aside class="product-column">

<div class="basket-totals">

<div class="total-row">
<span>Subtotal:</span>
<span id="subtotal"><?= money($subtotal) ?></span>
</div>

<div class="total-row">
<span>Tax (10%):</span>
<span id="tax"><?= money($tax) ?></span>
</div>

<div class="total-row">
<span>Delivery:</span>
<span id="delivery"><?= money($delivery) ?></span>
</div>

<div class="total-row total">
<span><strong>Total:</strong></span>
<span id="total"><strong><?= money($total) ?></strong></span>
</div>

</div>

<div class="checkout-sidebar-actions">
    <button type="submit" name="place_order" class="submit-btn" form="checkout-form">Confirm Payment</button>

    <div class="pay-buttons">
        <img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
        <img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
    </div>
</div>

</aside>

<section class="details-column">

<form method="post" id="checkout-form">

<section class="review-box">
<h1 class="form-title">YOUR DETAILS</h1>

<div class="review-details">
<label><strong>Full Name *</strong></label>
<input type="text" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>

<label><strong>Email *</strong></label>
<input type="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>

<label><strong>Phone</strong></label>
<input type="text" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>">

<label><strong>Address line 1 *</strong></label>
<input type="text" name="address1" value="<?php echo htmlspecialchars($userAddress1); ?>" required>

<label><strong>Address line 2</strong></label>
<input type="text" name="address2" value="<?php echo htmlspecialchars($userAddress2); ?>">

<label><strong>City *</strong></label>
<input type="text" name="city" value="<?php echo htmlspecialchars($userCity); ?>" required>

<label><strong>Postcode *</strong></label>
<input type="text" name="postcode" value="<?php echo htmlspecialchars($userPostcode); ?>" required>
</div>
</section>

<div class="card-fields">

<h1 class="form-title">CARD DETAILS</h1>
<?php if ($defaultCard): 
     $prefillExpiry = str_pad($defaultCard['expiry_month'],2,'0',STR_PAD_LEFT) . '/' . substr($defaultCard['expiry_year'],-2);
?>
    <div class="saved-card-note">
        Default card on file: •••• •••• •••• <?= htmlspecialchars($defaultCard['last_four']) ?> 
        &nbsp;·&nbsp; Expires <?= str_pad($defaultCard['expiry_month'],2,'0',STR_PAD_LEFT) ?>/<?= substr($defaultCard['expiry_year'],-2) ?>
    </div>

    <!-- hidden pre-filled fields submitted with form -->
    <input type="hidden" name="card_number" value="************<?= htmlspecialchars($defaultCard['last_four']) ?>">
    <input type="hidden" name="expiry" value="<?= $prefillExpiry ?>">
    <!-- only CVV required -->
    <input type="text" id="cvv" name="cvv" placeholder="CVV (3 Digits)" maxlength="3" inputmode="numeric" required />

    <!-- manual override toggle -->
    <button type="button" onclick="toggleManualEntry()" class="manual-entry-toggle">
        Enter card details manually
    </button>
    <div id="manualFields" style="display:none;">
        <input type="text" id="card_number_manual" placeholder="Card Number" maxlength="19" inputmode="numeric" oninput="formatCardNumber(this)">
        <input type="text" id="expiry_manual" placeholder="Expiry Date (MM/YY)" maxlength="5" oninput="formatExpiry(this)">
    </div>

<?php else: ?>
<input type="text" id="card_number" name="card_number" placeholder="Card Number (1234 4567 8901 2345)" maxlength="19" inputmode="numeric" oninput="formatCardNumber(this)" required />
<input type="text" id="expiry" name="expiry" placeholder="Expiry Date (MM/YY)" maxlength="5" pattern="(0[1-9]|1[0-2])/[0-9]{2}" oninput="formatExpiry(this)" required />
<input type="text" id="cvv" name="cvv" placeholder="CVV (3 Digits)" maxlength="3" inputmode="numeric" required />
<?php endif; ?>

</div>

</form>

</section>

</section>

</main>

<!-- basket side bar  -->
<div id="basketOverlay" class="basket-overlay">
    <div class="basket-drawer">
        <button type="button" id="closeBasketBtn" class="basket-close">&times;</button>

        <div class="basket-drawer-content">
            <h2 class="basket-drawer-title">View your bag (<?= count($basketItems) ?>)</h2>

            <?php if (!empty($basketItems)): ?>
                <?php foreach ($basketItems as $item): ?>
                    <?php
                        $imgPath = !empty($item['image']) ? '../images/' . ltrim($item['image'], '/') : '../images/placeholder.png';
                    ?>
                    <div class="basket-drawer-item">
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="basket-drawer-item-image">
                        <div>
                            <p class="basket-drawer-item-name"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="basket-drawer-item-meta">Qty: <?= (int)$item['qty'] ?></p>
                        </div>
                        <div class="basket-drawer-item-price"><?= money($item['price'] * $item['qty']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your basket is empty.</p>
            <?php endif; ?>
        </div>

        <div class="basket-drawer-footer">
            <div class="basket-drawer-total">
                <span>Total</span>
                <span><?= money($total) ?></span>
            </div>

            <button type="button" class="basket-drawer-btn primary" id="backToShippingBtn">Back to checkout</button>
            <a href="basket.php" class="basket-drawer-btn secondary">Edit basket</a>
        </div>
    </div>
</div>

<script src="../javascript/checkout.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
   const openBasketBtns = document.querySelectorAll('#openBasketBtn');
    const closeBasketBtn = document.getElementById('closeBasketBtn');
    const backToShippingBtn = document.getElementById('backToShippingBtn');
    const basketOverlay = document.getElementById('basketOverlay');

    openBasketBtns.forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        basketOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
});

    function closeBasketDrawer() {
        basketOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (closeBasketBtn) {
        closeBasketBtn.addEventListener('click', closeBasketDrawer);
    }

    if (backToShippingBtn) {
        backToShippingBtn.addEventListener('click', closeBasketDrawer);
    }

    if (basketOverlay) {
        basketOverlay.addEventListener('click', function (e) {
            if (e.target === basketOverlay) {
                closeBasketDrawer();
            }
        });
    }
});

function toggleManualEntry() {
    const manual = document.getElementById('manualFields');
    const isHidden = manual.style.display === 'none';
    manual.style.display = isHidden ? 'block' : 'none';

    // if opening manual, override the hidden fields on input
    if (isHidden) {
        document.getElementById('card_number_manual').addEventListener('input', function() {
            document.querySelector('input[name="card_number"]').value = this.value.replace(/\s/g,'');
        });
        document.getElementById('expiry_manual').addEventListener('input', function() {
            document.querySelector('input[name="expiry"]').value = this.value;
        });
    }
}

function formatCardNumber(input) {
    let val = input.value.replace(/\D/g, '').slice(0, 16);
    input.value = val.match(/.{1,4}/g)?.join(' ') ?? val;
}

function formatExpiry(input) { 
    let val = input.value.replace(/\D/g, '').slice(0, 4);
    if (val.length >= 3) val = val.slice(0, 2) + '/' + val.slice(2);
    input.value = val;
}
</script>

<?php include 'footer.php'; ?>
