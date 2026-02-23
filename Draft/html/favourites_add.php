<?php
session_start();
require __DIR__ . "/db.php";

if (!isset($_SESSION["user_id"])) { http_response_code(401); exit("Not logged in."); }
$userId = (int)$_SESSION["user_id"];

$productId = filter_input(INPUT_POST, "product_id", FILTER_VALIDATE_INT);
$redirect = $_POST["redirect"] ?? "favorites.php";

if ($productId) {
  // insert-ignore pattern via PRIMARY KEY (user_id, product_id)
  $stmt = $pdo->prepare("INSERT IGNORE INTO favourites (user_id, product_id) VALUES (?, ?)");
  $stmt->execute([$userId, $productId]);
}

header("Location: " . $redirect);
exit;


