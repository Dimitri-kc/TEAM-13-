<?php //Controller: Users 

session_start();
//include database and user models so can controller can connect to database and use user methods
include_once 'backend/db_connect.php';//changed to explicity state path
include_once 'backend/models/userModel.php';
//path files/folder to be created 26/11/25

//Handling user-related operations
class UserController {
    //method to register a new user

    //method to login a user  
}

//to check if form submitted for both register and login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['name'] ?? ''); //trims to remove whitespace
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    //register user - basic validation
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
    $registrationSuccess = $userModel->register($username, $email, $hashedPassword, $role);
    if ($registrationSuccess) {
        echo "Registration successful. You can now <a href='/login.html'>login</a>.";//hyperlink to login page after registration
    } else {
        echo "Registration failed. Please try again.";
    }
    
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? ''); //remove spaces

    //login user - basic validation
    if (!$email || !$password) {
        echo "All fields are required.";
        exit;
    }

    $user = $userModel->login($email, $hashedpassword);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_ID'] = $user['user_ID']; //store session userID
        $_SESSION['username'] = $user['username']; //store sesion username
        $_SESSSION['role'] = $user['role']; //customer/admin
        echo "Login successful. Welcome, " . htmlspecialchars($user['username']) . "!"; //also prevents XSS by stopping display of special characters in username
        //redirect to homepage after login
        header('Location: /Homepage.html');
        exit;
    } else {
        echo "Login failed. Invalid email or password.";
    }
}
//check if setting session variables necessary here or in model as currently in both
//confirm if register/login code needs to be inside class or outside as currently outside also separated or not
?>
