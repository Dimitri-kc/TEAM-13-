<?php

function ensureOrderItemStatusColumn(mysqli $conn): void
{
    $result = $conn->query("SHOW COLUMNS FROM order_items LIKE 'item_status'");

    if ($result instanceof mysqli_result && $result->num_rows > 0) {
        return;
    }

    $conn->query("
        ALTER TABLE order_items
        ADD COLUMN item_status VARCHAR(50) NOT NULL DEFAULT 'Active'
    ");
}

?>
