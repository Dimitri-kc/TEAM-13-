<?php
// $servername = "localhost"; 
// $username = "cs2team13";
// $password = "D9Q9c7S6QwcRNAt4Zd4p7JnO2";
// $dbname = "cs2team13_db";

$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "cs2team13_db";

$conn = new mysqli($servername, $username, $password, $dbname);

/* if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} */
if ($conn->connect_error) {
    throw new Exception("DB connection failed: " . $conn->connect_error);
}
