<?php
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/admin_model.php');
$model = new model($db);
$message = '';


/* Check to see if they are already logged in */
if (!empty($_POST['username'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password'] . sha1(substr($_POST['password'], 0, 1)));

    $valid_dealer = $model->newSession($db, $username, $password);
} else if (!empty($_GET['logout'])) {
    $clear_session = $model->deleteSession($db);
    $message = '<span style="color: red">You have successfully logged out</span>';
    $valid_dealer = 0;
    unset($_SESSION);
} else {
    $valid_dealer = $model->checkSession($db);

}

$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');
$content['ADMIN_MENU'] = file_get_contents('templates/admin_menu.html');

if (!empty($valid_dealer)) {

    $body_copy = file_get_contents('templates/' . $page_id . '.html');

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

