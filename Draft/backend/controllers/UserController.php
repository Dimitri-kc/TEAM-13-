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

        //hashed password for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        //create User model instance
        $userModel = new User(); //for database connection via user model User.php holding user class

        //default role is customer
        $role = 'customer';
        $registrationSuccess = $userModel->register($name, $surname, $email, $phone, $address, $hashedPassword, $role);
        if ($registrationSuccess) { //if registration successful
            echo json_encode([
            "success" => true,   
            "redirect" => "signin.php", //redirect to signin page after registration 
            "message" => "Registration successful. You can now login."
            ]);
            return;

        } else { //if registration failure
            echo json_encode([
                "success" => false,
                "message" => "Registration failed. Email already exists."
            ]);
            return;
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
        //echo "All fields are required.";
        return;
    }

    //create User model instance
    $userModel = new User(); //for database connection via user model User.php holding user class
    $user = $userModel->login($email); //fetches user data by email only
        
    //verify password
    if ($user && password_verify($password, $user['password'])) {
        //setting session details
        $_SESSION['user_ID'] = $user['user_ID']; //store session userID
        $_SESSION['name'] = $user['name']; //store sesion name
        $_SESSION['role'] = $user['role']; //customer/admin
        //merge guest basket with user basket upon login
        mergeBaskets($user['user_ID']);

        if (!empty($user['must_change_password'])) { //force password change on first login for security
            echo json_encode(["success" => true, "redirect" => "changepassword.php", //redirect to change password page after login
                "message" => "Login successful. Please change your password before proceeding."]);
            return;
        }
            //redirect to homepage after login
            echo json_encode([
                "success" => true, "redirect" => "homepage.php", "user" => ["user_ID" => $user['user_ID'], "name" => $user['name'], "role" => $user['role']]
            ]);
            return;
        } else {
            echo json_encode([ //failed login json response
            "success" => false, "message" => "Login failed. Invalid email or password."]);
            return;
        }
    }

    public function changePassword($data) { //change user password upon first login only 
        $user_ID = $_SESSION['user_ID']; //get user ID from session
        $newPassword = trim($data['newPassword'] ?? ''); //get new password from input, trim whitespace
        if (!$newPassword) { //check valid input
            echo json_encode([ "success" => false, "message" => "New password is required."]);
            return;
        }

        $minLength = strlen($newPassword) >=8; //minimum length must be longer than 8
        $caseNumbers = preg_match('/[A-Za-z0-9]/', $newPassword); //must contain uppercase, lowercase & numbers
        $specialChar = preg_match('/[!@#$%^&*()]/', $newPassword); //must contain special characters such as !@#$%^&*()
        if (!$minLength || !$caseNumbers || !$specialChar) {
            echo json_encode([
                "success" => false,
                "message" => "Password must be atleast 8 characters long and contain uppercase, lowercase, numbers and special characters."
            ]);
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT); //hash new password for security

        $userModel = new User(); //for database connection
        $changeSuccess = $userModel->changePassword((int)$_SESSION['user_ID'], $hashedPassword); //call changePassword method in model to update password in database

        if ($changeSuccess) {
            echo json_encode([
                "success" => true,
                "redirect" => "homepage.php", //redirect to homepage after successful password change
                "message" => "Password changed successfully."
            ]);
            return;
        } else {
            echo json_encode(["success" => false, "message" => "Failed to change password. Please try again."]);
            return;
        }
        
    }
}
//Notes:
//added json encoding for registration and login response
//logout handled in routes via session destruction
//changed POST data retrieval in register method to paramerer $data for better error handling/testing to make fetch-compatbile
?>
