<?php
class User {
    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../config/db_connect.php'; // Adjusted path to include db_connect.php
        global $conn; //variable from db_connect.php for database connection
        $this->conn = $conn;
    }
    
    //registration method to register new user & insert details into database user table
    public function register($name, $surname, $email, $phone, $address, $hashedPassword, $role) {
        try { //check if email already exists in database
            $check = $this->conn->prepare("SELECT user_ID FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) { 
                $check->close();
                return ["success" => false, "error" => "exists"]; //if email exits, registration fails
            }
            $check->close();//if email doesn't exist, proceed with registration 
            
            //insert new user in database
            $stmt = $this->conn->prepare("INSERT INTO users (name, surname, email, phone, password, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)"); // Using prepared statements to prevent SQL injection
            
            $stmt->bind_param("sssssss", $name, $surname, $email, $phone, $hashedPassword, $address, $role);
            $registrationSuccess = $stmt->execute();
            //if new user then redirect to changepassword.php
            $newUserID = $registrationSuccess ? $this->conn->insert_id : null; //retreive id of newly registered user, create session and password change redirection
            $stmt->close(); //close after execution freeing resources

            if ($registrationSuccess) {
                return ["success" => true, "user_ID" => $newUserID];
            } else {
                return ["success" => false];
            }
            
        } catch (Exception $e) {
            return ["success" => false, "error" => $e->getMessage()]; //return error message if exception occurs
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