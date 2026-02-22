<?php //User routes - receives http requests from html and calls controller methods

header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../controllers/userController.php'; //path to userController.php

$userController = new UserController(); //instance of usercontroller
//Define routes for user-related actions
$raw = file_get_contents("php://input"); //get raw POST data (JSON expected fron fetch)
$data = json_decode($raw, true); //get JSON input data and decode

//debug log to trace session & user input data for troubleshooting
$log = [
    'time' => date('c'), //exact current date/time
    'session' => $_SESSION, //current session data
    'role' => $_SESSION['role'] ?? 'none', //user role set
    'raw_input' => $raw, //raw POST received (unencrypted)
    'data' => $data, //JSON data from POST
    'json_error' => json_last_error_msg()
]; //debug log array
file_put_contents(__DIR__ . '/userDebug.log',
 print_r($log, true) .  str_repeat('-', 50) . PHP_EOL, FILE_APPEND); //print_r converts array to readable string (added -50 for clarity)

if(!$data || !isset($data['action'])){ //if no data/action provided then return error
    echo json_encode(["success" => false, "message" => "No action specified"]); 
    return;
}
//if POST request then check action (register/login/logout)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    return;
}    //POST prevents unintended data exposure via GET even if JSON input expected. Prevents CSRF attacks too

    //switch to call relevant controller method based on action in .html
    switch ($data['action']) {
        case 'register': //register action called from signup.html
            $userController->register($data);//call register method in controller
            break;

        case 'login': //login action called from signin.html
            $userController->login($data); //calling login method in controller
            break;

        case 'change_password': //change password action called from changePassword.php
            $userController->changePassword($data); //calling changePassword method in controller
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