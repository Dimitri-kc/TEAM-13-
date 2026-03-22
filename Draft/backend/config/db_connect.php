<?php
//error_log("TTHIS IS DB_CONNECT: " . __FILE__);
 $servername = "localhost"; 
 $username = "cs2team13";
 $password = "D9Q9c7S6QwcRNAt4Zd4p7JnO2";
 $dbname = "cs2team13_db";

 //uncomment to test on local machine and comment above block
/* $servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "cs2team13_db"; */

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    throw new Exception("DB connection failed: " . $conn->connect_error);
}

//error_log("Database connection established in db_connect.php: " . __FILE__);
?>