<?php
class User {
    private $conn;

    public function __construct() {
        include_once __DIR__ . '/backend/db_connect.php';
        $this->conn = $dbConnection;
    }

    public function register($username, $email, $hashedPassword, $role) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"); // Using prepared statements to prevent SQL injection
        return $stmt->execute();
    }
}
?>