<?php
/**
 * Reviews Routes
 */

// Correct header syntax
header('Content-Type: application/json');

// Load the Reviews Controller (matches the actual filename)
include_once __DIR__ . '/../controllers/reviewsController.php';

// Controller handles: ?action=insert, fetch, update, delete
?>
