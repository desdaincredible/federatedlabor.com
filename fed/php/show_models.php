<?php
session_start();
$db = new Database();
$model = new model($db);

$term = strtolower($_GET['term']);
$make_id = (empty($_GET['vmid'])) ? 0 : strtolower($_GET['vmid']);

echo $model->getModels($db, $term, $make_id);
