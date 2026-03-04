<?php //checoutController.php > checkout flow > handles checkout process

require_once __DIR__. '/../config/db_connect.php'; //DB conncetion
require_once __DIR__ . '/../models/basketModel.php'; //for basket data
require_once __DIR__ . '/../services/basketFunctions.php'; //for guest checkout
require_once __DIR__ . '/../models/orderModel.php'; //order processing creation
require_once __DIR__ . '/../models/orderItemsModel.php'; //order items creation
require_once __DIR__ . '/../models/paymentModel.php'; //payment record creation

class CheckoutController {
    private $basketModel; 
    private $orderModel;
    private $orderItemModel;

    public function __construct() {
        $this->basketModel = new Basket();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemsModel();
    }

    public function processCheckout(array $data): void {
        global $conn;

        //validate user session - guest checkout to be added later
        $user_ID = $_SESSION['user_ID'] ?? null; //get user id
        if (!$user_ID) { //if no user, return error
            echo json_encode(['success' => false, 'message' => "User not logged in. Please login to checkout.", 'data' => null]);
            return;
        }

        //merge any guest basket into DB user basket upon checkout
        mergeSessionBasketToUser((int)$user_ID);
        //read JSON from FE
        $address = trim($data['address'] ?? ''); //get address
        $cardNumber = preg_replace('/\s+/', '', (string)($data['card_number'] ?? '')); //get cardNumber & remove spaces
        $expiry = trim($data['expiry'] ?? ''); //get expiry
        $cvv = trim($data['cvv'] ?? ''); //get cvv

        if ($address === '' || $cardNumber === '' || $expiry === '' || $cvv === '') { //if details not present...
            echo json_encode(['success' => false, 'message' => "All fields are required.", 'data' => null]);
            return;
        }
        //validate card details format
        $paymentModel = new Payment();
        if (!$paymentModel->validatePayment($cardNumber, $expiry, $cvv)) {
            echo json_encode(['success' => false, 'message' => "Invalid payment details. Please check your card information.", 'data' => null]);
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
            $pid = (int)($item['product_ID'] ?? 0); //get pid
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

        $total_sum = basket_total($basketItems);
         //transaction
        try {
            $conn->begin_transaction();
            //create order (pending default)
            $order_ID = $this->orderModel->createOrder((int)$user_ID, $total_sum, $address); //create order with user id, total sum, address
            if (!$order_ID) {
                throw new Exception("Failed to create order.");
            }
            $this->orderModel->updateOrderStatus($order_ID, 'Pending'); //set order status to pending

            //insert order items with qty
            foreach ($basketItems as $item) {
                $pid = (int)($item['product_ID'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 0);
                $unit_price = (float)($item['price'] ?? 0);
                if ($pid <= 0 || $quantity <= 0) { //if valid, insert order item
                    throw new Exception ("Invalid product ID or quantity for order item.");
                }
                $result =$this->orderItemModel->addOrderItem($order_ID, $pid, $quantity, $unit_price); //create order item with order id, product id, qty, price
                if (!$result) {    
                    throw new Exception("Failed to add order item for product ID: $pid");
                }
            }

            //payment record > need to update paymentModel for this
            /* $stmt = $conn->prepare("INSERT INTO payments (order_ID, user_ID, address, total_sum) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare payment record creation.");
            }
            $oid = (int)$order_ID;
            $uid = (int)$user_ID;
            $sum = (float)$total_sum;
            $stmt->bind_param("iisd", $oid, $uid, $address, $sum);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new Exception("Failed to create payment record.");
            }

            $payment_ID = (int)$conn->insert_id; //get payment id for record
            $stmt->close(); */

            $paymentModel = new Payment();
            if (!$paymentModel->validatePayment($cardNumber, $expiry, $cvv)) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => "Invalid payment details. Please check submitted card details.", 'data' => null]);
                return;
            }

            //create payment record in DB
            $paymentSuccess = $paymentModel->createPayment($order_ID, (int)$user_ID, $address, (float)$total_sum);
            if (!$paymentSuccess) {
                throw new Exception("Failed to create payment record.");
            }
            // Get the payment_ID from the last insert
            $payment_ID = (int)$conn->insert_id;

            //success > mark order status paid
            $this->orderModel->updateOrderStatus((int)$order_ID, 'Paid'); //update order status to paid
/* COMMENTED OUT UNTIL updateStock() FUNCTION ADDED             //deduct stock for each item
            require_once __DIR__ . '/../models/productModel.php';
            $productModel = new ProductModel();
            foreach ($basketItems as $item) {
                $pid = (int)($item['product_ID'] ?? 0);
                $quantity = (int)($item['quantity'] ?? 0);
                $productModel->updateStock($pid, -$quantity); // Assuming this method exists
            } */
            //clear basket in DB & session
            $this->basketModel->clearBasket($basket_ID); //clear basket items in DB

            clearSessionBasket(); //clear guest basket or any leftover session basket data
            $conn->commit(); //commit transaction after all steps successful
            //return success message with order and payment IDs for reference
            echo json_encode(['success' => true, 'message' => "Checkout successful. Your order ID is $order_ID.", 'data' => ['order_ID' => (int)$order_ID, 'payment_ID' => (int)$payment_ID, 'total_sum' => (float)$total_sum]]); 
            //catch any errors/exceptions
        } catch (Exception $e) {
            if ($conn && $conn->errno === 0) { //if transaction is active, rollback
                $conn->rollback();
            }
            echo json_encode(['success' => false, 'message' => "Checkout failed: " . $e->getMessage(), 'data' => null]);
        }
    }

    //actually already have basket_total() function in basketFunctions.php that can be used for checkout without vreating order
    public function getQuote(array $data) : void {
        $user_ID = $_SESSION['user_ID'] ?? null; //get user id
        if ($user_ID) { 
            $userBasket = $this->basketModel->fetchUserBasket((int)$user_ID);
            $basket_ID = (int)($userBasket['basket_ID'] ?? 0);
            $basketItems = $basket_ID > 0 ? $this->basketModel->fetchBasketItems($basket_ID) : [];
            $total_sum = basket_total($basketItems);
            echo json_encode(['success' => true, 'message' => "Quote calculated successfully.", 'data' => ['items' => count($basketItems), 'total_sum' => (float)$total_sum]]);
        }
        //return total sum without creating order > checkout.php page display
    }
}
?>