<?php

include_once 'backend/models/returnsModel.php';

class ReturnsController {

public function index() {
$model = new ReturnsModel();
return $model->getAll();
}

public function show($id) {
$model = new ReturnsModel();
return $model->getById($id);
}

public function store($orderID, $userID, $productID, $reason, $status) {
$model = new ReturnsModel();
return $model->create($orderID, $userID, $productID, $reason, $status);
}

public function update($id, $status) {
$model = new ReturnsModel();
return $model->update($id, $status);
}

public function destroy($id) {
$model = new ReturnsModel();
return $model->delete($id);
}
}

?>
