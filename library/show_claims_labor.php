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
    $current_date = (empty($_GET['cdate'])) ? '' : $_GET['cdate'];

    if (!empty($current_date)) {
        $get_date_parts = explode('-', $current_date);


    }

    $claim_month = (empty($_POST['month'])) ? ((empty($get_date_parts[1])) ? '' : $get_date_parts[1]) : $_POST['month'];
    $claim_day = (empty($_POST['day'])) ? ((empty($get_date_parts[2])) ? '' : $get_date_parts[2]) : $_POST['day'];
    $claim_year = (empty($_POST['year'])) ? ((empty($get_date_parts[0])) ? '' : $get_date_parts[0]) : $_POST['year'];
    $claim_date = (empty($claim_month)) ? date('Y-m-d') : $claim_year . '-' . $claim_month . '-' . $claim_day;

    /* Populate the date dropdowns */
    $content['CURRENT_MONTH'] = '';

    $this_month = (!empty($claim_month)) ? $claim_month : date('m');
    for ($month = 1; $month <= 12; ++$month) {
        if ($month < 10) {
            $show_month = '0' . $month;
        } else {
            $show_month = $month;
        }

        if ($show_month == $this_month) {
            $selected = 'selected';

        } else {
            $selected = '';

        }

        $content['CURRENT_MONTH'] .= '<option value="' . $show_month . '" ' . $selected . '>' . $show_month . '</option>';
    }
    $content['CURRENT_DAY'] = '';
    $this_day = (!empty($claim_day)) ? $claim_day : date('d');
    for ($day = 1; $day <= 31; ++$day) {
        if ($day < 10) {
            $show_day = '0' . $day;
        } else {
            $show_day = $day;
        }

        if ($show_day == $this_day) {
            $selected = 'selected';

        } else {
            $selected = '';

        }

        $content['CURRENT_DAY'] .= '<option value="' . $show_day . '" ' . $selected . '>' . $show_day . '</option>';
    }
    $content['CURRENT_YEAR'] = '';

    $earliest_year = $model->getYear('claim_date', 'dealer_claims', 'claim_id', $db);
    $this_year = (!empty($claim_year)) ? $claim_year : date('Y');
    $current_year = date('Y');

    $year_count = $current_year - $earliest_year;
    $year_count = $this_year - $earliest_year;
    if ($year_count == 0) {
        $content['CURRENT_YEAR'] .= '<option value="' . $this_year . '" selected>' . $this_year . '</option>';
    } else {
        $this_day = date('d');

        for ($year = $this_year; $year > $earliest_year; --$year) {

            $show_year = $year;


            if ($show_year == $this_year) {
                $selected = 'selected';

            } else {
                $selected = '';

            }

            $content['CURRENT_YEAR'] .= '<option value="' . $show_year . '" ' . $selected . '>' . $show_year . '</option>';
        }
    }
    $start = (empty($_GET['start'])) ? '' : $_GET['start'];
    $order = (empty($_GET['order'])) ? 'default' : $_GET['order'];
    $direction = (empty($_GET['direction'])) ? 'DESC' : $_GET['direction'];
    $content['CLAIM_HEADER'] = '';

    $default_claim_header = '<div class="claim_id"><a href="show_claims?order=claim_id&amp;direction=DESC&amp;cdate=' . $claim_date . '"><b>Claim Id</b></a></div>';
    $default_claim_date = '<div class="claim_date"><a href="show_claims?order=claim_date&amp;direction=DESC&amp;cdate=' . $claim_date . '"><b>Claim Date</b></a></div>';
    $default_claim_dealer = '<div class="dealer_id"><a href="show_claims?order=dealer_id&amp;direction=DESC&amp;cdate=' . $claim_date . '"><b>Dealer Id</b></a></div>';
    $default_claim_amount = '';

    if ($direction == 'DESC') {
        $new_direction = 'ASC';
        $arrow = 'down';
    } else {
        $new_direction = 'DESC';
        $arrow = 'up';


    }
    switch ($order) {
        case 'claim_id':
            $content['CLAIM_HEADER'] .= '<div class="claim_id"><a href="show_claims?order=claim_id&amp;direction=' . $new_direction . '&amp;cdate=' . $claim_date . '"><b>Claim Id</b></a><img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['CLAIM_HEADER'] .= $default_claim_date . $default_claim_dealer;

            break;
        case 'claim_date':
            $content['CLAIM_HEADER'] .= $default_claim_header;
            $content['CLAIM_HEADER'] .= '<div class="claim_date"><a href="show_claims?order=claim_date&amp;direction=' . $new_direction . '&amp;cdate=' . $claim_date . '"><b>Claim Date</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['CLAIM_HEADER'] .= $default_claim_dealer . $default_claim_amount;
            break;
        case 'dealer_id':
            $content['CLAIM_HEADER'] .= $default_claim_header . $default_claim_date;
            $content['CLAIM_HEADER'] .= '<div class="dealer_id"><a href="show_claims?order=dealer_id&amp;direction=' . $new_direction . '&amp;cdate=' . $claim_date . '"><b>Dealer Id</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            $content['CLAIM_HEADER'] .= $default_claim_amount;
            break;
        case 'claim_total':
            $content['CLAIM_HEADER'] .= $default_claim_header . $default_claim_date . $default_claim_dealer;
            $content['CLAIM_HEADER'] .= '<div class="claim_total"><a href="show_claims?order=claim_total&amp;direction=' . $new_direction . '&amp;cdate=' . $claim_date . '"><b>Claim Total</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
            break;
        default:
            $content['CLAIM_HEADER'] .= $default_claim_header . $default_claim_date . $default_claim_dealer . $default_claim_amount;
            break;
    }
    if ($order == 'default') {
        $order = 'claim_date';
    }
    if (!empty($content['CLAIM_HEADER'])) {
        $content['CLAIM_HEADER'] .= '<div class="clearBoth"></div>';

    }


    $claims = $model->searchClaims($claim_date, $order, $direction, $start, $db);
    if (empty($claims)) {
        $content['CLAIM_INFO'] = '<p style="font-style: italic">There are no claims for this date</p>';

    } else {
        $content['CLAIM_INFO'] = $claims;

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
