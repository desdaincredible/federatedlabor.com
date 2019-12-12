<?php
$site = 'ntw';
//set_include_path(get_include_path() . PATH_SEPARATOR . 'library');
session_start();
require_once('/home/ntwclaim/public_html/library/db/config.php');
require_once('/home/ntwclaim/public_html/library/db/db.php');
$db = new Database();
require_once('/home/ntwclaim/public_html/library/model/site_model.php');
$model = new model($db);

$term = strtolower($_GET['term']);

echo $model->getTireMakes($db, $term);
