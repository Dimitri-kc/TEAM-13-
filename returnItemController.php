<?php

public function store(int $returnId, array $items) {
if (empty($items)) {
return false; 
}

$model = new ReturnItemModel();
return $model->createItems($returnId, $items);
}
}
?>