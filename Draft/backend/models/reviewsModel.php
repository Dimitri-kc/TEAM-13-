<?php

include_once __DIR__ . '/../../config/db_connect.php';

class ReviewModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    
    public function addReview($product_ID, $user_ID, $rating, $comment) {
        $stmt = $this->conn->prepare("
            INSERT INTO reviews (product_ID, user_ID, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $product_ID, $user_ID, $rating, $comment);

        return $stmt->execute();
    }

        public function getReviewsByProduct($product_ID) {
        $stmt = $this->conn->prepare("
            SELECT r.review_ID, r.rating, r.comment, r.review_date, u.name AS user_name
            FROM reviews r
            JOIN users u ON r.user_ID = u.user_ID
            WHERE r.product_ID = ?
            ORDER BY r.review_date DESC
        ");
        $stmt->bind_param("i", $product_ID);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function deleteReview($review_ID) {
        $stmt = $this->conn->prepare("
            DELETE FROM reviews WHERE review_ID = ?
        ");
        $stmt->bind_param("i", $review_ID);

        return $stmt->execute();
    }

    public function updateReview($review_ID, $rating, $comment) {
        $stmt = $this->conn->prepare("
            UPDATE reviews
            SET rating = ?, comment = ?
            WHERE review_ID = ?
        ");
        $stmt->bind_param("isi", $rating, $comment, $review_ID);

        return $stmt->execute();
    }
}
?>
