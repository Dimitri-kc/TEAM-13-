<?php
// reviewController.php - Handles review-related API requests
header('Content-Type: application/json');

include_once __DIR__ . '/../models/reviewModel.php';

$reviewModel = new ReviewModel();
$action = $_GET['action'] ?? '';

/*
 * INSERT A REVIEW
 * URL: reviewController.php?action=insert
 */
if ($action === 'insert') {

    $product_ID = $_POST['product_ID'] ?? 0;
    $user_ID    = $_POST['user_ID'] ?? 0;
    $rating     = $_POST['rating'] ?? 0;
    $comment    = $_POST['comment'] ?? '';

    $success = $reviewModel->addReview($product_ID, $user_ID, $rating, $comment);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Review added successfully" : "Failed to add review"
    ]);
}

/*
 * FETCH REVIEWS FOR A PRODUCT
 * URL: reviewController.php?action=fetch&product_ID=3
 */
elseif ($action === 'fetch') {

    $product_ID = $_GET['product_ID'] ?? 0;

    $reviews = $reviewModel->getReviewsByProduct($product_ID);
    echo json_encode($reviews);
}

/*
 * DELETE A REVIEW
 * URL: reviewController.php?action=delete
 */
elseif ($action === 'delete') {

    $review_ID = $_POST['review_ID'] ?? 0;

    $success = $reviewModel->deleteReview($review_ID);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Review deleted successfully" : "Failed to delete review"
    ]);
}

/*
 * UPDATE A REVIEW
 * URL: reviewController.php?action=update
 */
elseif ($action === 'update') {

    $review_ID = $_POST['review_ID'] ?? 0;
    $rating    = $_POST['rating'] ?? 0;
    $comment   = $_POST['comment'] ?? '';

    $success = $reviewModel->updateReview($review_ID, $rating, $comment);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Review updated successfully" : "Failed to update review"
    ]);
}

else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid or missing action"
    ]);
}
?>
