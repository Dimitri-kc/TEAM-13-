<?php
/**
 * Order Item Routes
 */

// Set JSON response header
header("Content-Type: application/json");

// Load the Order Items Controller
// The controller handles insert, fetch, delete, and update via ?action=
include_once __DIR__ . '/../controllers/orderItemsController.php';

?>
