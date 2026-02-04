<?php
// reviewsController.php
header('Content-Type: application/json');

require_once __DIR__ . '/reviewsModel.php';

class ReviewsController {

    private $model;

    public function __construct() {
        $this->model = new Review();
    }

    // POST: add review
    public function insert() {

        $product_ID = $_POST['product_ID'] ?? null;
        $user_ID    = $_POST['user_ID'] ?? null;
        $rating     = $_POST['rating'] ?? null;
        $comment    = $_POST['comment'] ?? null;

        if (!$product_ID || !$user_ID || !$rating || !$comment) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $success = $this->model->addReview(
            $product_ID,
            $user_ID,
            $rating,
            $comment
        );

        echo json_encode(
            $success
                ? ["status" => "success", "message" => "Review added"]
                : ["status" => "error", "message" => "Failed to add review"]
        );
    }

    // GET: fetch reviews by product
    public function fetchByProduct() {

        $product_ID = $_GET['product_ID'] ?? null;

        if (!$product_ID) {
            echo json_encode([
                "status" => "error",
                "message" => "Product ID required"
            ]);
            return;
        }

        $reviews = $this->model->getReviewsByProduct($product_ID);

        echo json_encode([
            "status" => "success",
            "data" => $reviews
        ]);
    }
}
