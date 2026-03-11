<?php
session_start();
include '../backend/config/db_connect.php';

$redirect = $_POST['redirect'] ?? 'favourites.php';
$isAjaxRequest = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

function send_toggle_response(array $payload, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

if (!isset($_SESSION['user_ID'])) {
    if ($isAjaxRequest) {
        send_toggle_response([
            'success' => false,
            'message' => 'Please sign in to save favourites.',
            'redirect' => 'signin.php'
        ], 401);
    }

    header('Location: signin.php');
    exit;
}

$userId = (int) $_SESSION['user_ID'];
$productId = (int) ($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    if ($isAjaxRequest) {
        send_toggle_response([
            'success' => false,
            'message' => 'Invalid product selected.'
        ], 422);
    }

    header("Location: $redirect");
    exit;
}

$checkStatement = $conn->prepare('SELECT 1 FROM favourites WHERE user_ID = ? AND product_ID = ? LIMIT 1');
$checkStatement->bind_param('ii', $userId, $productId);
$checkStatement->execute();
$isFavourite = (bool) $checkStatement->get_result()->fetch_row();
$checkStatement->close();

if ($isFavourite) {
    $statement = $conn->prepare('DELETE FROM favourites WHERE user_ID = ? AND product_ID = ?');
    $statement->bind_param('ii', $userId, $productId);
    $statement->execute();
    $statement->close();

    $payload = [
        'success' => true,
        'isFavourite' => false,
        'message' => 'Removed from favourites.'
    ];
} else {
    $statement = $conn->prepare('INSERT INTO favourites (user_ID, product_ID) VALUES (?, ?)');
    $statement->bind_param('ii', $userId, $productId);
    $statement->execute();
    $statement->close();

    $payload = [
        'success' => true,
        'isFavourite' => true,
        'message' => 'Added to favourites.'
    ];
}

if ($isAjaxRequest) {
    send_toggle_response($payload);
}

header("Location: $redirect");
exit;
