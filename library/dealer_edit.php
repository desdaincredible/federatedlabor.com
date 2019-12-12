<?php
$message = '';
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/admin_model.php');
$model = new model($db);
/* Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');
$content['ADMIN_MENU'] = file_get_contents('templates/admin_menu.html');


if (!empty($valid_dealer)) {
    $body_copy = file_get_contents('templates/' . $page_id . '.html');

    $dealer_id = intval($_REQUEST['did']);
    $show_dealer = $model->getDealer($dealer_id, $db);
    $show_dealer = array_change_key_case($show_dealer, CASE_UPPER);
    $content = array_merge($show_dealer, $content);


} else {
    $body_copy = file_get_contents('templates/login.html');
}
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

