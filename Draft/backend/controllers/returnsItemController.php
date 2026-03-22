<?php
require_once __DIR__ . '/../models/returnItemModel.php';

class ReturnItemController {

    private $model;

    public function __construct() {
        $this->model = new ReturnItemModel();
    }

    public function store($returnID, $items) {
    // We pass the data to the model to handle the insertion logic
    return $this->model->createMultiple($returnID, $items);
    }

    // Get items associated with a specific return ID
    public function showByReturn($returnID) {
        return $this->model->getByReturnId($returnID);
    }
}

?>