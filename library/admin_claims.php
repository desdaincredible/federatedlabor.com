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

    $claim_id = intval($_REQUEST['cid']);
    $claim_content = $model->getClaimContent($claim_id, $db);

    if (empty($claim_content)) {
        header("Location: error?eid=4");
    }

//    $claim_content = array_change_key_case($claim_content, CASE_UPPER);
//    $content = array_merge($claim_content, $content);
    $content['PAGE_TITLE'] = 'Tire Claim';

	$content['CLAIM_ID'] = (empty($claim_content[0]['claim_id'])) ? '' : $claim_content[0]['claim_id'];
    $content['DEALER_NAME'] = (empty($claim_content[0]['business_name'])) ? '' : $claim_content[0]['business_name'];
    $content['DEALER_PHONE'] = (empty($claim_content[0]['business_phone'])) ? '' : $claim_content[0]['business_phone'];

    $content['CLAIM_FILER'] = (empty($claim_content[0]['claim_filer'])) ? '' : $claim_content[0]['claim_filer'];
    $content['INVOICE_NUMBER'] = (empty($claim_content[0]['invoice_id'])) ? '' : $claim_content[0]['invoice_id'];
    $content['INVOICE_DATE'] = (empty($claim_content[0]['invoice_date'])) ? '' : date('Y-m-d', strtotime($claim_content[0]['invoice_date']));
    $content['ORIG_MILEAGE'] = (empty($claim_content[0]['original_vehicle_mileage'])) ? '' : $claim_content[0]['original_vehicle_mileage'];
    $content['CURRENT_MILEAGE'] = (empty($claim_content[0]['claim_vehicle_mileage'])) ? '' : $claim_content[0]['claim_vehicle_mileage'];
    $content['VEHICLE_YEAR'] = (empty($claim_content[0]['vehicle_year'])) ? '' : $claim_content[0]['vehicle_year'];
    $content['VEHICLE_MAKE'] = (empty($claim_content[0]['vehicle_make'])) ? '' : $claim_content[0]['vehicle_make'];
    $content['VEHICLE_MODEL'] = (empty($claim_content[0]['vehicle_model'])) ? '' : $claim_content[0]['vehicle_model'];

    $content['ORIGINAL_INVOICE'] = (empty($claim_content[0]['orig_inv_filename'])) ? '' : 'http://ntwclaims.net/' . $claim_content[0]['orig_inv_filename'];
    $content['NEW_INVOICE'] = (empty($claim_content[0]['claim_inv_filename'])) ? '' : 'http://ntwclaims.net/' . $claim_content[0]['claim_inv_filename'];


    $content['TIRE_1'] = 'block';
    $content['TIRE_MAKE_1'] = (empty($claim_content[0]['make'])) ? '' : ucwords(strtolower($claim_content[0]['make']));
    $content['TIRE_ID_1'] = (empty($content['TIRE_MAKE_1'])) ? '' : $content['TIRE_MAKE_1'];
    $content['TIRE_MODEL_1'] = (empty($claim_content[0]['model'])) ? '' : ucwords(strtolower($claim_content[0]['model']));
    $content['TIRE_SIZE_1'] = (empty($claim_content[0]['size'])) ? '' : $claim_content[0]['size'];
    $content['TIRE_DOT_1'] = (empty($claim_content[0]['DOT'])) ? '' : $claim_content[0]['DOT'];
    $content['ORIGINAL_PART_NUMBER_1'] = (empty($claim_content[0]['original_part_number'])) ? '' : $claim_content[0]['original_part_number'];
    $content['CLAIM_PART_NUMBER_1'] = (empty($claim_content[0]['claim_part_number'])) ? '' : $claim_content[0]['claim_part_number'];
    $content['ORIGINAL_TIRE_PRICE_1'] = (empty($claim_content[0]['original_tire_price'])) ? '' : $claim_content[0]['original_tire_price'];
    $content['CLAIM_TIRE_PRICE_1'] = (empty($claim_content[0]['claim_tire_price'])) ? '' : $claim_content[0]['claim_tire_price'];
    $content['ORIGINAL_TREAD_DEPTH_1'] = (empty($claim_content[0]['original_tread_depth'])) ? '' : $claim_content[0]['original_tread_depth'];
    $content['REMAINING_TREAD_DEPTH_1'] = (empty($claim_content[0]['remaining_tread_depth'])) ? '' : $claim_content[0]['remaining_tread_depth'];
    $content['TIRE_DAMAGE_DESC_1'] = (empty($claim_content[0]['damage_desc'])) ? '' : $claim_content[0]['damage_desc'];


    $content['TIRE_MAKE_2'] = (empty($claim_content[1]['make'])) ? '' : ucwords(strtolower($claim_content[1]['make']));
    $content['TIRE_2'] = (empty($content['TIRE_MAKE_2'])) ? 'none' : 'block';
    $content['TIRE_ID_2'] = (empty($content['TIRE_MAKE_2'])) ? '' : $content['TIRE_MAKE_2'];
    $content['TIRE_MODEL_2'] = (empty($claim_content[1]['model'])) ? '' : ucwords(strtolower($claim_content[1]['model']));
    $content['TIRE_SIZE_2'] = (empty($claim_content[1]['size'])) ? '' : $claim_content[1]['size'];
    $content['TIRE_DOT_2'] = (empty($claim_content[1]['DOT'])) ? '' : $claim_content[1]['DOT'];
    $content['ORIGINAL_PART_NUMBER_2'] = (empty($claim_content[1]['original_part_number'])) ? '' : $claim_content[1]['original_part_number'];
    $content['CLAIM_PART_NUMBER_2'] = (empty($claim_content[1]['claim_part_number'])) ? '' : $claim_content[1]['claim_part_number'];
    $content['ORIGINAL_TIRE_PRICE_2'] = (empty($claim_content[1]['original_tire_price'])) ? '' : $claim_content[1]['original_tire_price'];
    $content['CLAIM_TIRE_PRICE_2'] = (empty($claim_content[1]['claim_tire_price'])) ? '' : $claim_content[1]['claim_tire_price'];
    $content['ORIGINAL_TREAD_DEPTH_2'] = (empty($claim_content[1]['original_tread_depth'])) ? '' : $claim_content[1]['original_tread_depth'];
    $content['REMAINING_TREAD_DEPTH_2'] = (empty($claim_content[1]['remaining_tread_depth'])) ? '' : $claim_content[1]['remaining_tread_depth'];
    $content['TIRE_DAMAGE_DESC_2'] = (empty($claim_content[1]['damage_desc'])) ? '' : $claim_content[1]['damage_desc'];

    $body_copy = file_get_contents('templates/' . $page_id . '.html');

} else {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_login.html');
    $header = file_get_contents('templates/header.html');
    $footer = file_get_contents('templates/footer.html');
    $body_copy = file_get_contents('templates/login.html');
}
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;

foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

