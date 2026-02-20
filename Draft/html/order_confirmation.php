<?php
// checkout-success.php
declare(strict_types=1);
session_start();

function generateOrderId(): string {
  return "ORD-" . strtoupper(dechex(time())) . "-" . strtoupper(bin2hex(random_bytes(3)));
}

// EXAMPLE cart structure in session:
// $_SESSION['cart'] = [
//   ['name'=>'Product 1','sku'=>'SKU1','variant'=>'Size: M','quantity'=>1,'price'=>19.99,'imageUrl'=>''],
//   ['name'=>'Product 2','sku'=>'SKU2','variant'=>'Color: Black','quantity'=>2,'price'=>9.50,'imageUrl'=>''],
// ];

// If you don't have cart yet, here's a dummy cart for testing:
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [
    ['name'=>'Example Product 1','sku'=>'SKU-001','variant'=>'Size: M','quantity'=>1,'price'=>19.99,'imageUrl'=>''],
    ['name'=>'Example Product 2','sku'=>'SKU-002','variant'=>'Color: Black','quantity'=>2,'price'=>9.50,'imageUrl'=>''],
  ];
}

$cart = $_SESSION['cart'];
if (!is_array($cart) || count($cart) === 0) {
  echo "Cart is empty.";
  exit;
}

$orderId = generateOrderId();

// In real usage, these come from your checkout form:
$customer = ['firstName' => 'FirstName', 'lastName' => 'LastName'];
$shippingAddress = [
  'firstName' => 'FirstName',
  'lastName'  => 'LastName',
  'line1'     => 'Address Line 1',
  'line2'     => 'Address Line 2',
  'postcode'  => 'Postcode',
  'county'    => 'County',
  'country'   => 'Country'
];

$order = [
  'orderId' => $orderId,
  'createdAt' => date('c'),
  'currency' => 'GBP',
  'status' => 'PLACED',
  'customer' => $customer,
  'shippingAddress' => $shippingAddress,
  'shippingCost' => 4.99,
  'tax' => 0.00,
  'items' => []
];

foreach ($cart as $i => $p) {
  $order['items'][] = [
    'lineId' => 'line_' . ($i + 1),
    'name' => (string)($p['name'] ?? 'Item'),
    'sku' => (string)($p['sku'] ?? ''),
    'variant' => (string)($p['variant'] ?? ''),
    'quantity' => (int)($p['quantity'] ?? 1),
    'unitPrice' => (float)($p['price'] ?? 0),
    'imageUrl' => (string)($p['imageUrl'] ?? ''),
    'status' => 'PLACED'
  ];
}

// Store all orders in session
$_SESSION['orders'] ??= [];
$_SESSION['orders'][$orderId] = $order;

// Clear cart after order
unset($_SESSION['cart']);

// Redirect to details page
header("Location: orderdetails.php?orderId=" . urlencode($orderId));
exit;

?>