<?php

require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
session_start();
require_once('model/site_model.php');
$model = new model($db);
$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');

foreach ($content as $key => $value) {
    $header = str_replace('{' . strtoupper($key) . '}', $value, $header);
}

if (!empty($_SESSION['dealer_logged']) || !empty($_POST['username'])) {
    if (!empty($_POST['username'])) {
        $username = mysql_real_escape_string($_POST['username']);
        $password = mysql_real_escape_string($_POST['password']);


    }


    $body_copy = file_get_contents('templates/thank_you.html');

} else {
    $body_copy = file_get_contents('templates/log_in.html');
}
$footer = file_get_contents('templates/footer.html');
echo $header . $body_copy . $footer;
