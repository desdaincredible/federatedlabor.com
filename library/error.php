<?php
session_start();
$message = '';
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);

if (isset($_SESSION['admin_id'])) {
    require_once('model/admin_model.php');
} else {
    require_once('model/site_model.php');

}
$model = new model($db);


$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');

if ($site == 'mrh') {
    /*Check to see if they are already logged in */
    $valid_dealer = $model->checkSession($db);

    if (!empty($valid_dealer)) {
        $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_logout.html'), $header);

    } else {
        $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_login.html'), $header);
    }
}

if (isset($_SESSION['admin_id'])) {
    $content['ADMIN_MENU'] = file_get_contents('templates/admin_menu.html');

} else {
    $content['ADMIN_MENU'] = '';

}
$body_copy = file_get_contents('templates/' . $page_id . '.html');

switch ($_GET['eid']) {
    case 1:
    case 2:
        $message = "There has been a problem creating this plan";
        break;
    case 3:
        $message = "This plan is expired, no tire has been found<br> matching that DOT#, or the tire has already had a claim.";
        break;
    case 4:
        $message = "There has been an error in processing this claim.";
        break;
    case 5:
        $message = "This account is inactive because it is past due.";
        break;
    case 6:
        $message = "This account is inactive.";
        break;
    case 7:
        $message = "There was a problem adding the new dealer.";
        break;
    case 8:
        $message = "There was a problem updating this dealer.";
        break;
    case 9:
        $message = "There was a problem inserting this data.";
        break;
    case 10:
        $message = "We're sorry, but this policy has expired.";
        break;
    case 11:
        $message = "This plan could not be deleted, it may already have been deleted.";
        break;
    case 12:
        $message = "There was an error and the plan was not updated.";
        break;
    case 13:
        $message = "We were unable to update your user details.";
        break;
}


$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
