<?php //Controller: Users 
//header('Content-Type: application/json');
//session_start();

//include database and user models so controller can connect to database and use user methods
require_once __DIR__ . '/../config/db_connect.php';//state file path
require_once __DIR__ . '/../models/userModel.php';
require_once __DIR__ . '/../services/basketFunctions.php'; //for merger basket functions

//Handling user-related operations
class UserController {

    //method to register a new user
    public function register($data) {

        //collecting data from signup.hmtl form
        $name = trim($data['name'] ?? ''); //trims to remove whitespace
        $surname = trim($data['surname'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');
        $password = trim($data['password'] ?? '');

        //register user - basic validation
        if (!$name || !$surname || !$email || !$phone || !$address || !$password) {
            echo json_encode([
                "success" => false,
                "message" => "All fields are required."
            ]);
            //echo "All fields are required.";
            return;
        }

        //email validation included
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //validate email format
            echo json_encode(["success" => false, "message" => "Invalid email address"]);
            return;
        }

        //hashed password for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        //create User model instance
        $userModel = new User(); //for database connection via user model User.php holding user class

        //default role is customer
        $role = 'customer';
        $registrationSuccess = $userModel->register($name, $surname, $email, $phone, $address, $hashedPassword, $role);
        if (!empty($registrationSuccess['success'])) { //if registration successful
            echo json_encode([
            "success" => true,   
            "redirect" => "signin.php", //redirect to signin page after registration 
            "message" => "Registration successful. You can now login."
            ]);
            return;

        } else { //if registration failure
            if (isset($registrationSuccess['error']) && $registrationSuccess['error'] === 'exists') {
                echo json_encode([
                    "success" => false,
                    "message" => "Registration failed. Email already exists."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Registration failed. Please try again."
                ]);
            }
            return;
        }
    }

    //method to register an admin user
    public function registerAdmin($data) {
        //collect data from admin_signup.php
        $name = trim($data['name'] ?? '');
        $surname = trim($data['surname'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $password = trim($data['password'] ?? '');

        //basic validation
        if (!$name || !$surname || !$email || !$phone || !$password) {
            echo json_encode([
                "success" => false,
                "message" => "All fields are required."
            ]);
            return;
        }

        //email validation included
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //validate email format
            echo json_encode(["success" => false, "message" => "Invalid email address"]);
            return;
        }
        //password validation for server-side for admin
        if (strlen($password) < 8) {
            echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
            return;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            echo json_encode(["success" => false, "message" => "Password must include an uppercase character."]);
            return;
        }
        if (!preg_match('/[a-z]/', $password)) {
            echo json_encode(["success" => false, "message" => "Password must include a lowercase character."]);
            return;
        }
        if (!preg_match('/[0-9]/', $password)) {
            echo json_encode(["success" => false, "message" => "Password must include a number."]);
            return;
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) { //changed regex to allow any special character rather than set specifics for convenience
            echo json_encode(["success" => false, "message" => "Password must include a special character."]);
            return;
        }

        //hashed password for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        //create User model instance
        $userModel = new User(); //for database connection via user model User.php holding user class

        //ADMIN role
        $role = 'admin';
        $address = ''; //admin doesn't need address
        $registrationSuccess = $userModel->register($name, $surname, $email, $phone, $address, $hashedPassword, $role);
        if (!empty($registrationSuccess['success'])) { //if registration successful
            echo json_encode([
            "success" => true,   //page tbc as currently admin_login.php & admin_sigin.php
            "redirect" => "admin_login.php", //redirect to signin page after registration 
            "message" => "Admin account created successfully. You can now login."
            ]);
            return;

        } else { //if registration failure
            if (isset($registrationSuccess['error']) && $registrationSuccess['error'] === 'exists') {
                echo json_encode([
                    "success" => false,
                    "message" => "Email already exists. Please use a different email."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Admin registration failed. Please try again."
                ]);
            }
        }

    }

    //method to login a user  
    public function login($data) {

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? ''); //remove spaces

    //login user - basic validation
    if (!$email || !$password) {
        echo json_encode([
        "success" => false,
        "message" => "All fields are required."
        ]);
        return;
    }

    //create User model instance
    $userModel = new User(); //for database connection via user model User.php holding user class
    $user = $userModel->login($email); //fetches user data by email only
        
    //block admins from customer login page
    if (($user['role'] ?? '') === 'admin') {
        echo json_encode([
            "success" => false,
            "message" => "Admins must sign in via the admin login page."
        ]);
        return;
    }
    //verify password
    if ($user && password_verify($password, $user['password'])) {
        //setting session details
        $_SESSION['user_ID'] = (int)$user['user_ID']; //store session userID
        $_SESSION['name'] = $user['name']; //store sesion name
        $_SESSION['role'] = $user['role']; //customer/admin
        $_SESSION['must_change_password'] = (int)($user['must_change_password'] ?? 0); //stores flag in session at login > force password change on first login
        //merge guest basket with user basket upon login
        mergeSessionBasketToUser($_SESSION['user_ID']);

        if ($_SESSION['must_change_password'] === 1) { //force password change on first login for security
            echo json_encode(["success" => true, "redirect" => "changepassword.php", //redirect to change password page after login
                "message" => "Login successful. Please change your password before proceeding."]);
            return;
        }
            //redirect to homepage after login
            echo json_encode([
                "success" => true, "redirect" => "homepage.php", "user" => ["user_ID" => $_SESSION['user_ID'], "name" => $_SESSION['name'], "role" => $_SESSION['role'], "must_change_password" => $_SESSION['must_change_password']]
            ]);
            return;
        } else {
            echo json_encode([ //failed login json response
            "success" => false, "message" => "Login failed. Invalid email or password."]);
            return;
        }
    }

    public function adminLogin($data) { //admin-specific login method with admin role check

        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? ''); //remove spaces

        //basic validation
        if (!$email || !$password) {
            echo json_encode([
            "success" => false,
            "message" => "All fields are required."
            ]);
            return;
        }

        //create User model instance
        $userModel = new User(); //for DB connection
        $user = $userModel->login($email); //fetches user data by email only

        if (!$user) { //if user not found
            echo json_encode([
                "success" => false,
                "message" => "Invalid email or password."
            ]);
            return;
        }

        //check if user is an admin
        if ($user['role'] !== 'admin') {
            echo json_encode([
                "success" => false,
                "message" => "Access denied. Admin credentials required."
            ]);
            return;
        }

        //verify password
        if (!password_verify($password, $user['password'])) {
            echo json_encode([
                "success" => false,
                "message" => "Invalid email or password."
            ]);
            return;
        }

        //set session variables
        $_SESSION['user_ID'] = (int)$user['user_ID'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['surname'] = $user['surname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role']; //admin session also stored in session for role-based access control
        $_SESSION['must_change_password'] = (int)$user['must_change_password'];
        //check if password change required for first login
        if ((int)$user['must_change_password'] === 1) {
            echo json_encode(["success" => true, "redirect" => "changepassword.php",
                "message" => "Login successful. Please change your password before proceeding."]);
            return;
        }

        echo json_encode([
            "success" => true, "redirect" => "admin_dash.php", "user" => ["user_ID" => $_SESSION['user_ID'], "name" => $_SESSION['name'], "role" => $_SESSION['role'], "must_change_password" => $_SESSION['must_change_password']],
            "message" => "Admin login successful."
        ]);
        return;
        
        echo json_encode([
            "success" => false, "message" => "Login failed. Invalid email, password, or insufficient privileges."
        ]);
        return; 
    }

    public function changePassword($data) { //change user password upon first login only 

        if (session_status() === PHP_SESSION_NONE) {//start session if not already
            session_start();
        }
        if (empty($_SESSION['user_ID'])) { //check if user logged in
            echo json_encode(["success" => false, "message" => "Please login."]);
            return;
        }
        $user_ID = (int)$_SESSION['user_ID']; //get user ID from session
        $role = $_SESSION['role'] ?? ''; //get user role from session
        //forced log in flag
        $isForcedChange = isset($_SESSION['must_change_password']) && (int)$_SESSION['must_change_password'] === 1;

        $newPassword = trim($data['newPassword'] ?? ''); //get new password from input, trim whitespace
        if (!$newPassword) { //check valid input
            echo json_encode([ "success" => false, "message" => "New password is required."]);
            return;
        }

        //adding server-side validation with detailed error msgs for user
        if (strlen($newPassword) < 8) {
            echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
            return;
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include an uppercase character."]);
            return;
        }
        if (!preg_match('/[a-z]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include a lowercase character."]);
            return;
        }
        if (!preg_match('/[0-9]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include a number."]);
            return;
        }
        if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) { //changed regex to allow any special character rather than set specifics for convenience
            echo json_encode(["success" => false, "message" => "Password must include a special character."]);
            return;
        }
        
        $userModel = new User(); //for database connection
        global $conn;
        //check if user is reusing their old password
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_ID = ?"); //fetch current hashed password from database
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentPasswordData = $result->fetch_assoc();
        $stmt->close();
        //if new password matches current password then reject change for security
        if ($currentPasswordData &&password_verify($newPassword, $currentPasswordData['password'])) {
            echo json_encode(["success" => false, "message" => "New password must be different from the current password."]);
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); //hash new password for security
        $changeSuccess = $userModel->changePassword($user_ID, $hashedPassword); //call changePassword method in model to update password in database

        if ($changeSuccess) {
            $_SESSION['must_change_password'] = 0; //syncs session flag with DB after successful forced password change
            //redirect determined by role for security - admins>admin_dash & users>user_dash
            $redirect = ($role === 'admin') ? 'admin_dash.php' : 'user_dash.php';

            //if this wasn't forced change for first-login
            $returnTo = trim((string)($data['returnTo'] ?? '')); //returnTo parameter for redirect if optional password change
            if (!$isForcedChange && $returnTo !== '') { //if not forced change and returnTo provided
                $isSafeRelative = 
                    preg_match('/^[A-Za-z0-9._\-\/]+(?:\?.*)?(?:#.*)?$/', $returnTo) && //basic relative URL structure check
                    stripos($returnTo, 'changePassword.php') === false && //prevent redirect loops to change password page
                    stripos($returnTo, 'http://') === false && stripos($returnTo, 'https://') === false && //prevent absolute URLs to avoid open redirect vulnerabilities
                    stripos($returnTo, '..') === false; //prevent directory traversal attempts
                    
                    if ($isSafeRelative) {
                        $redirect = '/' . ltrim($returnTo, '/'); //construct safe redirect path
                    }
            }
            echo json_encode([
                "success" => true,
                "message" => "Password changed successfully.",
                "redirect" => $redirect ?? "homepage.php" //redirect to returnTo if provided and safe, otherwise homepage
            ]);
            return;
        } else {
            echo json_encode(["success" => false, "message" => "Failed to change password. Please try again."]);
            return;
        }
    }

    public function logout() { //logout user by destroying session
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); //start if not already
        }
        $_SESSION = []; //clear session array > free all variables
        if (ini_get("session.use_cookies")) { //clear any cookies
            $params = session_get_cookie_params(); //if cookies, get params & set cookie to expire from past > security constraint to prevent
            setcookie(session_name(), '', time() -42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        } 
        session_destroy(); //destroy/clear session
        echo json_encode(["success" => true, "redirect" => "homepage.php", "message" => "Logged out successfully."]);
        return;

    }
        
}
//Notes:
//added json encoding for registration and login response
//logout handled in routes via session destruction
//changed POST data retrieval in register method to paramerer $data for better error handling/testing to make fetch-compatbile
?>
