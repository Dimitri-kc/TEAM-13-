<?php

require_once __DIR__ . '/../models/favouriteModel.php';

class FavouriteController {

    private $favouriteModel;

    public function __construct() {
        $this->favouriteModel = new FavouriteModel();
    }

    /* CLEAR FAVOURITES */

    public function clear() {

        session_start();

        $user_ID = $_SESSION['user_ID'] ?? null;

        if (!$user_ID) {

            echo json_encode([
                "status" => "error",
                "message" => "User not logged in"
            ]);

            return;
        }

        $success = $this->favouriteModel->clearFavourites($user_ID);

        echo json_encode([
            "status" => $success ? "success" : "error"
        ]);
    }

}