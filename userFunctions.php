<?php //All user functions

//redirect to login page if user not logged in
//created for pages linked to user such as profile, orders etc.
function require_login() {
    if(empty($_SESSION['user_ID'])) {
        header('Location: /login.html'); 
        exit; 
    }
}

?>