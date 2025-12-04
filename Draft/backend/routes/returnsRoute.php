<?php
// returnRoutes (integrates returns and return items)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once '../../controllers/ReturnsController.php';
include_once '../../controllers/ReturnItemController.php';

$returnsController = new ReturnsController();
$returnItemController = new ReturnItemController(); 

// (getProductId helper function remains the same)

// Handle POST Requests (Create/Update/Delete) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Action and ID parsing remains the same

switch ($action) {
case 'store': 
// Create a new return request (User action)
// Get User Input (Header and Items)
$orderID = isset($_POST['order_id']) ? (int)$_POST['order_id'] : null;
$userID = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; 
$reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : null;
$status_default = 'Requested';

// Get the array of item details from the form
$items_to_return = isset($_POST['return_items']) ? $_POST['return_items'] : []; 

if ($orderID && $userID && !empty($items_to_return)) {
                                
// Create the main return header record
$new_return_id = $returnsController->store($orderID, $userID, $reason, $status_default);
                                
if ($new_return_id) {
                                        
// Insert the line items into the return_items table
$items_success = $returnItemController->store($new_return_id, $items_to_return);
                    
if ($items_success) {
// Success: Both header and items were inserted
echo "Return request completed successfully (ID: " . $new_return_id . ")";
} else {
// Failure inserting items: CLEANUP IS NEEDED
$returnsController->destroy($new_return_id); 
echo "Error: Return header created, but failed to add items. Transaction rolled back.";
}
} else {
echo "Error: Failed to initiate return request header.";
}
} else {
echo "Error: Missing required order info or no items selected for return.";
}
break;
}
}
?>