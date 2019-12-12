<?php
$site = 'ntw';
//set_include_path(get_include_path() . PATH_SEPARATOR . 'library');
session_start();
require_once('./../library/db/config.php');
require_once('./../library/db/db.php');
$db = new Database();
require_once('./../library/model/site_model.php');
$model = new model($db);

$term = strtolower($_GET['term']);
$make_id = (empty($_GET['vmid'])) ? 0 : strtolower($_GET['vmid']);

echo $model->getModels($db, $term, $make_id);
