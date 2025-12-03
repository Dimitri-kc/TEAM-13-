<?php //paymentController.php - process requests from checkout.html

session_start();
//include database and user models so controller can connect to database and use user methods
include_once '../../config/db_connect.php';//state file path
include_once '../../services/paymentFunctions.php'; //for merger basket functions
include_once '../../services/basketFunctions.php'; //for checkout helpers and basket merger

class PaymentController {
    
}
?>