<?php //All user functions

//redirect to login page if user not logged in
//created for pages linked to user such as profile, orders etc.
function require_login() {
    if(empty($_SESSION['user_ID'])) {
        header('Location: /login.html'); 
        exit; 
    }
}

//check if role suits access level, if not deny access and redirect to login
function require_role($requiredRole) {
    if(empty($_SESSION['role'])) {
        header('Location: /login.html');
        exit; 
    }
    //if role doesn't match required then deny access
    //e.g. if page requires 'admin' role but user is 'user' then deny access
    if($_SESSION['role'] !== $requiredRole) {
        header('HTTP/1.1 403 Forbidden');
        echo "Access denied. You do not have permission to access this page.";
        exit; 
    }
}
?>