<?php //Controller: Users 

session_start();
//include database and user models so controller can connect to database and use user methods
require_once '../../config/db_connect.php';//state file path
require_once '../../models/userModel.php';
require_once '../../services/basketFunctions.php'; //for merger basket functions

//Handling user-related operations
class UserController {

    //method to register a new user
    public function register() {

        //collecting data from signup.hmtl form
        $name = trim($_POST['name'] ?? ''); //trims to remove whitespace
        $surname = trim($_POST['surname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
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
        $registrationSuccess = $userModel->register($name, $surname, $email, $phone, $address, $hashedPassword, $role);
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
            echo json_encode (['success' => false, 'message' => "All fields are required."]);
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
            //merge guest basket with user basket upon login
            mergeBaskets($user['user_ID']);
            //redirect to homepage after login
            echo json_encode(['success' => true, 'message' => 'Login successful.', 'redirect' => '/Homepage.html' ]);
            exit;
        } else {
            echo json_encode(['success' => true, 'message' => 'Login failed. Invalid email or password.']);
        }
    }
}
//added json for linking successful backend implementation
?>
