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

$header = file_get_contents('templates/header.html');


if (!empty($valid_dealer)) {
    $message = $model->getDealerMessage($db);

    $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_logout.html'), $header);
    $body_copy = file_get_contents('templates/' . $page_id . '.html');

} else {
    $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_login.html'), $header);
    $body_copy = file_get_contents('templates/login.html');
}

$content['MESSAGE'] = $message;

if ($site == "car") {
    $content['DEALER_NAME'] = 'Certified Auto Repair Centers';

    if ($_SESSION['wfr'] == 1) {
        $content['DEALER_NAME'] = 'Worry Free';

    }

}


$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

