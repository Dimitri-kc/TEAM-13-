<?php

class ReturnsModel {

private $conn;

public function __construct() {
include_once __DIR__ . '/../db_connect.php';
$this->conn = $conn;
}
// Get ALL returns.
public function getAll() {
$sql = "SELECT * FROM returns";
$result = $this->conn->query($sql);
return $result->fetch_all(MYSQLI_ASSOC);
}
// Get ONE return.
public function getById($id) {
$stmt = $this->conn->prepare("SELECT * FROM returns WHERE return_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
return $stmt->get_result()->fetch_assoc();
}
// Create return request.
//Removed productID + returns insert ID.
public function create($orderID, $userID, $reason, $status) {
$stmt = $this->conn->prepare(
"INSERT INTO returns (order_ID, user_ID, reason, status)
VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("iiss", $orderID, $userID, $reason, $status);

if ($stmt->execute()) {
return $this->conn->insert_id;
}
return false;
}
// Update return status.
public function update($id, $status) {
$stmt = $this->conn->prepare("UPDATE returns SET status=? WHERE return_ID=?");
$stmt->bind_param("si", $status, $id);
return $stmt->execute();
}
// Delete return
public function delete($id) {
$stmt = $this->conn->prepare("DELETE FROM returns WHERE return_ID = ?");
$stmt->bind_param("i", $id);
return $stmt->execute();
}
}