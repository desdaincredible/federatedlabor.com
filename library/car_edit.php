<?php
session_start();
$message = '';
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

    $content['MAKES'] = $model->getMakes($db);

    if (!empty($_POST['add_make'])) {
        $model->insertMake(mysql_real_escape_string($_POST['make_name']), $db);
        $message = '<span style="color: #CC0033;">New car make successfully added</span>';

    }
    if (!empty($_POST['add_model'])) {
        $model->insertModel(mysql_real_escape_string($_POST['make_id']), mysql_real_escape_string($_POST['model_name']), $db);
        $message = '<span style="color: #CC0033;">New car model successfully added</span>';

    }
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

