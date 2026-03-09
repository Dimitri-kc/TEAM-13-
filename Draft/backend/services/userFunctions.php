<?php //All user functions

//authentication and authorization related functions
if(session_status() === PHP_SESSION_NONE) { //start session if not already started
    session_start();
}

//redirect to signin page if user not logged in
//created for pages linked to user such as profile, orders etc.
function require_login() {
    if(empty($_SESSION['user_ID'])) { //if no user_ID in session, user not logged in
        header('Location: /signin.html'); 
        exit; 
    }
}

//check if role suits access level, if not deny access and redirect to login
function require_role($requiredRole) {
    if(empty($_SESSION['role'])) {
        header('Location: /signin.html');
        exit; 
    }
    //if role doesn't match required then deny access
    //e.g. if page requires 'admin' role but user is 'user' then deny access
    if($_SESSION['role'] !== $requiredRole) {
        http_response_code(403);
        echo "Access denied. You do not have permission to access this page.";
        exit; 
    }
}

//check if user is an admin
function require_admin() {
    if(empty($_SESSION['user_ID'])) {
        header('Location: adminlogin.php');
        exit; 
    }
    
    if(empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo "Access denied. Admin privileges required.";
        exit; 
    }
}

//admin page access with alert and redirection if not admin
function require_admin_page($redirect = '/TEAM-13-/Draft/html/signin.php') { //
    if (empty($_SESSION['user_ID']) || ($_SESSION['role'] ?? '') !== 'admin') {
        $msg = json_encode("Access Denied. Admin privileges required.");
        $to  = json_encode($redirect);
        echo "<script>alert($msg); window.location.href = $to;</script>";
        exit;
    }
}

//check if user is a customer
function require_customer() {
    if(empty($_SESSION['user_ID'])) {
        header('Location: signin.php');
        exit; 
    }
    
    if(empty($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
        http_response_code(403);
        echo "Access denied. Customer access only.";
        exit; 
    }
}
//Notes:
//These functions should be included at the top of any page that should only be accessible to logged-in users. (profile, orders, admin pages etc.)
//Require_login() - If not logged in, then redirected to signin page.
//Require_role() - If logged in but role doesn't match required role, then access denied message shown. > (specific type of user access)

//If page requires login:
//.......//require_once '../../services/userFunctions.php';
//.......//require_login(); // ergo user must be logged in to view page

//For admin only page:
//.......//require_once '../../services/userFunctions.php';
//.......//require_admin(); //only admin accessible
?>