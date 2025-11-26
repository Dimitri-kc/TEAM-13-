<?php
class User {
    private $conn;

    public function __construct() {
        include_once __DIR__ . '/backend/db_connect.php';
        $this->conn = $dbConnection;
    }
    
    //registration method to add new user
    public function register($username, $email, $hashedPassword, $role) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"); // Using prepared statements to prevent SQL injection
        return $stmt->execute();
    }
    
    //login method to authenticate user
    public function login($email, $hashedPassword) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?"); //search for user via email
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC); //fetch usre data
        if ($user && password_verify($hashedPassword, $user['password'])) { //checks if password matches hashed password in database
            // Set session variables
            $_SESSION['user_ID'] = $user['user_ID']; //
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; //customer or admin
            return $user;
        }
        return false;
        echo "Login failed. Invalid email or password.";
    }
}
//check if setting session variables necessary here or in controller as currently in both
?>