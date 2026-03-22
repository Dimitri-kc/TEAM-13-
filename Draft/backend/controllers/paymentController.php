
<?php //paymentController.php - process requests from checkout.html

//STILL HAS OLD CODE FROM BEFORE JSON REFACTOR, NEEDS TO BE UPDATED TO MATCH NEW STRUCTURE

session_start();
//include database and user models so controller can connect to database and use user methods
require_once __DIR__ . '/../config/db_connect.php';//state file path
require_once __DIR__ . '/../models/paymentModel.php'; //call payment database functions
require_once __DIR__ . '/../models/orderItemsModel.php'; //call order 
require_once __DIR__ . '/../services/paymentFunctions.php'; //for payment functions
require_once __DIR__ . '/../services/basketFunctions.php'; //for checkout helpers and basket merger

class PaymentController {
    
    public function pay(){
        //collect data from checkout.html form
        $user_ID = $_SESSION['user_ID'] ?? null; //null id if guest session
        $order_ID = trim($_POST['order_ID'] ?? ''); //trim to remove whitespace
        $address = trim($_POST['address'] ?? '');
        $cardNumber = trim($_POST['card_number'] ?? '');
        $expiry = trim($_POST['expiry'] ?? '');
        $cvv = trim($_POST['cvv'] ?? '');

        if(!$order_ID || !$address || !$cardNumber || !$expiry || !$cvv) { //if details not present...
            echo 'All fields  are required.';
            exit();
        }

        //validation of payment details
        $paymentModel = new Payment();
        if (!$paymentModel-> validatePayment($cardNumber, $expiry, $cvv)) {
            echo 'Invalid payment details. Please check submitted card details.';
            return false;
        }

        //fetch payment via order id
        $orderItemModel = new OrderItemsModel();
        if (isset($user_ID)) { //if user logged in
            $orderItems = $orderItemModel->getItemsByOrder($order_ID) ?? [];
        } else {
            $orderItems = getSessionBasket(); //if guest, fetch session id basket
        }

        if(!$orderItems) {
            echo 'No items found.';
            exit;
        }
        $total_sum = basket_total($orderItems);
        $success = $paymentModel->createPayment($order_ID, $user_ID, $address, $total_sum); //create record of payment
        if ($success) {
            if(!isset($user_ID)) {
                clearSessionBasket();//call clear guest basket
            }
            header ('Location: /orderconfirmation.php');//if payment successful direct to confirmation
            exit;
        } else {
            echo 'Payment failed. Please try again.';
            exit;
        }
    }
    

    public function viewPayments() {
        if (!$_SESSION['user_ID']) {//must be logged in to access payment history
            echo 'Access denied. Please login to view payments.';
            exit;
        }

        $user_ID = $_SESSION['user_ID'];
        $paymentModel = new Payment();
        $payments = $paymentModel->fetchUserPayments($user_ID);
        return $payments; //>for payment page in user profile
    }
}
//Notes:
//For both registered users and guests
//basket totalS retrieved from basketFunctions.php
//redirects to orderconfirmation.php upon payment success
?>