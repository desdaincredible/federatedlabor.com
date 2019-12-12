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
$content['ADMIN_MENU'] = file_get_contents('templates/admin_menu.html');


if (!empty($valid_dealer)) {
    $search_info['start'] = (empty($_GET['start'])) ? '' : $_GET['start'];
    $search_info['order'] = (empty($_GET['order'])) ? 'default' : $_GET['order'];
    $search_info['direction'] = (empty($_GET['direction'])) ? 'DESC' : $_GET['direction'];
    $content['COURSE_HEADER'] = '';
    $default_course_header = '<div class="course_id"><a href="show_courses?order=course_number&amp;direction=DESC"><b>Plan Id</b></a></div>';
    $default_contact_header = '<div class="customer_name"><a href="show_courses?order=customer_last_name&amp;direction=ASC"><b>Customer Name</b></a></div>';
    $default_bname_header = '<div class="business_name"><a href="show_courses?order=business_name&amp;direction=ASC"><b>Business Name</b></a></div>';


    if ($search_info['direction'] == 'DESC') {
        $new_direction = 'ASC';
        $arrow = 'down';
    } else {
        $new_direction = 'DESC';
        $arrow = 'up';


    }
    switch ($search_info['order']) {
        case 'course_number':
            $content['COURSE_HEADER'] .= '<div class="course_id"><a href="show_courses?order=course_number&amp;direction=' . $new_direction . '"><b>Plan Id</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['COURSE_HEADER'] .= $default_contact_header . $default_bname_header;

            break;
        case 'customer_last_name':
            $content['COURSE_HEADER'] .= $default_course_header;
            $content['COURSE_HEADER'] .= '<div class="customer_name"><a href="show_courses?order=customer_last_name&amp;direction=' . $new_direction . '"><b>Customer Name</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['COURSE_HEADER'] .= $default_bname_header;
            break;
        case 'business_name':
            $content['COURSE_HEADER'] .= $default_course_header . $default_contact_header;
            $content['COURSE_HEADER'] .= '<div class="business_name"><a href="show_courses?order=business_name&amp;direction=' . $new_direction . '"><b>Business Name</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            break;
        default:
            $content['COURSE_HEADER'] .= $default_course_header . $default_contact_header . $default_bname_header;
            break;
    }

    if (!empty($content['COURSE_HEADER'])) {
        $content['COURSE_HEADER'] .= '<div class="clearBoth"></div>';

    }
    if ($search_info['order'] == 'default') {
        $search_info['order'] = 'course_number';
    }
    if (!empty($_POST['show_courses'])) {
        $search_info['operator'] = (empty($_POST['operator'])) ? '' : $_POST['operator'];
        $search_info['criteria'] = (empty($_POST['criteria'])) ? '' : $_POST['criteria'];
        $search_info['inactive'] = (empty($_POST['inactive'])) ? '' : $_POST['inactive'];
        if (!empty($search_info['inactive'])) {
            $search_info['term'] = 'view_inactive';

        } else {
            $search_info['term'] = (empty($_POST['term'])) ? '' : $_POST['term'];


        }
        $search_info['order'] = 'course_number';
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

    $courses = $model->searchCourses($search_info, $db);
    if (empty($courses)) {
        $content['COURSE_INFO'] = '<p style="font-style: italic">There are no courses matching your search</p>';

    } else {
        $content['COURSE_INFO'] = $courses;

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
