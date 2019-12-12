<?php
session_start();
$message = '';


if (date('U') < date('U', strtotime('2010-10-01'))) {
    $message .= '<p style="color: #ff0000; font-weight: bold"><br>Please Note: For security reasons, username and password are now Case Sensitive!</p>';

};


require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/site_model.php');
$model = new model($db);

/* Check to see if they are already logged in */
if (!empty($_POST['username'])) {
    $username = mysql_real_escape_string($_POST['username']);
    $password = md5($_POST['password'] . sha1(substr($_POST['password'], 0, 1)));
    $valid_dealer = $model->newSession($db, $username, $password, $site);
} else if (!empty($_POST['logout']) || !empty($_GET['logout'])) {
    $clear_session = $model->deleteSession($db);
    $message = '<span style="color: red">You have successfully logged out</span>';
    $valid_dealer = 0;
    unset($_SESSION);
} else {
    $valid_dealer = $model->checkSession($db);

}

if (!empty($valid_dealer)) {
    header('Location: dealer_index');

} else {
    if (!empty($_POST['username'])) {
        $message = '<span style="color: #B51010">Invalid Username and/or Password</span>';

    }
    $body_copy = file_get_contents('templates/' . $page_id . '.html');

}

$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');


if (!empty($valid_dealer)) {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_logout.html');

} else {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_login.html');
}
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
