<?php //User routes - receives http requests from html and calls controller methods

header('Content-Type: application/json');

session_start();
require_once '../../controllers/userController.php'; //path to userController.php

$userController = new UserController(); //instance of usercontroller
//Define routes for user-related actions
$data = json_decode(file_get_contents("php://input"), true); //get JSON input data
if(!$data || !isset($data['action'])){ //if no data/action provided then return error
    echo json_encode(["success" => false, "message" => "No action specified"]); 
    return;
}
//if POST request then check action (register/login/logout)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    return;
}    
    //switch to call relevant controller method based on action in .html
    switch ($data['action']) {
        case 'register': //register action called from signup.html
            $userController->register($data);//call register method in controller
            break;

        case 'login': //login action called from signin.html
            $userController->login($data); //calling login method in controller
            break;

        case 'logout': //logout action called
            session_unset(); //unset all session variables
            session_destroy(); //destroy the session
            echo json_encode ([ "success" => true, "message" => "Logged out successfully"]);
            break;

        default:
            echo json_encode (["success" => false, "message"=> "Invalid action."]);
            break;
    }
?>