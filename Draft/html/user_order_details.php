<?php
include '../backend/config/db_connect.php';
require_once '../backend/services/orderItemStatus.php';
session_start();

ensureOrderItemStatusColumn($conn);

if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit();
}

$user_ID = (int)$_SESSION['user_ID'];
$order_ID = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_ID <= 0) {
    die("Invalid order.");
}

$stmt = $conn->prepare("
SELECT *
FROM orders
WHERE order_ID = ? AND user_ID = ?
");

$stmt->bind_param("ii", $order_ID, $user_ID);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$user_stmt = $conn->prepare("
SELECT name
FROM users
WHERE user_ID = ?
");

$user_stmt->bind_param("i", $user_ID);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$user_name = $user['name'] ?? 'Customer';

$item_stmt = $conn->prepare("
SELECT p.name, p.image, oi.quantity, oi.unit_price, oi.item_status, (oi.quantity * oi.unit_price) AS line_total
FROM order_items oi
JOIN products p ON oi.product_ID = p.product_ID
WHERE oi.order_ID = ?
ORDER BY oi.order_item_ID
");

$item_stmt->bind_param("i", $order_ID);
$item_stmt->execute();
$items = $item_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$item_stmt->close();

$return_stmt = $conn->prepare("
SELECT return_ID
FROM returns
WHERE order_ID = ?
LIMIT 1
");
$return_stmt->bind_param("i", $order_ID);
$return_stmt->execute();
$hasReturnRequest = (bool)$return_stmt->get_result()->fetch_assoc();
$return_stmt->close();

$orderStatus = $order['order_status'] ?? 'Unknown';

function money($value) {
    return '£' . number_format((float)$value, 2);
}

function resolveProductImage(?string $imagePath): string {
    if (!$imagePath) {
        return '../images/basket-images/sofa.jpg';
    }

    if (preg_match('#^(https?:)?//#', $imagePath)) {
        return $imagePath;
    }

    $cleaned = preg_replace('#^(\.\./)+#', '', trim($imagePath));
    $cleaned = ltrim((string)$cleaned, '/');

    if (!str_starts_with($cleaned, 'images/')) {
        $cleaned = 'images/' . $cleaned;
    }

    return '../' . $cleaned;
}

$addressParts = array_map('trim', explode(",", $order['address']));

$line1 = $addressParts[0] ?? '';
$line2 = $addressParts[1] ?? '';
$city = $addressParts[2] ?? '';
$postcode = $addressParts[4] ?? '';
$activeItems = array_values(array_filter(
    $items,
    static fn($item) => strtolower((string)($item['item_status'] ?? 'active')) !== 'cancelled'
));
$itemCount = array_sum(array_map(static fn($item) => (int)($item['quantity'] ?? 0), $activeItems));
$subtotal = array_sum(array_map(static fn($item) => (float)($item['line_total'] ?? 0), $activeItems));
$storedTotal = (float)($order['total_price'] ?? 0);
$taxAmount = max(0, $storedTotal - $subtotal);
$orderTotal = $storedTotal > 0 ? $storedTotal : ($subtotal + $taxAmount);
$statusKey = strtolower(trim((string)$orderStatus));
$canReturn = !$hasReturnRequest && $statusKey !== 'cancelled' && !empty($activeItems);
$canCancel = !$hasReturnRequest && $statusKey !== 'cancelled' && !empty($activeItems);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Order Details</title>

<link rel="stylesheet" href="../css/header_footer_style.css?v=21">
<link rel="stylesheet" href="../css/user_order_details.css?v=4">

    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=13">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="order-details-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<div class="back-home-wrap">
    <a href="#" onclick="goBack(event)" class="back-home">← Go Back</a>
</div>

    <div class="container">

<div class="left-section">

<h2>Order #UK<?= str_pad($order['order_ID'], 5, "0", STR_PAD_LEFT) ?></h2>

<div class="order-action-bar">
    <a href="orderconfirmation.php?order_id=<?= $order['order_ID'] ?>" class="track-order-btn">Track My Order</a>
    <?php if ($canReturn): ?>
        <button type="button" class="detail-action-btn detail-action-btn-secondary" data-open-return>Return Item</button>
    <?php else: ?>
        <button type="button" class="detail-action-btn detail-action-btn-secondary" disabled>
            <?= $hasReturnRequest ? 'Return Requested' : 'Return Unavailable' ?>
        </button>
    <?php endif; ?>
    <?php if ($canCancel): ?>
        <button type="button" class="detail-action-btn detail-action-btn-danger" data-open-cancel>Cancel Order</button>
    <?php else: ?>
        <button type="button" class="detail-action-btn detail-action-btn-danger" disabled>
            <?= $statusKey === 'cancelled' ? 'Cancelled' : 'Cancel Unavailable' ?>
        </button>
    <?php endif; ?>
</div>

<div class="order-meta-grid">
    <div class="order-meta-card">
        <span class="order-meta-label">Customer</span>
        <span class="order-meta-value"><?= htmlspecialchars($user_name) ?></span>
    </div>
    <div class="order-meta-card">
        <span class="order-meta-label">Status</span>
        <span class="order-meta-value"><?= htmlspecialchars($orderStatus) ?></span>
    </div>
    <div class="order-meta-card">
        <span class="order-meta-label">Date</span>
        <span class="order-meta-value"><?= date("j F Y", strtotime($order['order_date'])) ?></span>
    </div>
    <div class="order-meta-card">
        <span class="order-meta-label">Items</span>
        <span class="order-meta-value"><?= (int)$itemCount ?></span>
    </div>
</div>

<?php if (!empty($items)): ?>
<div class="order-items-summary">
    <h3 class="order-items-title">Items in This Order</h3>
    <?php foreach ($items as $item): ?>
        <?php $itemStatus = trim((string)($item['item_status'] ?? 'Active')); ?>
        <div class="order-item-row <?= strtolower($itemStatus) === 'cancelled' ? 'order-item-row-cancelled' : '' ?>">
            <div class="order-item-thumb">
                <img src="<?= htmlspecialchars(resolveProductImage($item['image'] ?? null)) ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.onerror=null;this.src='../images/basket-images/sofa.jpg';">
            </div>
            <div class="order-item-copy">
                <div class="order-item-name">
                    <?= htmlspecialchars($item['name']) ?>
                    <?php if (strtolower($itemStatus) === 'cancelled'): ?>
                        <span class="order-item-status">Cancelled</span>
                    <?php endif; ?>
                </div>
                <div class="order-item-meta">Qty: <?= (int)$item['quantity'] ?> · Unit: <?= money($item['unit_price']) ?></div>
            </div>
            <div class="order-item-price"><?= money($item['line_total']) ?></div>
        </div>
    <?php endforeach; ?>
    <div class="order-summary-breakdown">
        <div class="order-summary-row">
            <span>Subtotal</span>
            <span><?= money($subtotal) ?></span>
        </div>
        <div class="order-summary-row">
            <span>Tax</span>
            <span><?= money($taxAmount) ?></span>
        </div>
    </div>
    <div class="order-summary-total">
        <span>Order Total</span>
        <strong><?= money($orderTotal) ?></strong>
    </div>
</div>
<?php endif; ?>

</div>


<div class="right-section">

<h3 class="shipping-address-title">Shipping Address:</h3>

<div class="form-row">
<label> Name</label>
<div class="display-line">
<?= htmlspecialchars($user_name) ?>
</div>
</div>

<div class="form-row">
<label>Address Line 1</label>
<div class="display-line">
<?= htmlspecialchars($line1) ?>
</div>
</div>

<div class="form-row">
<label>Address Line 2</label>
<div class="display-line">
<?= htmlspecialchars($line2) ?>
</div>
</div>

<div class="form-row">
<label>Town / City</label>
<div class="display-line">
<?= htmlspecialchars($city) ?>
</div>
</div>

<div class="form-row">
<label>Postcode</label>
<div class="display-line">
<?= htmlspecialchars($postcode) ?>
</div>
</div>

<div class="form-row">
<label>Country</label>
<div class="display-line">
United Kingdom
</div>
</div>

</div>

</div>

<?php if ($canReturn): ?>
<div id="returnModal" class="order-modal">
    <div class="order-modal-card">
        <button type="button" class="order-modal-close" data-close-modal>&times;</button>
        <h3>Return Item</h3>
        <p class="order-modal-copy">Choose the item you want to return and tell us why.</p>

        <form id="returnForm" class="order-modal-form">
            <input type="hidden" name="order_id" value="<?= $order_ID ?>">

            <label for="returnItem">Select Item</label>
            <select id="returnItem" name="order_item_id" required>
                <option value="">Select an item</option>
                <?php foreach ($items as $item): ?>
                    <?php if (strtolower((string)($item['item_status'] ?? 'active')) === 'cancelled') continue; ?>
                    <option value="<?= (int)$item['order_item_ID'] ?>">
                        <?= htmlspecialchars($item['name']) ?> · Qty <?= (int)$item['quantity'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="returnReason">Reason for Return</label>
            <select id="returnReason" name="reason" required>
                <option value="">Select a reason</option>
                <option value="damaged">Item arrived damaged</option>
                <option value="wrong">Wrong item received</option>
                <option value="size">Incorrect size</option>
                <option value="other">Other</option>
            </select>

            <label for="returnDetails">Additional Details</label>
            <textarea id="returnDetails" name="details" placeholder="Add any details that will help with your return."></textarea>

            <label for="returnName">Your Name (optional)</label>
            <input type="text" id="returnName" name="name" placeholder="Enter your name">

            <button type="submit" class="order-modal-submit">Submit Return</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($canCancel): ?>
<div id="cancelModal" class="order-modal">
    <div class="order-modal-card order-modal-card-wide">
        <button type="button" class="order-modal-close" data-close-modal>&times;</button>
        <h3>Cancel Order</h3>
        <p class="order-modal-copy">Choose whether to cancel the whole order or remove selected items from it.</p>

        <form id="cancelForm" class="order-modal-form">
            <input type="hidden" name="order_id" value="<?= $order_ID ?>">

            <div class="cancel-scope-grid">
                <label class="cancel-scope-option">
                    <input type="radio" name="cancel_scope" value="entire" checked>
                    <span>
                        <strong>Cancel entire order</strong>
                        <small>Keep the order record and mark everything as cancelled.</small>
                    </span>
                </label>
                <label class="cancel-scope-option">
                    <input type="radio" name="cancel_scope" value="items">
                    <span>
                        <strong>Cancel selected items</strong>
                        <small>Remove only the items you choose and update the order total.</small>
                    </span>
                </label>
            </div>

            <div class="cancel-item-picker" id="cancelItemPicker" hidden>
                <span class="cancel-item-picker-title">Select items to cancel</span>
                <div class="cancel-item-list">
                    <?php foreach ($items as $item): ?>
                        <?php if (strtolower((string)($item['item_status'] ?? 'active')) === 'cancelled') continue; ?>
                        <label class="cancel-item-option">
                            <input type="checkbox" name="order_item_ids[]" value="<?= (int)$item['order_item_ID'] ?>">
                            <span>
                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                                <small>Qty <?= (int)$item['quantity'] ?> · <?= money($item['line_total']) ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <label for="cancelReason">Reason for Cancellation</label>
            <select id="cancelReason" name="reason" required>
                <option value="">Select a reason</option>
                <option value="changed_mind">Changed my mind</option>
                <option value="ordered_by_mistake">Ordered by mistake</option>
                <option value="delivery_time">Delivery time is too long</option>
                <option value="other">Other</option>
            </select>

            <label for="cancelDetails">Additional Details</label>
            <textarea id="cancelDetails" name="details" placeholder="Tell us anything relevant about this cancellation."></textarea>

            <label for="cancelName">Your Name (optional)</label>
            <input type="text" id="cancelName" name="name" placeholder="Enter your name">

            <button type="submit" class="order-modal-submit order-modal-submit-danger">Submit Cancellation</button>
        </form>
    </div>
</div>
<?php endif; ?>

<div id="actionSuccessModal" class="order-modal">
    <div class="order-modal-card order-modal-card-compact">
        <h3>Request submitted</h3>
        <p class="order-modal-copy" id="actionSuccessMessage">Your order has been updated.</p>
        <button type="button" class="order-modal-submit" id="actionSuccessButton">Refresh Order</button>
    </div>
</div>


<?php $footerPartialOnly = true; include 'footer.php'; ?>
<script>
const returnModal = document.getElementById('returnModal');
const cancelModal = document.getElementById('cancelModal');
const successModal = document.getElementById('actionSuccessModal');
const successMessage = document.getElementById('actionSuccessMessage');
const successButton = document.getElementById('actionSuccessButton');
const cancelScopeInputs = document.querySelectorAll('input[name="cancel_scope"]');
const cancelItemPicker = document.getElementById('cancelItemPicker');

document.querySelector('[data-open-return]')?.addEventListener('click', () => {
    if (returnModal) returnModal.style.display = 'flex';
});

document.querySelector('[data-open-cancel]')?.addEventListener('click', () => {
    if (cancelModal) cancelModal.style.display = 'flex';
});

document.querySelectorAll('[data-close-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        button.closest('.order-modal').style.display = 'none';
    });
});

window.addEventListener('click', (event) => {
    if (event.target.classList.contains('order-modal')) {
        event.target.style.display = 'none';
    }
});

cancelScopeInputs.forEach((input) => {
    input.addEventListener('change', () => {
        const showItemPicker = input.checked && input.value === 'items';
        if (cancelItemPicker) {
            cancelItemPicker.hidden = !showItemPicker;
        }
        if (!showItemPicker) {
            document.querySelectorAll('input[name="order_item_ids[]"]').forEach((checkbox) => {
                checkbox.checked = false;
            });
        }
    });
});

document.getElementById('returnForm')?.addEventListener('submit', async function(event) {
    event.preventDefault();

    try {
        const response = await fetch('submit_return.php', {
            method: 'POST',
            body: new FormData(this)
        });
        const result = await response.json();

        if (result.status !== 'success') {
            throw new Error(result.message || 'Unable to submit return');
        }

        returnModal.style.display = 'none';
        successMessage.textContent = 'Your return request has been submitted. Refreshing the order now.';
        successModal.style.display = 'flex';
        setTimeout(() => window.location.reload(), 700);
    } catch (error) {
        alert(error.message);
    }
});

document.getElementById('cancelForm')?.addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const selectedScope = formData.get('cancel_scope');
    if (selectedScope === 'items') {
        const selectedItems = formData.getAll('order_item_ids[]');
        if (!selectedItems.length) {
            alert('Select at least one item to cancel.');
            return;
        }
    }

    try {
        const response = await fetch('submit_cancel.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            throw new Error(result.message || 'Unable to submit cancellation');
        }

        cancelModal.style.display = 'none';
        successMessage.textContent = result.message || 'Your order has been updated. Refreshing now.';
        successModal.style.display = 'flex';
        setTimeout(() => window.location.reload(), 700);
    } catch (error) {
        alert(error.message);
    }
});

successButton?.addEventListener('click', () => window.location.reload());

function goBack(e) {
    e.preventDefault();

    if (document.referrer && document.referrer !== window.location.href) {
        history.back();
    } else {
        window.location.href = "user_order_history.php"; 
    }
}
</script>
</body>
</html>
