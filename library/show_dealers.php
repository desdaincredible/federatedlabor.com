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
    $search_info['start'] = (empty($_GET['start'])) ? '' : $_GET['start'];
    $search_info['order'] = (empty($_GET['order'])) ? 'default' : $_GET['order'];
    $search_info['direction'] = (empty($_GET['direction'])) ? 'DESC' : $_GET['direction'];
    $content['DEALER_HEADER'] = '';
    $default_dealer_header = '<div class="dealer_id"><a href="show_dealers?order=dealer_id&amp;direction=DESC"><b>Dealer Id</b></a></div>';
    $default_contact_header = '<div class="contact_last_name"><a href="show_dealers?order=contact_last_name&amp;direction=ASC"><b>Contact Name</b></a></div>';
    $default_bname_header = '<div class="business_name"><a href="show_dealers?order=business_name&amp;direction=ASC"><b>Business Name</b></a></div>';


    if ($search_info['direction'] == 'DESC') {
        $new_direction = 'ASC';
        $arrow = 'down';
    } else {
        $new_direction = 'DESC';
        $arrow = 'up';


    }
    switch ($search_info['order']) {
        case 'dealer_id':
            $content['DEALER_HEADER'] .= '<div class="dealer_id"><a href="show_dealers?order=dealer_id&amp;direction=' . $new_direction . '"><b>Dealer Id</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['DEALER_HEADER'] .= $default_contact_header . $default_bname_header;

            break;
        case 'contact_last_name':
            $content['DEALER_HEADER'] .= $default_dealer_header;
            $content['DEALER_HEADER'] .= '<div class="contact_last_name"><a href="show_dealers?order=contact_last_name&amp;direction=' . $new_direction . '"><b>Contact Name</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['DEALER_HEADER'] .= $default_bname_header;
            break;
        case 'business_name':
            $content['DEALER_HEADER'] .= $default_dealer_header . $default_contact_header;
            $content['DEALER_HEADER'] .= '<div class="business_name"><a href="show_dealers?order=business_name&amp;direction=' . $new_direction . '"><b>Business Name</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            break;
        default:
            $content['DEALER_HEADER'] .= $default_dealer_header . $default_contact_header . $default_bname_header;
            break;
    }

    if (!empty($content['DEALER_HEADER'])) {
        $content['DEALER_HEADER'] .= '<div class="clearBoth"></div>';

    }
    if ($search_info['order'] == 'default') {
        $search_info['order'] = 'dealer_id';
    }
    if (!empty($_POST['show_dealers'])) {
        $search_info['operator'] = (empty($_POST['operator'])) ? '' : $_POST['operator'];
        $search_info['criteria'] = (empty($_POST['criteria'])) ? '' : $_POST['criteria'];
        $search_info['inactive'] = (empty($_POST['inactive'])) ? '' : $_POST['inactive'];
        if (!empty($search_info['inactive'])) {
            $search_info['term'] = 'view_inactive';

        } else {
            $search_info['term'] = (empty($_POST['term'])) ? '' : $_POST['term'];


        }
        $search_info['order'] = 'dealer_id';
        $search_info['direction'] = 'ASC';
        $_SESSION['term'] = $search_info['term'];
        $_SESSION['operator'] = $search_info['operator'];
        $_SESSION['criteria'] = $search_info['criteria'];
        $_SESSION['inactive'] = $search_info['inactive'];


    } else {
        $search_info['term'] = $_SESSION['term'];
        $search_info['operator'] = $_SESSION['operator'];
        $search_info['criteria'] = $_SESSION['criteria'];
        $search_info['inactive'] = $_SESSION['inactive'];


    }

    $dealers = $model->searchDealers($search_info, $db);
    if (empty($dealers)) {
        $content['DEALER_INFO'] = '<p style="font-style: italic">There are no dealers matching your search</p>';

    } else {
        $content['DEALER_INFO'] = $dealers;

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
