<?php
class User {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../config/db_connect.php'; // Adjusted path to include db_connect.php
        $this->conn = $conn;
    }
    
    //registration method to register new user & insert details into database user table
    public function register($name, $surname, $email, $phone, $address, $hashedPassword, $role) {
        try { //check if email already exists in database
            $check = $this->conn->prepare("SELECT user_ID FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->fetch()) { 
                return false; //if email exits, registration fails
            }
        $stmt = $this->conn->prepare("INSERT INTO users (name, surname, email, phone, password, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)"); // Using prepared statements to prevent SQL injection

        $stmt->bind_param("sssssss", $name, $surname, $email, $phone, $hashedPassword, $address, $role);
        $registrationSuccess = $stmt->execute();
        $stmt->close();
        return $registrationSuccess; //return true if registration successful

        } catch (Exception $e) {
            return false; //registration failed due to errors
        }


    } 
    
    //login method to authenticate user
    public function login($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?"); //search for user via email
        $stmt->bind_param("s", $email);
        $stmt->execute(); 

        $result=$stmt->get_result();
        $user=$result->fetch_assoc(); //fetch the user data as an associative array
        $stmt->close();
        return $user;
        //password verification handled in controller
    }
    //logout handled in routes via session destruction
    //SECURITY NOTE:
    //SQL queries pre-compiled, user data treated as a parameter (not executable code), preventing SQL injections
}
?>