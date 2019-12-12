<?php
$message = '';
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/site_model.php');
$model = new model($db);

/* Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

$content = $model->getHeaderValues($db, $site, $page_id);

if (!empty($valid_dealer)) {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_logout.html');
    $body_copy = file_get_contents('templates/' . $page_id . '.html');

} else {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_login.html');
    $body_copy = file_get_contents('templates/login.html');
}


$header = file_get_contents('templates/header.html');
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
