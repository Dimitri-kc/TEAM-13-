<?php

include_once 'backend/models/ReturnItemModel.php';

class ReturnItemController {

// Store the details associated with a new return request
public function store(int $returnId, array $items) {
$model = new ReturnItemModel();
return $model->createItems($returnId, $items);
}
}
?>