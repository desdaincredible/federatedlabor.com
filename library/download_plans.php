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

    $plan_start_month = (empty($_POST['start_month'])) ? '' : $_POST['start_month'];
    $plan_start_day = (empty($_POST['start_day'])) ? '' : $_POST['start_day'];
    $plan_start_year = (empty($_POST['start_year'])) ? '' : $_POST['start_year'];
    $plan_start_date = (empty($plan_start_month)) ? date('Y-m-d') : $plan_start_year . '-' . $plan_start_month . '-' . $plan_start_day;
    $plan_end_month = (empty($_POST['end_month'])) ? '' : $_POST['end_month'];
    $plan_end_day = (empty($_POST['end_day'])) ? '' : $_POST['end_day'];
    $plan_end_year = (empty($_POST['end_year'])) ? '' : $_POST['end_year'];
    $plan_end_date = (empty($plan_end_month)) ? date('Y-m-d') : $plan_end_year . '-' . $plan_end_month . '-' . $plan_end_day;

    $get_plans = (empty($_POST['show_plans'])) ? '' : 1;


    /* Populate the date dropdowns */
    $content['START_MONTH'] = '';

    $this_month = (!empty($plan_start_month)) ? $plan_start_month : date('m');
    for ($month = 1; $month <= 12; ++$month) {
        if ($month < 10) {
            $show_start_month = '0' . $month;
        } else {
            $show_start_month = $month;
        }

        if ($show_start_month == $this_month) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $content['START_MONTH'] .= '<option value="' . $show_start_month . '" ' . $selected . '>' . $show_start_month . '</option>';
    }

    $content['END_MONTH'] = '';

    $this_month = (!empty($plan_end_month)) ? $plan_end_month : date('m');
    for ($month = 1; $month <= 12; ++$month) {
        if ($month < 10) {
            $show_end_month = '0' . $month;
        } else {
            $show_end_month = $month;
        }

        if ($show_end_month == $this_month) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $content['END_MONTH'] .= '<option value="' . $show_end_month . '" ' . $selected . '>' . $show_end_month . '</option>';
    }

    $content['START_DAY'] = '';
    $this_day = (!empty($plan_start_day)) ? $plan_start_day : date('d');
    for ($day = 1; $day <= 31; ++$day) {
        if ($day < 10) {
            $show_start_day = '0' . $day;
        } else {
            $show_start_day = $day;
        }
        if ($show_start_day == $this_day) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $content['START_DAY'] .= '<option value="' . $show_start_day . '" ' . $selected . '>' . $show_start_day . '</option>';
    }
    $content['END_DAY'] = '';
    $this_day = (!empty($plan_end_day)) ? $plan_end_day : date('d');
    for ($day = 1; $day <= 31; ++$day) {
        if ($day < 10) {
            $show_end_day = '0' . $day;
        } else {
            $show_end_day = $day;
        }
        if ($show_end_day == $this_day) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        $content['END_DAY'] .= '<option value="' . $show_end_day . '" ' . $selected . '>' . $show_end_day . '</option>';
    }
    $content['START_YEAR'] = '';
    $earliest_year = $model->getYear('plan_date', 'plan_registration ', 'plan_date', $db);
    $current_year = date('Y');
    $this_year = (!empty($plan_start_year)) ? $plan_start_year : date('Y');
    $year_count = $current_year - $earliest_year;

    if ($year_count == 0) {
        $content['START_YEAR'] .= '<option value="' . $this_year . '" selected>' . $this_year . '</option>';
    } else {
        $this_year = date('Y');

        for ($year = $current_year; $year >= $earliest_year; --$year) {
            $show_start_year = $year;
            if ($show_start_year == $this_year) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $content['START_YEAR'] .= '<option value="' . $show_start_year . '" ' . $selected . '>' . $show_start_year . '</option>';
        }
    }
    $content['END_YEAR'] = '';
    $earliest_year = $model->getYear('plan_date', 'plan_registration ', 'plan_date', $db);
    $current_year = date('Y');
    $this_year = (!empty($plan_start_year)) ? $plan_start_year : date('Y');
    $year_count = $current_year - $earliest_year;

    if ($year_count == 0) {
        $content['END_YEAR'] .= '<option value="' . $this_year . '" selected>' . $this_year . '</option>';
    } else {
        $this_year = date('Y');

        for ($year = $current_year; $year >= $earliest_year; --$year) {
            $show_end_year = $year;
            if ($show_end_year == $this_year) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $content['END_YEAR'] .= '<option value="' . $show_end_year . '" ' . $selected . '>' . $show_end_year . '</option>';
        }
    }
    $content['plan_result'] = '';
    if (!empty($get_plans)) {
        $plans = $model->downloadPlans($plan_start_date, $plan_end_date, $db);

        if (empty($plans)) {
            $content['plan_result'] = "There were no plans created on this date";

        } else {

            $content['plan_result'] = '<a id="download_plans" href="download?download_file=' . $plans . '">Click to download plans</a>';


        }
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
