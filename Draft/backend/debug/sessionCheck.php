<?php //sessionCheck.php - active user session check

if (session_status() === PHP_SESSION_NONE) {
    session_start(); //start session if not already started
}
header('Content-Type:application/json'); //content type set to JSON for response
echo json_encode([
    "session_ID" => session_id(), //return sesison ID for debugging
    "session" => $_SESSION //return session details (user_ID, name, role etc)
]);

/* Debug session check for admin and customer sessions - access control & user details across website (e.g. name, role etc > dashboard, checkout etc)
If session exists, user is logged in, and session details are valid, then return true for logged in status and user details for use across platform (e.g. name, role for access control etc)
If session does not exist, user is not logged in, return false for logged in status and null for user details across website
*/
?>