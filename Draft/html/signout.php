<?php
// signout.php - ends the session and redirects back to signin with a flag

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear session variables
$_SESSION = [];

// Clear session cookie 
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to signin with a flag so we can show a message
header("Location: signin.php?logout=1");
exit;