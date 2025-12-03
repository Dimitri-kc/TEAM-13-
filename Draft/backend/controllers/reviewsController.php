<?php
// Review controller – handles review actions
header('Content-Type: application/json');

include_once __DIR__ . '/../models/reviewsModel.php';

$reviewModel = new ReviewModel();
$action = $_GET['action'] ?? '';

/* Add a review */
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

/* Fetch reviews for a product */
elseif ($action === 'fetch') {

    $product_ID = $_GET['product_ID'] ?? 0;
    echo json_encode($reviewModel->getReviewsByProduct($product_ID));
}

/* Delete a review */
elseif ($action === 'delete') {

    $review_ID = $_POST['review_ID'] ?? 0;

    $success = $reviewModel->deleteReview($review_ID);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Review deleted successfully" : "Failed to delete review"
    ]);
}

/* Update a review */
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

/* Invalid action */
else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid or missing action"
    ]);
}
?>
