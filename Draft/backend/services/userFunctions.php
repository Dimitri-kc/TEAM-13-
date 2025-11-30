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
//Notes:
//These functions should be included at the top of any page that should only be accessible to logged-in users. (profile, orders, admin pages etc.)
//Require_login() - If not logged in, then redirected to signin page.
//Require_role() - If logged in but role doesn't match required role, then access denied message shown. > (specific type of user access)

//If page requires login:
//.......//require_once '../../services/userFunctions.php';
//.......//require_login(); // ergo user must be logged in to view page

//For admin only page:
//.......//require_once '../../services/userFunctions.php';
//.......//require_role('admin'); //only admin accessible
?>