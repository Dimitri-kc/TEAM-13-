<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();


function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function money(float $n, string $currency = "£"): string {
  return $currency . number_format($n, 2);
}

function as_float($v, float $default = 0.0): float {
  return (is_numeric($v)) ? (float)$v : $default;
}

function as_positive_int($v, int $default = 1): int {
  if (is_int($v)) return max(1, $v);
  if (is_string($v) && preg_match('/^\d+$/', $v)) return max(1, (int)$v);
  if (is_float($v)) return max(1, (int)$v);
  return $default;
}

function safe_continue_url(string $url, string $fallback = "index.php"): string {
  $url = trim($url);
  if ($url === "") return $fallback;

  
  $isRelative = str_starts_with($url, "/") || !preg_match('~^[a-zA-Z][a-zA-Z0-9+.-]*:~', $url);
  $isHttp = (bool)preg_match('~^https?://~i', $url);

  return ($isRelative || $isHttp) ? $url : $fallback;
}


$order = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $order = [
    "currency" => (string)($_POST["currency"] ?? "£"),
    "number" => (string)($_POST["number"] ?? ($_POST["order_number"] ?? "")),
    "date" => (string)($_POST["date"] ?? ""),
    "payment_method" => (string)($_POST["payment_method"] ?? ""),
    "email" => (string)($_POST["email"] ?? ""),
    "shipping" => $_POST["shipping"] ?? 0,
    "tax" => $_POST["tax"] ?? 0,
    "discount" => $_POST["discount"] ?? 0,
    "continue_url" => (string)($_POST["continue_url"] ?? "index.php"),
    "customer" => (is_array($_POST["customer"] ?? null) ? $_POST["customer"] : []),
    "items" => (is_array($_POST["items"] ?? null) ? $_POST["items"] : []),
  ];

 
  $_SESSION["order"] = $order;

} elseif (isset($_SESSION["order"]) && is_array($_SESSION["order"])) {
  $order = $_SESSION["order"];
}

$hasOrder = is_array($order);


$currency = $hasOrder ? (string)($order["currency"] ?? "£") : "£";
$orderNumber = $hasOrder ? (string)($order["number"] ?? "") : "";
$orderDate = $hasOrder ? (string)($order["date"] ?? "") : "";
$payment = $hasOrder ? (string)($order["payment_method"] ?? "") : "";

if ($orderDate === "") $orderDate = date("F j, Y");
if ($hasOrder && $orderNumber === "") $orderNumber = (string)random_int(100000, 999999);

$continueUrl = safe_continue_url($hasOrder ? (string)($order["continue_url"] ?? "index.php") : "index.php", "index.php");

$customer = ($hasOrder && isset($order["customer"]) && is_array($order["customer"])) ? $order["customer"] : [];
$cName     = (string)($customer["name"] ?? "");
$cEmail    = (string)($customer["email"] ?? ($order["email"] ?? ""));
$cPhone    = (string)($customer["phone"] ?? "");
$cAddress1 = (string)($customer["address1"] ?? "");
$cAddress2 = (string)($customer["address2"] ?? "");
$cCity     = (string)($customer["city"] ?? "");
$cState    = (string)($customer["state"] ?? "");
$cPostcode = (string)($customer["postcode"] ?? "");


$items = ($hasOrder && isset($order["items"]) && is_array($order["items"])) ? $order["items"] : [];

$shipping = $hasOrder ? as_float($order["shipping"] ?? 0) : 0.0;
$tax      = $hasOrder ? as_float($order["tax"] ?? 0) : 0.0;
$discount = $hasOrder ? as_float($order["discount"] ?? 0) : 0.0;

$subtotal = 0.0;
$cleanItems = [];

foreach ($items as $it) {
  if (!is_array($it)) continue;

  $name = trim((string)($it["name"] ?? ""));
  if ($name === "") continue;

  $qty = as_positive_int($it["qty"] ?? 1, 1);
  $price = as_float($it["price"] ?? 0);

  $line = $qty * $price;
  $subtotal += $line;

  $cleanItems[] = [
    "name" => $name,
    "qty" => $qty,
    "price" => $price,
    "line" => $line,
    "sku" => (string)($it["sku"] ?? ""),
    "variant" => (string)($it["variant"] ?? ""),
  ];
}

