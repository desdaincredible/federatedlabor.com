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
    $claim_content = $model->getClaimLaborContent($claim_id, $db);

    if (empty($claim_content)) {
        header("Location: error?eid=4");
    }

//    $claim_content = array_change_key_case($claim_content, CASE_UPPER);
//    $content = array_merge($claim_content, $content);
    $content['PAGE_TITLE'] = 'Labor Claim';

	$content['CLAIM_ID'] = (empty($claim_content[0]['claim_id'])) ? '' : $claim_content[0]['claim_id'];
    $content['DEALER_NAME'] = (empty($claim_content[0]['business_name'])) ? '' : $claim_content[0]['business_name'];
    $content['DEALER_PHONE'] = (empty($claim_content[0]['business_phone'])) ? '' : $claim_content[0]['business_phone'];

    $content['ORIGINAL_INVOICE_NUMBER'] = (empty($claim_content[0]['invoice_number'])) ? '' : $claim_content[0]['invoice_number'];
    $content['ORIGINAL_REPAIR_DATE'] = (empty($claim_content[0]['original_repair_date'])) ? '' : $claim_content[0]['original_repair_date'];
    $content['SUB_INVOICE_NUMBER'] = (empty($claim_content[0]['sub_invoice_number'])) ? '' : $claim_content[0]['sub_invoice_number'];
    $content['SUB_REPAIR_DATE'] = (empty($claim_content[0]['sub_repair_date'])) ? '' : $claim_content[0]['sub_repair_date'];
    $content['ORIGINAL_REPAIR_MILEAGE'] = (empty($claim_content[0]['original_repair_mileage'])) ? '' : $claim_content[0]['original_repair_mileage'];
    $content['CURRENT_MILEAGE'] = (empty($claim_content[0]['current_mileage'])) ? '' : $claim_content[0]['current_mileage'];

    $content['CUSTOMER_FIRST_NAME'] = (empty($claim_content[0]['customer_first_name'])) ? '' : $claim_content[0]['customer_first_name'];
    $content['CUSTOMER_LAST_NAME'] = (empty($claim_content[0]['customer_last_name'])) ? '' : $claim_content[0]['customer_last_name'];
    $content['CUSTOMER_PHONE'] = (empty($claim_content[0]['customer_phone'])) ? '' : $claim_content[0]['customer_phone'];
    $content['CUSTOMER_EMAIL'] = (empty($claim_content[0]['customer_email'])) ? '' : $claim_content[0]['customer_email'];

    $content['VEHICLE_YEAR'] = (empty($claim_content[0]['vehicle_year'])) ? '' : $claim_content[0]['vehicle_year'];
    $content['VEHICLE_MAKE'] = (empty($claim_content[0]['vehicle_make'])) ? '' : $claim_content[0]['vehicle_make'];
    $content['VEHICLE_MODEL'] = (empty($claim_content[0]['vehicle_model'])) ? '' : $claim_content[0]['vehicle_model'];
//
    $content['ORIGINAL_INVOICE'] = (empty($claim_content[0]['orig_inv_filename'])) ? '' : '/' . $claim_content[0]['orig_inv_filename'];
    $content['NEW_INVOICE'] = (empty($claim_content[0]['claim_inv_filename'])) ? '' : '/' . $claim_content[0]['claim_inv_filename'];

    $content['REPAIR_CODE'] = (empty($claim_content[0]['repair_code'])) ? '' :  $claim_content[0]['repair_code'];
    $content['ORIGINAL_LABOR_PRICE'] = (empty($claim_content[0]['original_labor_price'])) ? '' :  $claim_content[0]['original_labor_price'];
    $content['LABOR_PRICE'] = (empty($claim_content[0]['labor_price'])) ? '' :  $claim_content[0]['labor_price'];
    $content['LABOR_HOUR'] = (empty($claim_content[0]['labor_hour'])) ? '' :  $claim_content[0]['labor_hour'];
    $content['SUB_LABOR_PRICE'] = (empty($claim_content[0]['sub_labor_price'])) ? '' :  $claim_content[0]['sub_labor_price'];
    $content['REPAIR_DESCRIPTION'] = (empty($claim_content[0]['repair_description'])) ? '' :  $claim_content[0]['repair_description'];




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
//print_r($content);
foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

