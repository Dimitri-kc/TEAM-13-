<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

function e(string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
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
?>
<?php
$pageTitle = 'Order Confirmation | LOFT &amp; LIVING';
$extraHeadContent = <<<'HTML'
<style>
:root {
    --bg:#ffffff; --text:#0b0b0b; --muted:#555; --line:#e9e9e9;
    --card:#ffffff; --shadow:0 12px 28px rgba(0,0,0,.06); --radius:16px;
    --btn:#000; --btnText:#fff;
}
body { margin:0; background:var(--bg); color:var(--text); font-family:system-ui,sans-serif; line-height:1.45; }
.wrap { width:min(980px,92vw); margin:28px auto 50px; }
.hero { padding:18px 0 28px; border-bottom:1px solid var(--line); margin-bottom:22px; }
.hero h1 { margin:0; font-size:clamp(24px,3vw,36px); letter-spacing:-.02em; }
.grid { display:grid; grid-template-columns:1.1fr .9fr; gap:16px; }
@media (max-width:860px) { .grid { grid-template-columns:1fr; } }
.card { background:var(--card); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.cardHead { padding:14px 16px; border-bottom:1px solid var(--line); font-weight:800; }
.cardBody { padding:14px 16px 16px; }
.row { display:grid; grid-template-columns:80px 1fr 110px 140px; gap:10px; padding:12px 14px; border-top:1px solid var(--line); align-items:center; }
.row:first-child { border-top:0; }
.head { background:#fafafa; font-weight:800; }
.right { text-align:right; }
.totals { margin-top:12px; border-top:1px solid var(--line); padding-top:12px; }
.grand { display:flex; justify-content:space-between; font-weight:900; font-size:18px; }
.kvRow { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px dashed var(--line); }
.kvRow:last-child { border-bottom:0; }
.addr { color:var(--muted); line-height:1.6; white-space:pre-line; }
.actions { margin-top:14px; }
.btn { display:inline-flex; padding:11px 14px; border-radius:12px; background:var(--btn); color:var(--btnText); text-decoration:none; font-weight:800; border:1px solid #000; }
.btnGhost { background:transparent; color:#000; }
.note { border:1px solid var(--line); border-radius:var(--radius); padding:14px; color:var(--muted); text-align:center; }
.track {
    display:flex;
    justify-content:center;
    align-items:flex-start;
    gap:0;
    margin-top:18px;
}

.trackStep {
    display:flex;
    flex-direction:column;
    align-items:center;
    min-width:120px;
    position:relative;
}

.trackIcon {
    width:34px;
    height:34px;
    border-radius:50%;
    border:2px solid #d9d9d9;
    background:#fff;
    color:#999;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    font-weight:700;
    z-index:2;
}

.trackStep.active .trackIcon {
    background:#8bc34a;
    border-color:#8bc34a;
    color:#fff;
}

.trackLine {
    width:70px;
    height:2px;
    background:#d9d9d9;
    margin-top:16px;
}

.trackLabel {
    margin-top:8px;
    text-align:center;
    font-size:14px;
    color:#999;
    font-weight:600;
    line-height:1.25;
}

.trackStep.active .trackLabel {
    color:#222;
}
</style>
HTML;

include 'header.php';
?>
<div class="wrap">

<?php if (!$hasOrder): ?>

    <h1>Order Confirmation</h1>
    <p>No order selected or access denied.</p>
    <a href="homepage.php" class="btn">Back to Homepage</a>

<?php else: ?>

   <div class="hero">
    <h1>Thank you for your order!</h1>

    <div class="track">
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

    <div class="grid">

        <section class="card">
            <div class="cardHead">Items</div>
            <div class="cardBody">

                <?php if (empty($cleanItems)): ?>
                    <div class="note">No items found in this order.</div>
                <?php else: ?>

                    <div class="row head">
                        <div></div>
                        <div>Item</div>
                        <div class="right">Qty</div>
                        <div class="right">Total</div>
                    </div>

                    <?php foreach ($cleanItems as $it): ?>
                    <div class="row">
                        <div>
                            <?php if ($it['image']): ?>
                                <img src="<?= e($it['image']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                            <?php endif; ?>
                        </div>
                        <div><strong><?= e($it['name']) ?></strong></div>
                        <div class="right"><?= $it['qty'] ?></div>
                        <div class="right"><?= e(money($it['line'], $currency)) ?></div>
                    </div>
                    <?php endforeach; ?>

                   <div class="totals">

    <div class="kvRow">
        <span>Subtotal</span>
        <span><?= e(money($subtotal, $currency)) ?></span>
    </div>

    <div class="kvRow">
        <span>Tax (10%)</span>
        <span><?= e(money($tax, $currency)) ?></span>
    </div>

    <div class="grand">
        <span>Total</span>
        <span><?= e(money($total, $currency)) ?></span>
    </div>

</div>

                    <div class="actions">
                        <a href="homepage.php" class="btn btnGhost">Continue shopping</a>
                    </div>

                <?php endif; ?>

            </div>
        </section>

        <aside class="card">
            <div class="cardHead">Order details</div>
            <div class="cardBody">

                <div class="kvRow">
                    <strong>Order #</strong>
                    <span><?= e($orderNumber) ?></span>
                </div>

                <div class="kvRow">
                    <strong>Date</strong>
                    <span><?= e($orderDate) ?></span>
                </div>

                <div class="kvRow">
                    <strong>Status</strong>
                    <span><?= e($orderStatus) ?></span>
                </div>

                <div style="margin-top:14px;">
                    <div class="cardHead" style="box-shadow:none;">Delivery address</div>
                    <div class="cardBody">
                        <div class="addr"><?= nl2br(e($address)) ?></div>
                    </div>
                </div>

            </div>
        </aside>

    </div>

<?php endif; ?>

</div>
<?php include 'footer.php'; ?>