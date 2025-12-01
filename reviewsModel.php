<?php
// ReviewModel.php - database class for product reviews
include_once __DIR__ . '/../../config/db_connect.php';

class Review {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Add a new review
    public function addReview($user_ID, $product_ID, $rating, $comment) {
        $stmt = $this->conn->prepare("
            INSERT INTO reviews (user_ID, product_ID, rating, comment) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $user_ID, $product_ID, $rating, $comment);
        return $stmt->execute();
    }

    // Get all reviews for a product
    public function getReviewsByProduct($product_ID) {
        $stmt = $this->conn->prepare("
            SELECT * FROM reviews WHERE product_ID = ?
        ");
        $stmt->bind_param("i", $product_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all reviews by a specific user
    public function getReviewsByUser($user_ID) {
        $stmt = $this->conn->prepare("
            SELECT * FROM reviews WHERE user_ID = ?
        ");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update a review
    public function updateReview($review_ID, $rating, $comment) {
        $stmt = $this->conn->prepare("
            UPDATE reviews SET rating = ?, comment = ? WHERE review_ID = ?
        ");
        $stmt->bind_param("isi", $rating, $comment, $review_ID);
        return $stmt->execute();
    }

    // Delete a review
    public function deleteReview($review_ID) {
        $stmt = $this->conn->prepare("
            DELETE FROM reviews WHERE review_ID = ?
        ");
        $stmt->bind_param("i", $review_ID);
        return $stmt->execute();
    }
}
?>
