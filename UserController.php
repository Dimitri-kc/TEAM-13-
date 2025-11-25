<?php //Controller: Users 

session_start();
//include database and user models so can controller can connect to database and use user methods
include_once 'backend/db_connect.php';//changed to explicity state path
include_once 'backend/models/User.php';

//Handling user-related operations
class UserController {
    //method to register a new user

    //method to login a user  
}

//to check if form submitted for both register and login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    //register user
    if (!$username || !$email || !$password) {
        echo "All fields are required.";
        exit;
    }
    //hashed password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    //create User model instance
    $userModel = new User(); //for database connection via user model User.php holding user class - not yet made - create user
    //default role is customer
    $role = 'customer';
    
}
?>
