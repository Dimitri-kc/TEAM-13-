<?php

require_once __DIR__ . '/../config/db_connect.php';

class FavouriteModel {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /* CLEAR ALL FAVOURITES*/

    public function clearFavourites($user_ID) {

        $stmt = $this->conn->prepare("
            DELETE FROM favourites
            WHERE user_ID = ?
        ");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $user_ID);

        return $stmt->execute();
    }

}