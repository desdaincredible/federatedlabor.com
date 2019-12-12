<?php
$message = '';
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/admin_model.php');
$model = new model($db);


$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');
$content['ADMIN_MENU'] = file_get_contents('templates/admin_menu.html');

$body_copy = file_get_contents('templates/' . $page_id . '.html');

switch ($_GET['mid']) {
    case 1:
        $message = "The new dealer has been added successfully.";

        break;
    case 2:
        $message = "The dealer has been inactivated due to payment being past due";
        break;
    case 3:
        $message = "The dealer's status has been updated.";
        break;
    case 4:
        $message = "The dealer has been deleted.";
        break;
    case 5:
        $message = "The plan has been deleted.";
        break;
    case 6:
        $message = "The plan has been updated.";
        break;
}


$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
