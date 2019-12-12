<?php
session_start();
$message = '';
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/site_model.php');
$model = new model($db);


$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');

/*Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

if (!empty($valid_dealer)) {
    $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_logout.html'), $header);
} else {
    $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_login.html'), $header);

}
$body_copy = file_get_contents('templates/' . $page_id . '.html');

$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
