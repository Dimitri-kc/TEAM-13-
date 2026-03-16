<?php
include '../backend/config/db_connect.php';
session_start();

if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_ID'];

$stmt = $conn->prepare("DELETE FROM favourites WHERE user_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

header("Location: favourites.php");
exit;