<?php
session_start();
require __DIR__ . "/db.php";

if (!isset($_SESSION["user_id"])) { http_response_code(401); exit("Not logged in."); }
$userId = (int)$_SESSION["user_id"];

$productId = filter_input(INPUT_POST, "product_id", FILTER_VALIDATE_INT);
$redirect = $_POST["redirect"] ?? "favourites.php";

if ($productId) {
  $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
  $stmt->execute([$userId, $productId]);
}

header("Location: " . $redirect);
exit;
