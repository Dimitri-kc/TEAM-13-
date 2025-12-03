<?php //payment routes > for POST requests from checkout.html
// > GET for payment history

session_start();
include_once '../../controllers/paymentController.php';

$paymentController = new PaymentController(); //instance of payment controller

//Define routes for payment-related actions
//if POST request then check action (checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    //checking for any hidden input
    $action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : ''; //get action from form input

    switch ($action) {
        case 'pay': //checkout form submission
            $paymentController->pay();
            break;

            default:
            echo 'Invalid POST action.';
            break;
        }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    //checking for any hidden input
    $action = isset($_POST['action']) ? htmlspecialchars($_GET['action']) : ''; //get action from form input

    switch($action) {
        case 'view': //payment histor > users only
            $payments = $paymentController->viewPayments();
            break;

            default:
            echo "Invalid GET action.";
            break;
        }
}
?>