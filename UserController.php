<?php //Controller: Users 

//include database and user models so can controller can connect to database and use user methods
include_once 'backend/db_connect.php';//changed to explicity state path
include_once 'backend/models/User.php';
session_start(); 

//Handling user-related operations
class UserController {
    //method to register a new user

    //method to login a user  
}

//to check if form submitted for both register and login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordConfirm = trim($_POST['password_confirm'] ?? '');
}
?>