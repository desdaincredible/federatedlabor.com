<?php
$message = '';
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/admin_model.php');
$model = new model($db);
$valid_dealer = $model->checkSession($db);

if ($valid_dealer) {
    header('Content-Type: application/json');

    $type = $_GET['type'];
    $value = $_GET['value'];
    $claims = $model->getClaimsLabor($db, $type, $value);
    echo json_encode($claims);
} else {
    echo 'You Are Not Logged in.';
}