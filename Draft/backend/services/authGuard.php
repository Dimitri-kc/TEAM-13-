<?php //session management and access control functions for user authentication and role-based access in the application
//prevent stale sessions from crashing the app & ensure proper sesison clean up upon logout

declare(strict_types=1); //enforce strict typing for better error detection

//logs out the user by clearing session data and redirecting to the login page
function logoutAndRedirect(string $loginPath = 'login.php'): never 
{
    if (session_status() === PHP_SESSION_ACTIVE) { //if session is active, clear session data and destroy session
        $_SESSION = [];
        if (ini_get('session.use_cookies')) { //get session cookie parameters and set cookie to expire in the past to remove it
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    header('Location: ' . $loginPath);
    exit();
}

// Returns user_ID if logged in and valid, or null if guest
// Does NOT redirect - allows guests to continue
function getValidSessionUser(mysqli $conn): ?int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userId = $_SESSION['user_ID'] ?? null;

    if (!is_numeric($userId) || (int)$userId <= 0) { // Not logged in or invalid user ID
        return null; // Guest
    }

    $userId = (int)$userId;

    $stmt = $conn->prepare('SELECT 1 FROM users WHERE user_ID = ? LIMIT 1'); //check if user exists in DB
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        // User no longer exists in DB, clear stale session
        logoutAndRedirect();
    }

    return $userId;
}

// requires a valid session user and checks if the user exists in the database, otherwise logs out and redirects to login page
function requireValidSessionUser(mysqli $conn, string $loginPath = 'login.php'): int
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userId = $_SESSION['user_ID'] ?? null; //get user id from sesison

    if (!is_numeric($userId) || (int)$userId <= 0) { //if user id is not valid, log out and redirect
        logoutAndRedirect($loginPath);
    }

    $userId = (int)$userId;

    $stmt = $conn->prepare('SELECT 1 FROM users WHERE user_ID = ? LIMIT 1'); //check if user exists in DB
    if (!$stmt) { //if statement preparation fails
        logoutAndRedirect($loginPath);
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) { //if no user found with given ID, log out and redirect
        logoutAndRedirect($loginPath);
    }

    return $userId;
}
?>