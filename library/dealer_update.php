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
    if (!empty($_POST['set_status'])) {
        $content['change_status'] = 1;
        $content['dealer_id'] = $_POST['dealer_id'];
        switch ($_POST['set_status']) {
            case 'past_due':
                $content['inactive'] = 5;
                break;
            case 'general':
                $content['inactive'] = 6;
                break;
            case 'delete':
                $content['inactive'] = -1;
                break;
            default:
                $content['inactive'] = 0;
                break;
        }
    } else {
        reset($_POST);
        while (list($k, $v) = each($_POST)) {
            $content[strtolower($k)] = $v;
        }

    }

    $dealer_complete = $model->updateDealer($content, $site, $db);
    if ($dealer_complete == "success") {

        switch ($content['inactive']) {
            case 5:
                $mid = 2;
                break;
            case 6:
                $mid = 3;
                break;
            case '-1':
                $mid = 4;
                break;
            default:
                $mid = 3;
                break;
        }
        header("Location: success?mid=$mid");

    } else {
        header("Location: error?eid=$eid");


    }
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

