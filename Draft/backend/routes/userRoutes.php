<?php //User routes - receives http requests frmo html and calls controller methods

session_start();
include_once '../../controllers/userController.php'; //path to userController.php

$userController = new UserController(); //nstance of usercontroller
//Define routes for user-related actions
//if POST request then check action (register/login/logout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    //checking for any hidden input
    $action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : ''; //get action from form input
    //included the htmlspecialchars for XSS
    //switch to call relevant controller method based on action in .html
    switch ($action) {
        case 'register': //register action called from signup.html
            $userController->register();//call register method in controller
            break;

        case 'login': //login action called from signin.html
            $userController->login(); //calling login method in controller
            break;

        default:
            echo "Invalid action.";
            break;
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //checking for any hidden input
    $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';

    if ($action === 'logout') { //if logout action called from any page with logout link
            session_unset(); //clear session data
            session_destroy(); //destroy session > no logout method
            header('Location: /Homepage.html'); //auto redirect to homepage after logout
            exit; //ensure script stops here
            break; //end logout case
    }

}
//Notes:
//htmlspecialchars used to prevent XSS attacks > converts special chars to HTML
//signup.html path for form > ../../routes/userRoutes.php?action=register (POST) > input type hidden
//signin.html path for form > ../../routes/userRoutes.php?action=login (POST)
//logout as a href? >  ../../routes/userRoutes.php?action=logout 
?>