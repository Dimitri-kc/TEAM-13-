<?php 
//admin controller - handles admin specific operations
require_once __DIR__ . '/../config/db_connect.php'; //database connection
require_once __DIR__ . '/../models/userModel.php'; //user model for database operations related to users

class AdminController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    private function fail ($message = 'Error', $code = 400) {
        http_response_code($code);
        echo json_encode(["success" => false, "message" => $message]);
        return;
    }

    public function dashboardSummary($data) {
        //for dashboard summary stats
        $summary = [
            "total_customers" => 150, //placeholder data for now, will replace with actual database queries
            "total_orders" => 1200,
            "total_revenue" => 50000,
            "low_stock_items" => 5
        ];
        echo json_encode([
            "success" => true, 
            "message" => "Dashboard summary retrieved successfully.", 
            "data" => $summary]);
        return;
    }

//Customer Management Operations
    public function getCustomersList($data) {
        //list limit hadling
        $limit = isset($data['limit']) && is_int($data['limit']) ? $data['limit'] : 10; //default limit to 10 if not provided or invalid
        if ($limit <= 0) $limit = 1; //minimum limit set to 1 to prevent invalid queries
        if ($limit > 100) $limit = 100; //max limit set to 100 to prevent excessive data retrieval

        //for retrieving list of all customers in database and their details (name, email, phone, address etc)
        $sql = "SELECT user_ID as id, name, surname, email, phone, address, role, status
                FROM users
                ORDER BY created_at DESC LIMIT ?"; 
                //customer details, sort by newest first, limit results to specified amnt (default 10, max 100) to prevent excessive data retrieval
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $customers = []; //hold customer data in array
        while ($row = $result->fetch_assoc()) { 
            $customers[] = $row; //add each customer record to customers array
        }
        $stmt->close();

        echo json_encode([
            "success" => true, 
            "customers" => $customers]);
    }

    public function createCustomer($data) { //for creating and adding new customer to database
        $name = trim($data['name'] ?? '');
        $surname = trim($data['surname'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');
        $password = trim($data['password'] ?? '');

        //validate required fields
        if ($name === '' || $surname === '' || $email === '' || $password === '') {
            $this->fail("Name, surname, email and password are required.");
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //validate email format
            $this->fail("Invalid email address.");
            return;
        }

        //strong server-side password validation (same as changePassword/registerAdmin)
        if (strlen($password) < 8) {
            echo json_encode([
                "success" => false, 
                "message" => "Password must be at least 8 characters."
                ]);
            return;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            echo json_encode([
                "success" => false, 
                "message" => "Password must include an uppercase character."
                ]);
            return;
        }
        if (!preg_match('/[a-z]/', $password)) {
            echo json_encode([
                "success" => false, 
                "message" => "Password must include a lowercase character."
                ]);
            return;
        }
        if (!preg_match('/[0-9]/', $password)) {
            echo json_encode([
                "success" => false, 
                "message" => "Password must include a number."
                ]);
            return;
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            echo json_encode([
                "success" => false, 
                "message" => "Password must include a special character."
                ]);
            return;
        }

        //check for duplicate email
        $check = $this->conn->prepare("SELECT user_ID FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $check->close();
            $this->fail("Email is already in use.");
            return;
        }
        $check->close();

        //hash password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer'; //default new user role to customer
        $status = 'active'; // default status
        $mustChangePassword = 1;
        //insert new customer into database with role 'customer' and status 'active'
        $stmt = $this->conn->prepare("INSERT INTO users (name, surname, email, phone, address, password, role, status, must_change_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssi", $name, $surname, $email, $phone, $address, $hashedPassword, $role, $status, $mustChangePassword);
        $success = $stmt->execute();
        if (!$success) {
            $stmt->close();
            $this->fail("Failed to create customer.");
            return;
        }
        $newCustomerID = (int)$stmt->insert_id; //get ID of newly created customer
        $stmt->close();

        echo json_encode([
            "success" => true, 
            "message" => "Customer created successfully.", 
            "customer_ID" => $newCustomerID]);
    }
    public function getCustomerDetails($data) {
        //get details of a specific customer
        $customerID = (int)($data['customer_ID'] ?? $data['customerId'] ?? 0); //accept either customer_ID or customerId for flexibility
        if ($customerID <= 0) { //validate customer ID
            $this->fail("Invalid customer ID", 400);
            return;
        }
        //select user details from database for the chosen record, limit to 1 record for efficiency
        $sql = "SELECT user_ID as id, name, surname, email, phone, address, role, status
                FROM users WHERE user_ID = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();

        if (!$customer) {
            $this->fail("User not found", 404);
            return;
        }
        echo json_encode([
            "success" => true, 
            "customer" => $customer]);
    }

    public function updateCustomer($data) {
        //update customer personal details (name, email, phone etc)
        $customerID = (int)($data['customer_ID'] ?? 0);
        $role = trim($data['role'] ?? 'customer');
        $name = trim($data['name'] ?? '');
        $surname = trim($data['surname'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');
        
        //validate user details exist and are in correct format before updating
        if ($customerID <= 0) { //validate customer ID
            $this->fail("Invalid customer id."); 
            return; 
        }
        if ($name === '' || $surname === '' || $email === '') { //if fields empty return error
            $this->fail("Name, surname and email are required."); 
            return; 
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  //validate email format
            $this->fail("Invalid email address."); 
            return; 
        }
        if (!in_array($role, ['customer', 'admin'], true)) { 
            $this->fail("Invalid role."); //validate role is either customer or admin
            return; 
        }
        //check if email is already in use by another user
        $check = $this->conn->prepare("SELECT user_ID FROM users WHERE email = ? AND user_ID <> ? LIMIT 1");
        $check->bind_param("si", $email, $customerID);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $check->close();
            $this->fail("Email is already in use.");
            return;
        }
        $check->close(); //if validation passes, proceed with update

        //update customer details in database
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, phone = ?, address = ?, role = ? WHERE user_ID = ?");
        $stmt->bind_param("ssssssi", $name, $surname, $email, $phone, $address, $role, $customerID);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            $this->fail("Failed to update customer details.");
            return;
        }
        echo json_encode([
            "success" => true, 
            "message" => "Customer details updated successfully."]);
    }

    public function deactivateCustomer($data) {
        //for soft delete (deactivate & retain data) or hard delete (permanently deleting all cx data)
        $customerID = (int)($data['customer_ID'] ?? 0); //validate customer ID
        $mode = strtolower(trim($data['mode'] ?? '')); //mode can be deactivate, reactivate or remove (for hard delete)

        if ($customerID <= 0) { //validate user exists
            $this->fail("Invalid customer id."); 
            return; 
        } 
        //validate mode is either deactivate, reactivate or hard delete
        if (!in_array($mode, ['deactivate', 'reactivate', 'remove'], true)) {
            $this->fail("Invalid account action.");
            return;
        }

        if ($mode === 'remove') { //hard delete - permanently remove all customer data from DB
            $stmt = $this->conn->prepare("DELETE FROM users WHERE user_ID = ? LIMIT 1");
            $stmt->bind_param("i", $customerID);
            $ok = $stmt->execute();
            $stmt->close();

            if (!$ok) { 
                $this->fail("Action failed. Unable to delete customer data."); 
                return; 
            }
            echo json_encode([
                "success" => true, 
                "message" => "Customer removed successfully."]);
            return;
        }

        $newStatus = ($mode === 'deactivate') ? 'inactive' : 'active'; //view new status
        $stmt = $this->conn->prepare("UPDATE users SET status = ? WHERE user_ID = ? LIMIT 1");//update status to active/inactive in DB
        $stmt->bind_param("si", $newStatus, $customerID); //bind new status and customer ID parameters
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) { //if update fails return error message
            $this->fail("Action failed. Unable to update account status."); 
            return; 
        }

        echo json_encode([
            "success" => true, 
            "message" => "Account status updated.", 
            "new_status" => $newStatus]);
    }

    //update customer role (user>admin or vice versa)
    public function updateCustomerRole($data) {
        //validate customer ID & role
        $customerID = (int)($data['customer_ID'] ?? 0);
        $role = trim($data['role'] ?? '');
    
        if ($customerID <= 0) { //if no id
            $this->fail("Invalid customer id."); 
            return; 
        }
        if (!in_array($role, ['customer', 'admin'], true)) {  //if role isn't cx/admin
            $this->fail("Invalid role."); 
            return; 
        }
        //update DB with new role
        $stmt = $this->conn->prepare("UPDATE users SET role = ? WHERE user_ID = ? LIMIT 1");
        $stmt->bind_param("si", $role, $customerID);
        $success = $stmt->execute();
        $stmt->close();
    
        if (!$success) { 
            $this->fail("Failed to update role."); 
            return; 
        }
        echo json_encode([
            "success" => true, 
            "message" => "Role updated successfully."]);
    }

    public function getCustomerOrders($data) {
        //for retrieving orders of a specific customer
        $customerID = (int)($data['customer_ID'] ?? $data['user_id'] ?? 0);
        $limit = isset($data['limit']) ? (int)$data['limit'] : 50; //default limit to 50
    
        if ($customerID <= 0) { $this->fail("Invalid customer id."); return; }
        if ($limit <= 0) $limit = 100; //set minimum limit to 1 to prevent invalid queries, 100 
        if ($limit > 250) $limit = 250; //set maximum limit to 250 to prevent excessive data retrieval
    
        //select order details for specific customer, sort by newest first, limit results to specified limit (default 50, max 250) to prevent excessive data retrieval
        $sql = "SELECT order_ID as order_id, order_date, order_status as status, total_price as total_amount FROM orders WHERE user_ID = ? ORDER BY order_date DESC LIMIT ?";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $customerID, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $orders = []; //hold order data in array
        while ($row = $result->fetch_assoc()) { 
            $orders[] = $row; //add each order record to orders array
        }
        $stmt->close();
    
        echo json_encode([
            "success" => true, 
            "orders" => $orders]);
    }

//Report Analytics
    public function reportStockLevel($data) {
        //for generating stock level report
        $this->fail('Not implemented yet', 501);
    }

    public function reportCustomerSignups($data) {
        //for retrieveing total customers 
        $this->fail('Not implemented yet', 501);
    }

    public function reportTotalRevenue($data) {
        //generate total revenue report
        $this->fail('Not implemented yet', 501);
    }
    
    public function getOrdersList($data) {
        //get list of all orders with details (customer name, order date, total amount, status etc)
        $this->fail('Not implemented yet', 501);
    }

    public function updateOrderStatus($data) {
        //update the status of a specific order
        $this->fail('Not implemented yet', 501);
    }
}

?>
