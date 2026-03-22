<?php
// reviewsModel.php
require_once __DIR__ . '/../config/db_connect.php';

class Review {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Function to fetch data (GET)
    public function getReviewsByProduct($product_ID) {
        $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE product_ID = ?");
        $stmt->bind_param("i", $product_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // : Function to insert data (POST)
    public function addReview($product_ID, $user_ID, $rating, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO reviews (product_ID, user_ID, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $product_ID, $user_ID, $rating, $comment);
        return $stmt->execute();
    }
}
