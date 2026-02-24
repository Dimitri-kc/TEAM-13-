<?php //checkoutRoutes.php > JSON API for all checkout process
//Flow: basket > create order + order_items > payment > finalise order

ini_set('display_errors', 1); //t
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: applicaiton/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//Controller call
require_once __DIR__ . '/../controllers/checkoutController.php';

$checkoutController = new CheckoutController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $raw = file_get_contents("php://input"); //get raw POST data
    $data = json_decode($raw, true); //decode json to array
    if (!$data || empty($data['action'])) { //if no data/action specified, return msg
        echo json_encode(['success' => false, 'message' => "No action specified.", 'data' => null]);
        exit;
    }
    //sanitise action input for security -> XSS prevention
    $action = is_string($data['action']) ? htmlspecialchars($data['action']) : '';

    switch ($action) {
        case 'checkout': 
            $checkoutController->processCheckout($data); //call processCheckout method in controller
            break;

        case 'quote':
            $checkoutController->getQuote($data);
            break;

        default:
            echo json_encode(['success' => false, 'message' => "Invalid action.", 'data' => null]);
            break;
    }
    exit; 
} 

echo json_encode(['success' => false, 'message' => 'Invalid request method. Use POST.', 'data' => null]);


/* Notes: 
ADded new flow with checkout for basket/order/payment harmony
Flow breakdwon: 
1. Require user logged in (MAY add guest checkout implementation)
2. Pull basket items (from basket/session logic)
3. Compute totals + validate stock
4. Create order (status Pending)
5. Insert all order_items (with quantity — need Omar to adjust DB table)
6. Create payment record (paymentModel)
7. If payment success: mark order as Paid  > clear basket */
?>