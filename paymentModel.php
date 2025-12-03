<?php //paymentModel.php - database comms for payments
//creates and retrieves payment records

class Payment {
    private $conn;

        public function __construct() {
        include_once __DIR__ . '/../config/db_connect.php'; // database connection
        include_once __DIR__ . '/../models/productModel.php'; //some overlap with product/order
        include_once __DIR__ . '/../models/orderModel.php';
        $this->conn = $dbConnection;
    }

    //validate input
    public function validatePayment($cardNumber, $expiry, $cvv) {
        if(strlen($cardNumber) !== 16) { //checks against length - luhn algorithm to be implemented
            return false;
        }
        if (!preg_match('/^[0-9]{3}$/', $cvv)) { //checks for 3 digits between 0-9
            return false;
        }
        if(!preg_match('/^(0[1-9]|1[1-2])\/[0-9]{2}$/', $expiry)) { //expiry must be in MM/YY format
            return false;
        }
        return true;
    }

    //create record of payment 
    public function createPayment($order_ID, $user_ID, $address, $total_sum) {
        //add values to payments table
        $stmt = $this->conn->prepare("INSERT INTO payments (order_ID, user_ID, address, total_sum) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("i", $order_ID); //attach value prevent SQLinjection
        $stmt->bind_param('i', $user_ID);
        $stmt->bind_param("s", $address);
        $stmt->bind_param("d", $total_sum);//double for decimal

        return $stmt->execute($order_ID, $user_ID, $address, $total_sum);
    }

    //fetches payment using order_ID
    public function fetchPaymentOrderId($order_ID) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE order_ID = ?");
        $stmt->bind_param("i", $order_ID);
        $stmt->execute($order_ID);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //fetch payment via payment_ID
    public function fetchPaymentID($payment_ID) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE payment_ID = ?");
        $stmt->bind_param("i", $payment_ID);
        $stmt->execute($payment_ID);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    //fetch all payments via user_ID
    public function fetchUserPayments($user_ID) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute($user_ID);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    //update order status (& reduce stock??)

    //store billing/shipping address info
    public function storeAddress($paymentID, $address){
        $stmt = $this->conn->prepare("UPDATE payments SET address = ? WHERE payment_ID = ?");

        $stmt->bind_param("s", $address);

    }

    //delete payment > used by admin & for testing
    public function deletePayment($payment_ID) {
        $stmt = $this->conn->prepare("DELETE FROM payments WHERE payment_ID = ?");
        $stmt->bind_param("i", $payment_ID);
        return $stmt->execute($payment_ID);
    }
    

}
//Notes:
//stores payment info, links payment to basket/order
//already have basket_total() function & clearSessionBasket() in basketFunctions
//clear session if guest checkout >  $_SESSION['guest_basket']
//POST request sent to > paymentRoutes.php?action=pay

//TO DO:
//add luhn algorithm for realism to fake validate card
//may need to add created_at and status to payment sql and include in createPayment

?>
