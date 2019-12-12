<?php
$message = '';
session_start();
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
    if (!empty($_POST['delete_plan'])) {
        $delete_plan = $model->deletePlan($_POST['plan_number'], $db);
        if ($delete_plan == "success") {
            header("Location: success?mid=5");
            break;


        } else {
            header("Location: error?eid=$eid");
            break;


        }
    }
    if (!empty($_POST['plan_update'])) {
        $plan_info['customer_first_name'] = (empty($_POST['customer_first_name'])) ? '' : addslashes(ucwords(strtolower($_POST['customer_first_name'])));
        $plan_info['customer_last_name'] = (empty($_POST['customer_last_name'])) ? '' : addslashes(ucwords(strtolower($_POST['customer_last_name'])));
        $plan_info['customer_phone'] = (empty($_POST['customer_area'])) ? '' : $_POST['customer_area'];
        $plan_info['customer_phone'] .= (empty($_POST['customer_exchange'])) ? '' : $_POST['customer_exchange'];
        $plan_info['customer_phone'] .= (empty($_POST['customer_digits'])) ? '' : $_POST['customer_digits'];
        $plan_info['invoice_number'] = (empty($_POST['invoice_number'])) ? '' : $_POST['invoice_number'];
        $plan_info['vehicle_year'] = (empty($_POST['vehicle_year'])) ? '' : $_POST['vehicle_year'];
        $plan_info['vehicle_make'] = (empty($_POST['vehicle_make'])) ? '' : $_POST['vehicle_make'];
        $plan_info['vehicle_model'] = (empty($_POST['vehicle_model'])) ? '' : $_POST['vehicle_model'];
        $plan_info['vehicle_mileage'] = (empty($_POST['vehicle_mileage'])) ? 0 : $_POST['vehicle_mileage'];

        $plan_info['tires'][1]['make'] = (empty($_POST['tire_make_1'])) ? '' : ucwords(strtolower($_POST['tire_make_1']));
        $plan_info['tires'][1]['model'] = (empty($_POST['tire_model_1'])) ? '' : ucwords(strtolower($_POST['tire_model_1']));
        $plan_info['tires'][1]['size'] = (empty($_POST['tire_size_1'])) ? '' : $_POST['tire_size_1'];
        $plan_info['tires'][1]['DOT'] = (empty($_POST['tire_dot_1'])) ? '' : $_POST['tire_dot_1'];
        $plan_info['tires'][1]['price'] = (empty($_POST['tire_price_1'])) ? '' : $_POST['tire_price_1'];
        $plan_info['tires'][1]['replaced'] = (empty($_POST['tire_replaced_1'])) ? '' : $_POST['tire_replaced_1'];

        $plan_info['tires'][2]['make'] = (empty($_POST['tire_make_2'])) ? '' : ucwords(strtolower($_POST['tire_make_2']));
        $plan_info['tires'][2]['model'] = (empty($_POST['tire_model_2'])) ? '' : ucwords(strtolower($_POST['tire_model_2']));
        $plan_info['tires'][2]['size'] = (empty($_POST['tire_size_2'])) ? '' : $_POST['tire_size_2'];
        $plan_info['tires'][2]['DOT'] = (empty($_POST['tire_dot_2'])) ? '' : $_POST['tire_dot_2'];
        $plan_info['tires'][2]['price'] = (empty($_POST['tire_price_2'])) ? '' : $_POST['tire_price_2'];
        $plan_info['tires'][2]['replaced'] = (empty($_POST['tire_replaced_2'])) ? '' : $_POST['tire_replaced_2'];


        $plan_info['tires'][3]['make'] = (empty($_POST['tire_make_3'])) ? '' : ucwords(strtolower($_POST['tire_make_3']));
        $plan_info['tires'][3]['model'] = (empty($_POST['tire_model_3'])) ? '' : ucwords(strtolower($_POST['tire_model_3']));
        $plan_info['tires'][3]['size'] = (empty($_POST['tire_size_3'])) ? '' : $_POST['tire_size_3'];
        $plan_info['tires'][3]['DOT'] = (empty($_POST['tire_dot_3'])) ? '' : $_POST['tire_dot_3'];
        $plan_info['tires'][3]['price'] = (empty($_POST['tire_price_3'])) ? '' : $_POST['tire_price_3'];
        $plan_info['tires'][3]['replaced'] = (empty($_POST['tire_replaced_3'])) ? '' : $_POST['tire_replaced_3'];

        $plan_info['tires'][4]['make'] = (empty($_POST['tire_make_4'])) ? '' : ucwords(strtolower($_POST['tire_make_4']));
        $plan_info['tires'][4]['model'] = (empty($_POST['tire_model_4'])) ? '' : ucwords(strtolower($_POST['tire_model_4']));
        $plan_info['tires'][4]['size'] = (empty($_POST['tire_size_4'])) ? '' : $_POST['tire_size_4'];
        $plan_info['tires'][4]['DOT'] = (empty($_POST['tire_dot_4'])) ? '' : $_POST['tire_dot_4'];
        $plan_info['tires'][4]['price'] = (empty($_POST['tire_price_4'])) ? '' : $_POST['tire_price_4'];
        $plan_info['tires'][4]['replaced'] = (empty($_POST['tire_replaced_4'])) ? '' : $_POST['tire_replaced_4'];

        $plan_info['tires'][5]['make'] = (empty($_POST['tire_make_5'])) ? '' : ucwords(strtolower($_POST['tire_make_5']));
        $plan_info['tires'][5]['model'] = (empty($_POST['tire_model_5'])) ? '' : ucwords(strtolower($_POST['tire_model_5']));
        $plan_info['tires'][5]['size'] = (empty($_POST['tire_size_5'])) ? '' : $_POST['tire_size_5'];
        $plan_info['tires'][5]['DOT'] = (empty($_POST['tire_dot_5'])) ? '' : $_POST['tire_dot_5'];
        $plan_info['tires'][5]['price'] = (empty($_POST['tire_price_5'])) ? '' : $_POST['tire_price_5'];
        $plan_info['tires'][5]['replaced'] = (empty($_POST['tire_replaced_5'])) ? '' : $_POST['tire_replaced_5'];


        $plan_info['tires'][6]['make'] = (empty($_POST['tire_make_6'])) ? '' : ucwords(strtolower($_POST['tire_make_6']));
        $plan_info['tires'][6]['model'] = (empty($_POST['tire_model_6'])) ? '' : ucwords(strtolower($_POST['tire_model_6']));
        $plan_info['tires'][6]['size'] = (empty($_POST['tire_size_6'])) ? '' : $_POST['tire_size_6'];
        $plan_info['tires'][6]['DOT'] = (empty($_POST['tire_dot_6'])) ? '' : $_POST['tire_dot_6'];
        $plan_info['tires'][6]['price'] = (empty($_POST['tire_price_6'])) ? '' : $_POST['tire_price_6'];
        $plan_info['tires'][6]['replaced'] = (empty($_POST['tire_replaced_6'])) ? '' : $_POST['tire_replaced_6'];

        $update_plan = $model->planUpdate($plan_info, $_POST['plan_id'], $db);
        if ($update_plan == "success") {
            header("Location: success?mid=6");
            break;


        } else {
            header("Location: error?eid=12");
            break;


        }


    }
    $body_copy = file_get_contents('templates/' . $page_id . '.html');
    $plan_id = (empty($_GET['plan_number'])) ? '' : $_GET['plan_number'];
    $plan_content = $model->getPlanContent($plan_id, $db);
    $tires = $plan_content['tire'];
    unset($plan_content['tire']);
    $plan_content['customer_area'] = substr($plan_content['customer_phone'], 0, 3);
    $plan_content['customer_exchange'] = substr($plan_content['customer_phone'], 3, 3);
    $plan_content['customer_digits'] = substr($plan_content['customer_phone'], 6);
    $plan_content['plan_id'] = $plan_id;


    $plan_content = array_change_key_case($plan_content, CASE_UPPER);
    $content = array_merge($plan_content, $content);
    $tire_count = count($tires);
    /* Load the defaults */
    for ($i = 1; $i <= 6; ++$i) {
        $content['TIRE_' . $i] = 'none';
        $content['TIRE_REPLACE_NO_' . $i] = ' selected';
        $content['TIRE_REPLACE_YES_' . $i] = '';
        $content['TIRE_MAKE_' . $i] = '';
        $content['TIRE_MODEL_' . $i] = '';
        $content['TIRE_DOT_' . $i] = '';
        $content['TIRE_PRICE_' . $i] = '';
        $content['TIRE_SIZE_' . $i] = '';
    }
    for ($i = 0; $i < $tire_count; ++$i) {
        $current_tire = $i + 1;
        $content['TIRE_MAKE_' . $current_tire] = $tires[$i]['make'];
        $content['TIRE_MODEL_' . $current_tire] = $tires[$i]['model'];
        $content['TIRE_SIZE_' . $current_tire] = $tires[$i]['size'];
        $content['TIRE_DOT_' . $current_tire] = $tires[$i]['DOT'];
        $content['TIRE_PRICE_' . $current_tire] = $tires[$i]['price'];
        if ($tires[$i]['replaced'] == 1) {
            $content['TIRE_REPLACE_YES_' . $current_tire] = ' selected';
            $content['TIRE_REPLACE_NO_' . $current_tire] = '';
        }
    }


    if (!empty($content['TIRE_MAKE_1'])) {
        $content['TIRE_1'] = 'block';
        $content['SHOW_ADD'] = 'inline';
        $content['SHOW_DIVIDER'] = 'none';
        $content['SHOW_REMOVE'] = 'none';
        $content['CURRENT_TIRE'] = 1;
    }
    if (!empty($content['TIRE_MAKE_2'])) {
        $content['TIRE_2'] = 'block';
        $content['SHOW_ADD'] = 'inline';
        $content['SHOW_DIVIDER'] = 'inline';
        $content['SHOW_REMOVE'] = 'inline';
        $content['CURRENT_TIRE'] = 2;
    }
    if (!empty($content['TIRE_MAKE_3'])) {
        $content['TIRE_3'] = 'block';
        $content['SHOW_ADD'] = 'inline';
        $content['SHOW_DIVIDER'] = 'inline';
        $content['SHOW_REMOVE'] = 'inline';
        $content['CURRENT_TIRE'] = 3;
    }

    if (!empty($content['TIRE_MAKE_4'])) {
        $content['TIRE_4'] = 'block';
        $content['SHOW_ADD'] = 'inline';
        $content['SHOW_DIVIDER'] = 'inline';
        $content['SHOW_REMOVE'] = 'inline';
        $content['CURRENT_TIRE'] = 4;
    }
    if (!empty($content['TIRE_MAKE_5'])) {
        $content['TIRE_5'] = 'block';
        $content['SHOW_ADD'] = 'inline';
        $content['SHOW_DIVIDER'] = 'inline';
        $content['SHOW_REMOVE'] = 'inline';
        $content['CURRENT_TIRE'] = 5;

    }
    if (!empty($content['TIRE_MAKE_6'])) {
        $content['TIRE_6'] = 'block';
        $content['SHOW_ADD'] = 'none';
        $content['SHOW_DIVIDER'] = 'none';
        $content['SHOW_REMOVE'] = 'inline';
        $content['CURRENT_TIRE'] = 6;

    }

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