$total = max(0.0, $subtotal + $shipping + $tax - $discount);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Order Confirmation</title>

  <style>
    :root{
      --bg:#ffffff;
      --text:#0b0b0b;
      --muted:#555;
      --line:#e9e9e9;
      --card:#ffffff;
      --shadow: 0 12px 28px rgba(0,0,0,.06);
      --radius: 16px;
      --btn:#000;
      --btnText:#fff;
    }

    body{
      margin:0;
      background:var(--bg);
      color:var(--text);
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      line-height:1.45;
    }

    .wrap{
      width:min(980px, 92vw);
      margin: 28px auto 50px;
    }

    
    .empty h1{
      margin:0;
      font-size: clamp(30px, 3vw, 44px);
      letter-spacing:-.02em;
    }
    .empty p{
      margin:8px 0 18px;
      color: var(--muted);
    }
    .empty-box{
      border:1px dashed #d9d9d9;
      border-radius: 14px;
      padding: 18px 16px;
      color: #555;
      background:#fff;
      display:flex;
      justify-content:center;
      align-items:center;
      text-align:center;
      min-height: 74px;
    }
    .empty a{
      display:inline-block;
      margin-top: 14px;
      color:#000;
      text-decoration: underline;
      font-weight: 500;
    }

    
    .hero{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      padding: 18px 0 22px;
      border-bottom:1px solid var(--line);
      margin-bottom:22px;
    }
    .hero h1{ margin:0; font-size: clamp(24px, 3vw, 36px); letter-spacing:-.02em; }
    .hero p{ margin:8px 0 0; color:var(--muted); }

    .badge{
      border:1px solid var(--line);
      border-radius: 999px;
      padding: 10px 14px;
      font-weight:700;
      color:#111;
      white-space:nowrap;
    }

    .grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:16px; }
    @media (max-width: 860px){ .grid{ grid-template-columns: 1fr; } }

    .card{
      background:var(--card);
      border:1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow:hidden;
    }
    .cardHead{ padding:14px 16px; border-bottom:1px solid var(--line); font-weight:800; }
    .cardBody{ padding: 14px 16px 16px; }

    .table{ border:1px solid var(--line); border-radius: 14px; overflow:hidden; }
    .row{
      display:grid;
      grid-template-columns: 1fr 110px 140px;
      gap:10px;
      padding: 12px 14px;
      border-top:1px solid var(--line);
      align-items:center;
    }
    .row:first-child{ border-top:0; }
    .head{ background:#fafafa; font-weight:800; color:#111; }
    .right{ text-align:right; }
    .meta{ font-size: 12.5px; color: var(--muted); margin-top: 4px; }

    .totals{
      margin-top:12px; border-top:1px solid var(--line);
      padding-top: 12px; display:grid; gap:8px; color:var(--muted);
    }
    .totRow{ display:flex; justify-content:space-between; gap:12px; }
    .grand{
      margin-top:6px; padding-top:12px; border-top:1px solid var(--line);
      color:#111; font-weight:900; font-size: 18px;
    }

    .kv{ display:grid; gap:10px; }
    .kvRow{
      display:flex; justify-content:space-between; gap:12px;
      padding: 10px 0; border-bottom:1px dashed var(--line);
    }
    .kvRow:last-child{ border-bottom:0; }
    .k{ color:#111; font-weight:700; }
    .v{ color:var(--muted); text-align:right; }
    .v strong{ color:#111; }

    .addr{ color:var(--muted); line-height:1.6; }
    .addr strong{ color:#111; }

    .actions{ display:flex; flex-wrap:wrap; gap:10px; margin-top: 14px; }
    .btn{
      display:inline-flex; align-items:center; justify-content:center;
      padding: 11px 14px; border-radius: 12px;
      background: var(--btn); color: var(--btnText);
      text-decoration:none; font-weight:800; border:1px solid #000; cursor:pointer;
    }
    .btn:hover{ filter: brightness(.92); }
    .btnGhost{ background: transparent; color: #000; }
    .note{
      border:1px solid var(--line);
      border-radius: var(--radius);
      padding: 14px 16px;
      color: var(--muted);
    }
  </style>
</head>

<body>
  <div class="wrap">

    <?php if (!$hasOrder): ?>
      <section class="empty">
        <h1>Order Confirmation</h1>
        <p>Place an order and you’ll see your receipt here</p>

        <div class="empty-box">
          No orders yet. Go to categories and start ordering.
        </div>

       <div class="actions"><a href="homepage.php">Back to Homepage</a></div>
      </section>

    <?php else: ?>

      <div class="hero">
        <div>
          <h1>Order confirmed</h1>
          <p>Thanks — we’ve received your order. Your receipt is below.</p>
        </div>
        <div class="badge">Order #<?= e($orderNumber) ?></div>
      </div>

      <div class="grid">
        <section class="card">
          <div class="cardHead">Items</div>
          <div class="cardBody">

            <?php if (count($cleanItems) === 0): ?>
              <div class="note">No items were found in this order.</div>
            <?php else: ?>

              <div class="table">
                <div class="row head">
                  <div>Item</div>
                  <div class="right">Qty</div>
                  <div class="right">Line total</div>
                </div>

                <?php foreach ($cleanItems as $it): ?>
                  <div class="row">
                    <div>
                      <div><strong><?= e($it["name"]) ?></strong></div>
                      <?php if ($it["sku"] !== "" || $it["variant"] !== ""): ?>
                        <div class="meta">
                          <?= $it["sku"] !== "" ? ("SKU: " . e($it["sku"])) : "" ?>
                          <?= ($it["sku"] !== "" && $it["variant"] !== "") ? " · " : "" ?>
                          <?= $it["variant"] !== "" ? e($it["variant"]) : "" ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="right"><?= (int)$it["qty"] ?></div>
                    <div class="right"><?= e(money((float)$it["line"], $currency)) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="totals">
                <div class="totRow"><span>Subtotal</span><span><?= e(money($subtotal, $currency)) ?></span></div>
                <?php if ($shipping > 0): ?>
                  <div class="totRow"><span>Shipping</span><span><?= e(money($shipping, $currency)) ?></span></div>
                <?php endif; ?>
                <?php if ($tax > 0): ?>
                  <div class="totRow"><span>Tax</span><span><?= e(money($tax, $currency)) ?></span></div>
                <?php endif; ?>
                <?php if ($discount > 0): ?>
                  <div class="totRow"><span>Discount</span><span>-<?= e(money($discount, $currency)) ?></span></div>
                <?php endif; ?>
                <div class="totRow grand"><span>Total</span><span><?= e(money($total, $currency)) ?></span></div>
              </div>

              <div class="actions">
                <a class="btn" href="#" onclick="window.print();return false;">Print receipt</a>
                <a class="btn btnGhost" href="<?= e($continueUrl) ?>">Continue shopping</a>
              </div>

            <?php endif; ?>
          </div>
        </section>

        <aside class="card">
          <div class="cardHead">Order details</div>
          <div class="cardBody">
            <div class="kv">
              <div class="kvRow">
                <div class="k">Order #</div>
                <div class="v"><strong><?= e($orderNumber) ?></strong></div>
              </div>
              <div class="kvRow">
                <div class="k">Date</div>
                <div class="v"><?= e($orderDate) ?></div>
              </div>
              <?php if ($payment !== ""): ?>
                <div class="kvRow">
                  <div class="k">Payment</div>
                  <div class="v"><?= e($payment) ?></div>
                </div>
              <?php endif; ?>
              <?php if ($cEmail !== ""): ?>
                <div class="kvRow">
                  <div class="k">Email</div>
                  <div class="v"><?= e($cEmail) ?></div>
                </div>
              <?php endif; ?>
            </div>

            <div style="height:14px"></div>

            <div class="card" style="box-shadow:none;">
              <div class="cardHead" style="border-bottom:1px solid var(--line);">Delivery address</div>
              <div class="cardBody">
                <div class="addr">
                  <?php if ($cName !== ""): ?><strong><?= e($cName) ?></strong><br><?php endif; ?>
                  <?php if ($cAddress1 !== ""): ?><?= e($cAddress1) ?><br><?php endif; ?>
                  <?php if ($cAddress2 !== ""): ?><?= e($cAddress2) ?><br><?php endif; ?>
                  <?php if ($cCity !== "" || $cState !== "" || $cPostcode !== ""): ?>
                    <?= e(trim($cCity . ($cCity && $cState ? ", " : "") . $cState)) ?>
                    <?= $cPostcode !== "" ? (" " . e($cPostcode)) : "" ?><br>
                  <?php endif; ?>
                  <?php if ($cPhone !== ""): ?><span class="meta">Phone: <?= e($cPhone) ?></span><?php endif; ?>
                </div>
              </div>
            </div>

          </div>
        </aside>
      </div>

    <?php endif; ?>

  </div>
</body>
</html>