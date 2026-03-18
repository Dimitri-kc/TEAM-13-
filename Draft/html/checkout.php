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
<link rel="stylesheet" href="../css/checkout.css">

<style>
#basket-count { display:none!important; }
.checkout-layout{ display:flex!important; }
.product-column{ order:1!important; }
.details-column{ order:2!important; }
.product-column img{ display:none!important; }
.submit-btn,.checkout-btn{ background:#ccc!important; }

.card-fields .form-title{
    display: block;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 12px 14px;
    margin: 0 0 10px 0;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.2;
    background: #fff;
}

input[name="postcode"]{
    margin-bottom: 28px !important;
}
.card-fields{
    margin-top: 12px !important;
    clear: both;
}

.payment-summary{
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 18px 16px;
    margin: 14px 0;
    background: #fff;
}
.payment-summary .row{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 8px 0;
}
.payment-summary .row.total{
    font-weight: 700;
    font-size: 16px;
    margin-top: 10px;
}

.view-basket-btn{
    padding: 10px 12px;
    border: 1px solid #111;
    border-radius: 6px;
    background: #fff;
    color: #111;
    text-decoration: none;
    font-size: 12px;
    text-align: center;
    box-sizing: border-box;
}
.payment-actions{
    display:flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin: 12px 0;
}


.basket-overlay{
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: none;
    justify-content: flex-end;
    z-index: 99999;
}
.basket-overlay.active{
    display: flex;
}
.basket-drawer{
    width: 650px;
    max-width: 100%;
    height: 100%;
    background: #fff;
    display: flex;
    flex-direction: column;
    position: relative;
    box-shadow: -8px 0 24px rgba(0,0,0,0.12);
    animation: slideInRight 0.25s ease;
    overflow: visible;
}

.basket-close{
    position: absolute;
    top: 20px;
    right: 20px;
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    color: #111;
    font-size: 32px;
    line-height: 40px;
    text-align: center;
    cursor: pointer;
    z-index: 9999;
    display: block;
}
.basket-drawer-content{
    padding: 56px 28px 24px;
    flex: 1;
    overflow-y: auto;
}
.basket-drawer-title{
    font-size: 22px;
    font-weight: 500;
    margin: 0 0 28px 0;
}
.basket-drawer-item{
    display: grid;
    grid-template-columns: 110px 1fr auto;
    gap: 16px;
    align-items: start;
    margin-bottom: 24px;
}
.basket-drawer-item-image{
    width: 110px;
    height: 140px;
    object-fit: cover;
    background: #f5f1ec;
}
.basket-drawer-item-name{
    font-size: 16px;
    line-height: 1.3;
    margin: 0 0 10px 0;
}
.basket-drawer-item-meta{
    font-size: 15px;
    color: #5c6f82;
    margin: 0;
}
.basket-drawer-item-price{
    font-size: 18px;
    font-weight: 600;
    color: #3f2330;
    white-space: nowrap;
}
.basket-drawer-footer{
    border-top: 1px solid #ddd;
    padding: 20px 28px 28px;
}
.basket-drawer-total{
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 700;
    margin-bottom: 18px;
}
.basket-drawer-btn{
    width: 100%;
    height: 56px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    margin-bottom: 14px;
}
.basket-drawer-btn.primary{
    background: #442732;
    color: #fff;
}
.basket-drawer-btn.secondary{
    background: #fff;
    color: #442732;
    border: 1px solid #442732;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-column .view-basket-btn{
    display:block;
    width:100%;
    margin-top:15px;
    text-align:center;
}

</style>
HTML;

include 'header.php';
?>

<header class="checkout-header">
<h1>CHECKOUT</h1>
</header>

<main class="checkout-layout">

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

<a href="#" class="view-basket-btn" id="openBasketBtn2">View Basket</a>

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
    <div style="font-size:13px; color:#555; margin-bottom:12px; padding:10px 14px; background:#f5f5f5; border-radius:8px;">
        Default card on file: •••• •••• •••• <?= htmlspecialchars($defaultCard['last_four']) ?> 
        &nbsp;·&nbsp; Expires <?= str_pad($defaultCard['expiry_month'],2,'0',STR_PAD_LEFT) ?>/<?= substr($defaultCard['expiry_year'],-2) ?>
    </div>

    <!-- hidden pre-filled fields submitted with form -->
    <input type="hidden" name="card_number" value="************<?= htmlspecialchars($defaultCard['last_four']) ?>">
    <input type="hidden" name="expiry" value="<?= $prefillExpiry ?>">
    <!-- only CVV required -->
    <input type="text" id="cvv" name="cvv" placeholder="CVV (3 Digits)" maxlength="3" inputmode="numeric" required />

    <!-- manual override toggle -->
    <button type="button" onclick="toggleManualEntry()" style="background:none;border:none;color:#555;font-size:12px;text-decoration:underline;cursor:pointer;padding:4px 0;margin-bottom:10px;">
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

<div class="payment-summary">
    <div class="row">
        <span>Subtotal</span>
        <span><?= money($subtotal) ?></span>
    </div>
    <div class="row">
        <span>Tax (10%)</span>
        <span><?= money($tax) ?></span>
    </div>
    <div class="row">
        <span>Delivery</span>
        <span><?= money($delivery) ?></span>
    </div>
    <div class="row total">
        <span>Total</span>
        <span><?= money($total) ?></span>
    </div>
</div>

<div class="payment-actions">
    <a href="#" class="view-basket-btn" id="openBasketBtn">View Basket</a>
    <div style="font-weight:700;">
        Total: <?= money($total) ?>
    </div>
</div>

<button type="submit" name="place_order" class="submit-btn">Confirm Payment</button>

<div class="pay-buttons">
<img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
<img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
</div>

</div>

</form>

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
   const openBasketBtns = document.querySelectorAll('#openBasketBtn, #openBasketBtn2');
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