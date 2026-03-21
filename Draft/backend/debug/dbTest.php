<?php //specifically for testing DB connection inside backend

$mysqli = new mysqli("localhost", "root", "", "cs2team13_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully!";
?>

<!-- <?php
require 'db.php';
echo "Database connected successfully!";
?> -->