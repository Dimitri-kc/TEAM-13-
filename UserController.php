<?php //Controller: Users 

session_start();
//include database and user models so can controller can connect to database and use user methods
include_once '../../config/db_connect.php';//state file path
include_once '../../models/userModel.php';

//Handling user-related operations
class UserController {

    //method to register a new user
    public function register() {

        //collecting data from signup.hmtl form
        $name = trim($_POST['name'] ?? ''); //trims to remove whitespace
        $surname = trim($_POST['surname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = trim($_POST['password'] ?? '');

        //register user - basic validation
        if (!$name || !$surname || !$email || !$address || !$password) {
            echo "All fields are required.";
            exit;
        }

        //hashed password for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        //create User model instance
        $userModel = new User(); //for database connection via user model User.php holding user class

        //default role is customer
        $role = 'customer';
        $registrationSuccess = $userModel->register($name, $surname, $email, $address, $hashedPassword, $role);
        if ($registrationSuccess) {
            echo "Registration successful. You can now <a href='/signin.html'>login</a>.";//hyperlink to login page after registration
            } else {
                echo "Registration failed. Please try again.";
            }
 }

    //method to login a user  
    public function login() {
    
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? ''); //remove spaces

        //login user - basic validation
        if (!$email || !$password) {
            echo "All fields are required.";
            exit;
        }

        //create User model instance
        $userModel = new User(); //for database connection via user model User.php holding user class
        $user = $userModel->login($email, $password);
        
        //verify password
        if ($user && password_verify($password, $user['password'])) {
            //setting session details
            $_SESSION['user_ID'] = $user['user_ID']; //store session userID
            $_SESSION['name'] = $user['name']; //store sesion name
            $_SESSION['role'] = $user['role']; //customer/admin
            //redirect to homepage after login
            header('Location: /Homepage.html');
            exit;
        } else {
            echo "Login failed. Invalid email or password.";
        }
    }
}
//removed the echo before header redirection to avoid header errors
?>
