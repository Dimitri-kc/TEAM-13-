<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

function e($s): string {
    return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
}

function money(float $n, string $currency = "£"): string {
    return $currency . number_format($n, 2);
}

$hasOrder = false;
$order = null;
$cleanItems = [];
$subtotal = 0.0;
$currency = "£";
$orderNumber = "";
$orderDate = date("F j, Y");
$orderStatus = "Pending";
$address = "No address available";
$total = 0.0;
$taxRate = 0.10;
$tax = 0.0;
$statusKey = "pending";
$sessionOrder = $_SESSION['order'] ?? null;

try {
    include '../backend/config/db_connect.php';

    if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    $user_ID  = isset($_SESSION['user_ID']) ? (int)$_SESSION['user_ID'] : 0;
    $order_ID = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

    if ($order_ID > 0 && $user_ID > 0) {

        $stmt = $conn->prepare("
            SELECT *
            FROM orders
            WHERE order_ID = ? AND user_ID = ?
            LIMIT 1
        ");
        $stmt->bind_param("ii", $order_ID, $user_ID);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($order) {
            $hasOrder    = true;
            $orderNumber = (string)$order['order_ID'];
            $orderDate   = date("F j, Y", strtotime($order['order_date'] ?? $order['created_at'] ?? 'now'));
            $address     = $order['address'] ?? 'No address saved';
            $total       = (float)($order['total_price'] ?? 0.0);

            $rawStatus = strtolower(trim((string)($order['status'] ?? $order['order_status'] ?? 'pending')));

            if ($rawStatus === 'preparing your order') {
                $rawStatus = 'processing';
            }

            $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            $statusKey = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'pending';

            $statusLabels = [
                'pending'    => 'Pending',
                'processing' => 'Processing',
                'shipped'    => 'Shipped',
                'delivered'  => 'Delivered',
                'cancelled'  => 'Cancelled',
            ];

            $orderStatus = $statusLabels[$statusKey];

            $items_stmt = $conn->prepare("
                SELECT oi.quantity, oi.unit_price,
                       (oi.quantity * oi.unit_price) AS line_total,
                       p.name, p.image
                FROM order_items oi
                JOIN products p ON oi.product_ID = p.product_ID
                WHERE oi.order_ID = ?
                ORDER BY oi.order_item_ID
            ");
            $items_stmt->bind_param("i", $order_ID);
            $items_stmt->execute();
            $result = $items_stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $line = (float)$row['line_total'];
                $unit_price = (float)$row['unit_price'];
                $subtotal += $line;
                
                $qty = $unit_price > 0 ? (int)round($line / $unit_price) : 1;
                
                $cleanItems[] = [
                    'name'  => $row['name'],
                    'qty'   => $qty,
                    'line'  => $line,
                    'image' => $row['image'] ?? ''
                ];
            }

            $items_stmt->close();
            
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;
        }
    }

} catch (Throwable $e) {
    $hasOrder = false;
}

if (
    !$hasOrder &&
    is_array($sessionOrder) &&
    !empty($sessionOrder['number']) &&
    ((int)$sessionOrder['number'] === (int)($_GET['order_id'] ?? $sessionOrder['number']))
) {
    $hasOrder = true;
    $orderNumber = (string)($sessionOrder['number'] ?? '');
    $orderDate = (string)($sessionOrder['date'] ?? $orderDate);
    $address = (string)($sessionOrder['customer']['address1'] ?? 'No address saved');
    $tax = (float)($sessionOrder['tax'] ?? 0.0);
    $currency = (string)($sessionOrder['currency'] ?? '£');

    $cleanItems = [];
    $subtotal = 0.0;

    foreach (($sessionOrder['items'] ?? []) as $item) {
        $qty = (int)($item['qty'] ?? 1);
        $price = (float)($item['price'] ?? 0.0);
        $line = $qty * $price;
        $subtotal += $line;

        $cleanItems[] = [
            'name' => $item['name'] ?? 'Product',
            'qty' => $qty,
            'line' => $line,
            'image' => $item['image'] ?? ''
        ];
    }

    $total = $subtotal + $tax;
    $orderStatus = 'Paid';
    $statusKey = 'pending';
}
?>
<?php
$pageTitle = 'Order Confirmation | LOFT &amp; LIVING';
$extraHeadContent = <<<'HTML'
<link rel="stylesheet" href="../css/orderconfirmation.css?v=1">
HTML;

include 'header.php';
?>
<div class="order-confirmation-wrap">

<?php if (!$hasOrder): ?>

    <h1>Order Confirmation</h1>
    <p>No order selected or access denied.</p>
    <a href="homepage.php" class="btn">Back to Homepage</a>

<?php else: ?>

   <div class="order-confirmation-hero">
    <h1>Thank you for your order!</h1>
    <p class="order-confirmation-subtitle">Your order has been placed successfully and we will keep you updated as it moves through each stage.</p>

    <div class="order-track">
        <div class="trackStep <?= in_array($statusKey, ['pending', 'processing', 'shipped', 'delivered'], true) ? 'active' : '' ?>">
            <div class="trackIcon">✓</div>
            <div class="trackLabel">Order<br>Confirmed</div>
        </div>

        <div class="trackLine"></div>

        <div class="trackStep <?= in_array($statusKey, ['shipped', 'delivered'], true) ? 'active' : '' ?>">
            <div class="trackIcon">🚚</div>
            <div class="trackLabel">Shipped</div>
        </div>

        <div class="trackLine"></div>

        <div class="trackStep <?= $statusKey === 'delivered' ? 'active' : '' ?>">
            <div class="trackIcon">📦</div>
            <div class="trackLabel">Delivered</div>
        </div>
    </div>
</div>

    <div class="order-confirmation-grid">

        <section class="confirmation-card">
            <div class="confirmation-card-head">Items</div>
            <div class="confirmation-card-body">

                <?php if (empty($cleanItems)): ?>
                    <div class="note">No items found in this order.</div>
                <?php else: ?>

                    <div class="confirmation-row confirmation-row-head">
                        <div></div>
                        <div>Item</div>
                        <div class="right">Qty</div>
                        <div class="right">Total</div>
                    </div>

                    <?php foreach ($cleanItems as $it): ?>
                    <div class="confirmation-row">
                        <div class="confirmation-thumb">
                            <?php if ($it['image']): ?>
                                <img src="<?= e('../images/' . ltrim((string)$it['image'], '/')) ?>" alt="<?= e($it['name']) ?>" class="confirmation-thumb-image">
                            <?php endif; ?>
                        </div>
                        <div class="confirmation-item-name"><?= e($it['name']) ?></div>
                        <div class="right"><?= $it['qty'] ?></div>
                        <div class="right"><?= e(money($it['line'], $currency)) ?></div>
                    </div>
                    <?php endforeach; ?>

                   <div class="confirmation-totals">

    <div class="confirmation-kv-row">
        <span>Subtotal</span>
        <span><?= e(money($subtotal, $currency)) ?></span>
    </div>

    <div class="confirmation-kv-row">
        <span>Tax (10%)</span>
        <span><?= e(money($tax, $currency)) ?></span>
    </div>

    <div class="confirmation-grand">
        <span>Total</span>
        <span><?= e(money($total, $currency)) ?></span>
    </div>

</div>

                    <div class="confirmation-actions">
                        <a href="homepage.php" class="confirmation-btn confirmation-btn-ghost">Continue shopping</a>
                    </div>

                <?php endif; ?>

            </div>
        </section>

        <aside class="confirmation-card">
            <div class="confirmation-card-head">Order details</div>
            <div class="confirmation-card-body">

                <div class="confirmation-kv-row">
                    <strong>Order #</strong>
                    <span><?= e($orderNumber) ?></span>
                </div>

                <div class="confirmation-kv-row">
                    <strong>Date</strong>
                    <span><?= e($orderDate) ?></span>
                </div>

                <div class="confirmation-kv-row">
                    <strong>Status</strong>
                    <span><?= e($orderStatus) ?></span>
                </div>

                <div class="confirmation-address-block">
                    <div class="confirmation-card-head confirmation-card-head-inner">Delivery address</div>
                    <div class="confirmation-card-body confirmation-card-body-inner">
                        <div class="confirmation-addr"><?= nl2br(e($address)) ?></div>
                    </div>
                </div>

            </div>
        </aside>

    </div>

<?php endif; ?>

</div>
<?php include 'footer.php'; ?>
