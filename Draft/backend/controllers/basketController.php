<?php //basketController.php - requests from hmtl pages

if(session_status() === PHP_SESSION_NONE) { //start session if not already started
    session_start();
}
//included so controller can connect to database and relevant methods
require_once $_SERVER['DOCUMENT_ROOT'] . '/TEAM-13-/Draft/backend/models/basketModel.php';
//require_once __DIR__ . '/../models/basketModel.php';//state file path
require_once __DIR__ . '/../services/basketFunctions.php'; //for session-based guest basket functions
//require_once __DIR__ . '/../services/userFunctions.php'; //for require_login() & require_role() & guest basket functions
require_once __DIR__ . '/../models/productModel.php'; //for stock validation

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
    //using basketModel > getProductStock(int $product_ID) : ?int
    private function validateStock(int $product_ID, int $requestedQuantity) : bool {
        $productModel = new ProductModel();
    $product = $productModel->getById($product_ID); //get current stock, returning int or null
        if ($product === null) {
            $this->jsonSuccess(false, "Product not found.", ['product_ID' => $product_ID], 404); //404=not
        return false; //product doesn't exit > no stock
        } 
        $stock = (int)($product['stock'] ?? 0); //ensure stock is int, default to 0 if not set
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

        //if USER logged in, fetch database basket items
        if (isset($_SESSION['user_ID'])) {
            //get session details
            $user_ID = (int)$_SESSION['user_ID']; 

            // try {
            //     $basketModel = new Basket();
            // } catch (Exception $e) {
            //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
            //     return;
            // }
            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID);
            if (!$basket || empty($basket['basket_ID'])) { //if no basket found, return empty array
                $this->jsonSuccess(false, "No basket found for user.", null, 500); //500 for server error
                return;
            }

            $basket_ID = (int)$basket['basket_ID'];
            $items = $basketModel->fetchBasketItems($basket_ID);
            if (empty($items)) $items = []; //if no items, return empty
            $this->jsonSuccess(true, "Basket fetched successfully.", $items, 200); //200=success
            return;
        }

        //if GUEST user, fetch session basket items
        $sessionBasket = getSessionBasket(); //get guest basket from session > [product_ID => quantity]
        if (empty($sessionBasket)) {
            $this->jsonSuccess(true, "Guest basket fetched successfully.", [], 200); //200=success
            return; //empty basket
        }
        
        $productIDs = array_keys($sessionBasket); //get product IDs from session basket
        // try {
        //     $basketModel = new Basket();
        // } catch (Exception $e) {
        //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
        //     return;
        // }
        $basketModel = new Basket();
        $products = $basketModel->fetchGuestBasketProducts($productIDs); //fetch product details for guest basket items
        $items = []; //combine prodcut details with quantities
        foreach ($products as $product) {
            $pid = (int)$product['product_ID'];
            $items[] = [ //defining item details to return for guest basket items
                'product_ID' => $pid,
                'quantity' => (int)($sessionBasket[$pid] ?? 0), //get quantity from sess basket
                'name' => $product['name'], //get all deatils from products table > session only holds product_ID & quantity
                'price' => (float)$product['price'],
                'image' => $product['image'],
                'stock' => (int)$product['stock'],
                'category_id' => (int)$product['category_id'], ]; 
        }
        //check if deleted/out of stock items in session basket & remove
        foreach ($sessionBasket as $pid => $quantity) {
            $pid = (int)$pid;
            $productExists = false;
            foreach ($products as $product) {
                if ((int)$product['product_ID'] === $pid) {
                    $productExists = true;
                    break;
                }
            }
            if (!$productExists) { //if product no longer exists or unavailable
            removeFromSessionBasket($pid);//remove to prevent orphaned data & user confusuion
            }
        }
        $this->jsonSuccess(true, "Guest basket fetched successfully.", $items, 200);
        return;
    }

    //POST request to add item to basket
    public function addItem(array $data): void {

        //get product details from session
        $product_ID = (int)($data['product_ID'] ?? 0); //validate/convert to int, default to 0 otherwise
        $quantity = (int)($data['quantity'] ?? 1);
        //int to prevent SQL injection & ensure correct daata type

        if ($product_ID <=0 || $quantity <= 0) { //check valid input
            $this->jsonSuccess(false, "Invalid product ID or quantity.", null, 400); //400=bad request ie invalid input
            return;
        }

        // try {
        //     $basketModel = new Basket();
        // } catch (Exception $e) {
        //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
        //     return;
        // }
        $basketModel = new Basket();
        //if user logged in, use database basket
        if (isset($_SESSION['user_ID'])) { 
            $user_ID = (int)$_SESSION['user_ID']; //retreive user ID from session
        
            //create Basket model instance
            // try {
            //     $basketModel = new Basket();
            // } catch (Exception $e) {
            //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
            //     return;
            // }
            $basketModel = new Basket(); //for database connection via basket model Basket.php holding basket class <
            //fetch or create user basket
            $basket = $basketModel->fetchUserBasket($user_ID);
            //if no basket, return error
            if (!$basket || empty($basket['basket_ID'])) {
                $this->jsonSuccess(false, "Failed to fetch or create basket for user.", null, 500);
                return;
             }
             
            if (!$this->validateStock($product_ID, $quantity)) {
            return; // Stock validation failed, response already sent
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

        // try {
        //     $basketModel = new Basket();
        // } catch (Exception $e) {
        //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
        //     return;
        // }
        //$basketModel = new Basket();
        //validate stock before updating basket
        if (!$this->validateStock($product_ID, $quantity)) {
            return; //stock validation failed, response sent in validateStock method
        }
        //if user logged in, update database basket
        if(isset($_SESSION['user_ID'])) {
            $user_ID = (int)$_SESSION['user_ID'];

            // try {
            //     $basketModel = new Basket();
            // } catch (Exception $e) {
            //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
            //     return;
            // }
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

            // try {
            //     $basketModel = new Basket();
            // } catch (Exception $e) {
            //     $this->jsonSuccess(false, "Failed to connect to database: " . $e->getMessage(), null, 500); //500=server error
            //     return;
            // }
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

    //get count of items in basket for basket icon
    public function getBasketCount(): void {
        if (isset($_SESSION['user_ID'])) { //if user logged in, count items in database basket
            $user_ID = (int)$_SESSION['user_ID'];
            $basketModel = new Basket();
            $basket = $basketModel->fetchUserBasket($user_ID); //fetch user basket
            if (!$basket || empty($basket['basket_ID'])) { //if no basket found, return count 0
                $this->jsonSuccess(true, "Basket count fetched successfully.", ['count' => 0], 200);
                return;
            }
            $basket_ID = (int)$basket['basket_ID'];
            $basketItems = $basketModel->fetchBasketItems($basket_ID);
            $count = 0; //call model method to count items in basket
            foreach ($basketItems as $item) {
                $count += (int)($item['quantity'] ?? 0); //sum quantity of each item for total count
            }
            $this->jsonSuccess(true, "Basket count fetched successfully.", ['count' => $count], 200);
            return;
        }
        //guest user
        $sessionBasket = getSessionBasket(); //get guest basket from session > [product_ID => quantity]
        $count = array_sum($sessionBasket); //sum quantities for total count
        $this->jsonSuccess(true, "Basket count fetched successfully.", ['count' => $count], 200);
    }
//Notes: 
//calls basketModel methods to interact with database
//adds to basket
//updates quantity 
//removes items from basket
//fetches user's basket items
//Update: wrapped logic in if() so basket can be handled based on login status (user/guest)
}
?>
