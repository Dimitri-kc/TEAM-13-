<?php

include_once 'backend/models/returnsModel.php';

class ReturnsController {

    // Get ALL returns
public function index() {
$model = new ReturnsModel();
return $model->getAll();
}

// Get ONE return by ID
public function show($id) {
$model = new ReturnsModel();
return $model->getById($id);
}

// Create a new return request
public function store($orderID, $userID, $reason, $status) {
$model = new ReturnsModel();
return $model->create($orderID, $userID, $productID, $reason, $status);
}

// Update return status
public function update($id, $status) {
$model = new ReturnsModel();
return $model->update($id, $status);
}

// Delete return record
public function destroy($id) {
$model = new ReturnsModel();
return $model->delete($id);
}
}

?>
