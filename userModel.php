<?php
class User {
    private $conn;

    public function __construct() {
        include_once __DIR__ . '/../config/db_connect.php'; // Adjusted path to include db_connect.php
        $this->conn = $dbConnection;
    }
    
    //registration method to register new user & insert details into database user table
    public function register($name, $surname, $email, $address, $hashedPassword, $role) {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, address, role) VALUES (?, ?, ?, ?, ?)"); // Using prepared statements to prevent SQL injection
        return $stmt->execute([$name, $email, $hashedPassword, $address, $role]); //execute the statement with user details
    }
    
    //login method to authenticate user
    public function login($email, $hashedPassword) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?"); //search for user via email
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); //fetch user data
        //password verification handled in controller
    }
}
?>