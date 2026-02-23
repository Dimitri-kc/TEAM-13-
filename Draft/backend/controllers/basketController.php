<?php //basketController.php - requests from hmtl pages

if(session_status() === PHP_SESSION_NONE) { //start session if not already started
    session_start();
}
//included so controller can connect to database and relevant methods
require_once __DIR__ . '/../models/basketModel.php';//state file path
require_once __DIR__ . '/../services/basketFunctions.php'; //for session-based guest basket functions
//require_once __DIR__ . '/../services/userFunctions.php'; //for require_login() & require_role() & guest basket functions

//Handling basket-related operations
class BasketController {

    //helper - consistent JSON response format
    private function jsonSuccess(bool $success, string $message, $data = null, int $statusCode =200): void {
        http_response_code($statusCode); //set HTTP code
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]); //standard response format for javascript
    }

    //helper - validate & convert input to int safely
    private function requireInt($value): int { 
        return (int)($value ?? 0); //convert to int, default 0 if null
    }

    //Stock validation helper - checks if requested quantity available
    //using basketModel > getStock(int $product_ID) : ?int
    private function validateStock(Basket $basketModel, int $product_ID, int $requestedQuantity) : bool {
        $stock = $basketModel->getStock($product_ID); //get current stock, returning int or null
        if ($stock === null) {
            $this->jsonSuccess(false, "Product not found.", ['product_ID' => $product_ID], 404); //404=not
        return false; //product doesn't exit > no stock
    } 
        if ($stock <=0) {
            $this->jsonSuccess(false, "This product is currently out of stock.", ['product_ID' => $product_ID, 'availableStock' => 0], 409); //409=conflict > out of stock
            return false; //out of stock
        }
        if ($requestedQuantity > $stock) {
            $this->jsonSuccess(false, "Only {$stock} left in stock.", ['product_ID' => $product_ID, 'availableStock' => $stock, 'allowedQuantity' => $stock], 409); //409=conflict
            return false; //requested quantity exceeds stock
        }
        return true; //sufficient stock available
    }

    //view basket contents
    public function viewBasket(): void {

        //if user logged in, fetch database basket items
        if (isset($_SESSION['user_ID'])) {
            //get session details
            $user_ID = (int)$_SESSION['user_ID']; 

            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID);
            if (!$basket || empty($basket['basket_ID'])) { //if no basket found, return empty array
                $this->jsonSuccess(false, "No basket found for user.", null, 500); //500 for server error
                return;
            }

            $basket_ID = (int)$basket['basket_ID'];
            $items = $basketModel->fetchBasketItems($basket_ID);
            if (empty($items)) $items = []; //if no items, return empty
            $this->jsonSuccess(true, "Basket fetched successfully.", ['items' => $items]);
            return;
        }
        //if guest user, fetch session basket items
        $items = getSessionBasket(); //get guest basket from session
        if (empty($items)) $items = []; //if no items, return empty
        $this->jsonSuccess(true, "Guest basket fetched successfully.", ['items' => $items]);
        
    }

    //POST request to add item to basket
    public function addItem(array $data): void {

        //get product details from session
        $product_ID = $this->requireInt($data['product_ID'] ?? ''); //validate/convert to int, default to 0 otherwise
        $quantity = $this->requireInt($data['quantity'] ?? '');
        //requireint to prevent SQL injection & ensure correct daata type

        if ($product_ID <=0 || $quantity <= 0) { //check valid input
            $this->jsonSuccess(false, "Product ID and quantity integers are required.", null, 400); //400=bad request ie invalid input
            return;
        }

        $basketModel = new Basket();
        //validate stock before adding to basket
        if (!$this->validateStock($basketModel, $product_ID, $quantity)) {
            return; //stock validation failed, response already sent in validateStock method
        }

        //if user logged in, use database basket
        if (isset($_SESSION['user_ID'])) { 
            $user_ID = (int)$_SESSION['user_ID']; //retreive user ID from session
        
            //create Basket model instance
            $basketModel = new Basket(); //for database connection via basket model Basket.php holding basket class <
            //fetch or create user basket
            $basket = $basketModel->fetchUserBasket($user_ID);
            //if no basket, return error
            if (!$basket || empty($basket['basket_ID'])) {
                $this->jsonSuccess(false, "Failed to fetch or create basket for user.", null, 500);
                return;
             }
             
            $basket_ID = (int)$basket['basket_ID']; //get basket ID
            $addBasketSuccess = $basketModel->addItemToBasket($basket_ID, $product_ID, $quantity);//adds item into database basket
            $this->jsonSuccess((bool)$addBasketSuccess, $addBasketSuccess ? "Item added to basket." : "Failed to add item to basket.", null, $addBasketSuccess ? 200 : 500); //200=success, 500=server error
            return;
        }
        //guest user - use session-based basket
            addToSessionBasket($product_ID, $quantity); 
            $this->jsonSuccess(true, "Item added to guest basket.", null, 200);

    }

    public function updateItem(array $data): void {
    //update quantity of specific item in basket

        //validate/convert to int, default to 0 otherwise
        $product_ID = $this->requireInt($data['product_ID'] ?? '');
        $quantity = $this->requireInt($data['quantity'] ?? '');

        if ($product_ID <=0) { //check valid input
            $this->jsonSuccess(false, "Invalid product ID.", null, 400); //400=bad request > product_ID is required
            return;
        }
        //if qty invalid, remove item from basket
        if ($quantity <=0) {
            $this->removeItem(['product_ID' => $product_ID]);
            return;
        }

        $basketModel = new Basket();
        //validate stock before updating basket
        if (!$this->validateStock($basketModel, $product_ID, $quantity)) {
            return; //stock validation failed, response sent in validateStock method
        }
        //if user logged in, update database basket
        if(isset($_SESSION['user_ID'])) {
            $user_ID = (int)$_SESSION['user_ID'];

            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
            
            //if no basket found, return error > error hadling as also in model
            if (!$basket || empty($basket['basket_ID'])) {
                $this->jsonSuccess(false, "Failed to fetch or create basket for user.", null, 500); //500=server error
                return;
            }

            $basket_ID = (int)$basket['basket_ID'];
            if ($quantity <=0) {
                $removeItemSuccess = $basketModel->removeItemFromBasket($basket_ID, $product_ID);
                $this->jsonSuccess((bool)$removeItemSuccess, $removeItemSuccess ? "Item removed from basket." : "Failed to remove item from basket.", null, $removeItemSuccess ? 200 : 500); //200=success, 500=server error
                return;
            }

            $updateSuccess = $basketModel->updateItemQuantity($basket_ID, $product_ID, $quantity);
            $this->jsonSuccess((bool)$updateSuccess, $updateSuccess ? "Item quantity updated." : "Failed to update item quantity.", null, $updateSuccess ? 200 : 500); //200=success, 500=server error
            return;
        } 
        //if guest user, update session basket
        if ($quantity <=0) { //if quantity invalid, remove item from session basket
            removeFromSessionBasket($product_ID);
            $this->jsonSuccess(true, "Item removed from guest basket.", null, 200);
            return;
        }
        updateSessionBasket($product_ID, $quantity); //update quantity in session basket not user basket
        $this->jsonSuccess(true, "Quantity updated in guest basket.", null, 200);
        return;
    }

    //POST request to remove item from basket
    public function removeItem(array $data): void {

        //validate/convert to int, default to 0 otherwise
        $product_ID = $this->requireInt($data['product_ID'] ?? '');

        if ($product_ID <=0) {//validate product ID
            $this->jsonSuccess(false, "Invalid product ID.", null, 400); //400=bad request > product_ID is required
            return;
        }

        //if user logged in, remove from database basket
        if (isset($_SESSION['user_ID'])) {
            $user_ID = (int)$_SESSION['user_ID'];

            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
            //if no basket found, return error
            if (!$basket || empty($basket['basket_ID'])) {
                $this->jsonSuccess(false, "Failed to fetch or createbasket for user.", null, 500); //500=server error
                return;
            }
            
            $basket_ID = (int)$basket['basket_ID']; //get basket ID
            $removeItemSuccess = $basketModel->removeItemFromBasket($basket_ID, $product_ID);//remove item by calling model method
            $this->jsonSuccess((bool)$removeItemSuccess, $removeItemSuccess ? "Item removed from basket." : "Failed to remove item from basket.", null, $removeItemSuccess ? 200 : 500); //200=success, 500=server error
            return;
        }
        
        //if guest user, remove from session basket
        removeFromSessionBasket($product_ID);
        $this->jsonSuccess(true, "Item removed from guest basket.", null, 200);

    }

}


//Notes: 
//calls basketModel methods to interact with database
//adds to basket
//updates quantity 
//removes items from basket
//fetches user's basket items

//Update: wrapped logic in if() so basket can be handled based on login status (user/guest)
?>
