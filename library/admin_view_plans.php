<?php
session_start();
$message = '';

require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/admin_model.php');
$model = new model($db);

/* Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');


if (!empty($valid_dealer)) {


    $search_info['start'] = (empty($_GET['start'])) ? '' : $_GET['start'];
    $search_info['order'] = (empty($_GET['order'])) ? 'default' : $_GET['order'];
    $search_info['direction'] = (empty($_GET['direction'])) ? 'DESC' : $_GET['direction'];
    $search_info['demo'] = $_SESSION['demo'];
    $search_info['dealer_id'] = $_SESSION['dealer_id'];
    $content['PLAN_HEADER'] = '';
    $default_plan_header = '<div class="plan_number"><a href="view_plans?order=id&amp;direction=DESC"><b>Plan No.</b></a></div>';
    $default_date_header = '<div class="plan_date"><a href="view_plans?order=plan_date&amp;direction=DESC"><b>Date Sold</b></a></div>';
    $default_lname_header = '<div class="cust_last_name"><a href="view_plans?order=customer_last_name&amp;direction=ASC"><b>Last Name</b></a></div>';
    $default_fname_header = '<div class="cust_first_name"><a href="view_plans?order=customer_first_name&amp;direction=ASC"><b>First Name</b></a></div>';
    $default_vehicle_header = '<div class="vehicle"><a href="view_plans?order=vehicle&amp;direction=ASC"><b>Vehicle</b></a></div>';

    if ($search_info['direction'] == 'DESC') {
        $new_direction = 'ASC';
        $arrow = 'down';
    } else {
        $new_direction = 'DESC';
        $arrow = 'up';


    }
    switch ($search_info['order']) {
        case 'id':
            $content['PLAN_HEADER'] .= '<div class="plan_number"><a href="view_plans?order=id&amp;direction=' . $new_direction . '"><b>Plan No.</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['PLAN_HEADER'] .= $default_date_header . $default_lname_header . $default_fname_header . $default_vehicle_header;

            break;
        case 'plan_date':
            $content['PLAN_HEADER'] .= $default_plan_header;
            $content['PLAN_HEADER'] .= '<div class="plan_date"><a href="view_plans?order=plan_date&amp;direction=' . $new_direction . '"><b>Date Sold</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['PLAN_HEADER'] .= $default_lname_header . $default_fname_header . $default_vehicle_header;

            break;
        case 'customer_last_name':
            $content['PLAN_HEADER'] .= $default_plan_header . $default_date_header;
            $content['PLAN_HEADER'] .= '<div class="cust_last_name"><a href="view_plans?order=customer_last_name&amp;direction=' . $new_direction . '"><b>Last Name</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['PLAN_HEADER'] .= $default_fname_header . $default_vehicle_header;
            break;
        case 'customer_first_name':
            $content['PLAN_HEADER'] .= $default_plan_header . $default_date_header . $default_lname_header;
            $content['PLAN_HEADER'] .= '<div class="cust_first_name"><a href="view_plans?order=customer_first_name&amp;direction=' . $new_direction . '"><b>First Name</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['PLAN_HEADER'] .= $default_vehicle_header;
            break;
        case 'vehicle':
            $content['PLAN_HEADER'] .= $default_plan_header . $default_date_header . $default_lname_header . $default_fname_header;
            $content['PLAN_HEADER'] .= '<div class="vehicle"><a href="view_plans?order=vehicle&amp;direction=' . $new_direction . '"><b>Vehicle</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';

            break;
        default:
            $content['PLAN_HEADER'] .= $default_plan_header . $default_date_header . $default_lname_header . $default_fname_header . $default_vehicle_header;
            break;
    }
    if ($search_info['order'] == 'default') {
        $search_info['order'] = 'id';
    }
    if (!empty($_POST['search_plans'])) {
        $search_info['operator'] = (empty($_POST['operator'])) ? '' : $_POST['operator'];
        $search_info['criteria'] = (empty($_POST['criteria'])) ? '' : $_POST['criteria'];
        $search_info['inactive'] = (empty($_POST['inactive'])) ? '' : $_POST['inactive'];
        $search_info['term'] = (empty($_POST['term'])) ? '' : $_POST['term'];
        $search_info['order'] = $search_info['criteria'];

        if ($search_info['criteria'] == 'plan_date') {
            $search_info['term'] = date('Y-m-d', strtotime($search_info['term']));
        }

        if ($search_info['criteria'] == 'customer_phone') {
            $char_to_replace = array('-', '_', ' ', ')', '(', '.');
            $search_info['term'] = str_replace($char_to_replace, '', $search_info['term']);
        }


        $_SESSION['term'] = (empty($search_info['term'])) ? '' : $search_info['term'];
        $_SESSION['operator'] = (empty($search_info['operator'])) ? '' : $search_info['operator'];
        $_SESSION['criteria'] = (empty($search_info['criteria'])) ? '' : $search_info['criteria'];

    } else {
        $search_info['term'] = (empty($_SESSION['term'])) ? '' : $_SESSION['term'];
        $search_info['operator'] = (empty($_SESSION['operator'])) ? '' : $_SESSION['operator'];
        $search_info['criteria'] = (empty($_SESSION['criteria'])) ? '' : $_SESSION['criteria'];
    }

    $content['PLAN_INFO'] = $model->searchPlans($search_info, $db);
    if (empty($content['PLAN_INFO'])) {
        $content['PLAN_INFO'] = '<p style="font-style: italic">There are no plans matching your search</p>';

    }
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_logout.html');
    $body_copy = file_get_contents('templates/' . $page_id . '.html');


} else {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_login.html');
    $body_copy = file_get_contents('templates/login.html');
}
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
