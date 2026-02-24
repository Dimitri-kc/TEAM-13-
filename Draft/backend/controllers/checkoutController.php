<?php //checoutController.php > checkout flow

require_once __DIR__. '/../config/db_connect.php'; //DB conncetion
require_once __DIR__ . '/../models/basketModel.php'; //for basket data
require_once __DIR__ . '/../services/basketFunctions.php'; //for guest checkout
require_once __DIR__ . '/../models/orderModel.php'; //order processing creation
require_once __DIR__ . '/../models/orderItemsModel.php'; //order items creation

class CheckoutController {
    private $basketModel; 
    private $orderModel;
    private $orderItemModel;

    public function __construct() {
        $this->basketModel = new Basket();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItem();
    }

    public function processCheckout(array $data): void {
        global $conn;

        //validate user session - guest checkout to be added later
        $user_ID = $_SESSION['user_ID'] ?? null; //get user id
        if (!$user_ID) { //if no user, return error
            echo json_encode(['success' => false, 'message' => "User not logged in. Please login to checkout.", 'data' => null]);
            return;
        }

        //merge any guest basket into database basket upon checkout
        mergeSessionBasketToUser((int)$user_ID);
        $address = trim($data['address'] ?? ''); //get address
        $cardNumber = preg_replace('/\s+/', '', (string)($data['card_number'] ?? '')); //get cardNumber & remove spaces
        $expiry = trim($_POST['expiry'] ?? ''); //get expiry
        $cvv = trim($_POST['cvv'] ?? ''); //get cvv

        if (!$address || !$cardNumber || !$expiry || !$cvv) { //if details not present...
            echo json_encode(['success' => false, 'message' => "All fields are required.", 'data' => null]);
            return;
        }

        //fetch user basket + items
        $userBasket = $this->basketModel->fetchUserBasket((int)$user_ID);
        $basket_ID = (int)($userBasket['basket_ID'] ?? 0);
        if ($basket_ID <= 0) { //if no basket, retunr error
            echo json_encode(['success' => false, 'message' => "Basket not found for user.", 'data' => null]); 
            return;
        }
        $basketItems = $this->basketModel->fetchBasketItems($basket_ID);
        if (empty($basketItems)) {
            echo json_encode(['success' => false, 'message' => "Your basket is empty.", 'data' => null]);
            return;
        }

        //stock validation        
        $stockIssues = []; //hold stock issues
        foreach ($basketItems as $item) {
            $pid = (int)($item['product_ID' ?? 0]); //get pid
            $quantity = (int)($item['quantity'] ?? 0); //get qty
            $inStock = (int)($item['stock'] ?? 0); //get stock > JOIIN in fetchBasketItems
            
            if ($pid <=0 || $quantity <=0) { //if invalid, add to stockissues
                $stockIssues[] = ['product_ID' => $pid, 'message' => "Invalid product or quantity."];
                continue; //skip to next item
            }
            if ($quantity > $inStock) {//if qty>stock, add to issues array
            $stockIssues[] = [ 'product_ID' => $pid, 'name' => $item['name'] ?? 'Unknown', 'requested' => $quantity, 'available' => $inStock];
            }
        }
        //if any stock issues...
        if (!empty($stockIssues)) {
            echo json_encode(['success' => false, 'message' => "Some items in your basket exceed available stock.", 'data' => ['stock_issues' => $stockIssues]]);
            return; //return to FE for user feedback
         }
         //"Product ID $pid has only $inStock items in stock, but you requested $quantity."

         //transaction
         //create order (pending default)
         //order items with qty
         //payment record > need to update paymentModel for this
         //success > mark paid
         //clear basket in DB & session
         //catch any errors/exceptions
    }

    public function getQuote(array $data) : void {
        //return total price without creating order > checkout.php page display
    }
}
?>