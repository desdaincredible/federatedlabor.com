<?php
session_start();
$message = '';
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/site_model.php');
$model = new model($db);

$valid_claim = '';

/* Check to see if they are already logged in */

if ($_SESSION['valid_claim'] == 1) {
    $valid_claim = 1;
}

$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');


if (!empty($valid_claim)) {

    $body_copy = file_get_contents('templates/' . $page_id . '.html');
    $content['CLAIM_FILER'] = (empty($_POST['claim_filer'])) ? '' : $_POST['claim_filer'];
    $content['INVOICE_NUMBER'] = (empty($_POST['invoice_number'])) ? '' : $_POST['invoice_number'];
    $content['INVOICE_DATE'] = (empty($_POST['invoice_date'])) ? '' : $_POST['invoice_date'];
    $content['ORIG_MILEAGE'] = (empty($_POST['orig_mileage'])) ? '' : $_POST['orig_mileage'];
    $content['CURRENT_MILEAGE'] = (empty($_POST['current_mileage'])) ? '' : $_POST['current_mileage'];
    $content['VEHICLE_YEAR'] = (empty($_POST['vehicle_year'])) ? '' : $_POST['vehicle_year'];
    $content['VEHICLE_MAKE'] = (empty($_POST['vehicle_make'])) ? '' : $_POST['vehicle_make'];
    $content['VEHICLE_MAKE_NAME'] = $model->getMakeName($db, $content['VEHICLE_MAKE']);
    $content['VEHICLE_MAKE_DROPDOWN'] = $model->getMakesDropdown($db, $content['VEHICLE_MAKE']);
    $content['VEHICLE_MODEL'] = (empty($_POST['vehicle_model'])) ? '' : $_POST['vehicle_model'];

    $content['TIRE_1'] = 'none';
    $content['TIRE_MAKE_1'] = (empty($_POST['tire_make_1'])) ? '' : ucwords(strtolower($_POST['tire_make_1']));
    $content['TIRE_ID_1'] = (empty($content['TIRE_MAKE_1'])) ? '' : $model->getTireMakeId($db, $content['TIRE_MAKE_1']);
    $content['TIRE_MODEL_1'] = (empty($_POST['tire_model_1'])) ? '' : ucwords(strtolower($_POST['tire_model_1']));
    $content['TIRE_SIZE_1'] = (empty($_POST['tire_size_1'])) ? '' : $_POST['tire_size_1'];
    $content['TIRE_DOT_1'] = (empty($_POST['tire_dot_1'])) ? '' : $_POST['tire_dot_1'];
    $content['ORIGINAL_PART_NUMBER_1'] = (empty($_POST['original_part_number_1'])) ? '' : $_POST['original_part_number_1'];
    $content['CLAIM_PART_NUMBER_1'] = (empty($_POST['claim_part_number_1'])) ? '' : $_POST['claim_part_number_1'];
    $content['ORIGINAL_TIRE_PRICE_1'] = (empty($_POST['original_tire_price_1'])) ? '' : $_POST['original_tire_price_1'];
    $content['CLAIM_TIRE_PRICE_1'] = (empty($_POST['claim_tire_price_1'])) ? '' : $_POST['claim_tire_price_1'];
    $content['ORIGINAL_TREAD_DEPTH_1'] = (empty($_POST['original_tread_depth_1'])) ? '' : $_POST['original_tread_depth_1'];
    $content['REMAINING_TREAD_DEPTH_1'] = (empty($_POST['remaining_tread_depth_1'])) ? '' : $_POST['remaining_tread_depth_1'];
    $content['TIRE_DAMAGE_DESC_1'] = (empty($_POST['tire_damage_desc_1'])) ? '' : $_POST['tire_damage_desc_1'];
    $content['TIRE_COVERAGE_1'] = (empty($_POST['coverage_1'])) ? '' : $_POST['coverage_1'];
    $content['TIRE_PERCENTAGE_1'] = (empty($_POST['percent_1'])) ? '' : $_POST['percent_1'];

    $content['TIRE_2'] = 'none';
    $content['SECOND_TIRE'] = '';
    $content['TIRE_MAKE_2'] = (empty($_POST['tire_make_2'])) ? '' : ucwords(strtolower($_POST['tire_make_2']));
    $content['TIRE_ID_2'] = (empty($content['TIRE_MAKE_2'])) ? '' : $model->getTireMakeId($db, $content['TIRE_MAKE_2']);
    $content['TIRE_MODEL_2'] = (empty($_POST['tire_model_2'])) ? '' : ucwords(strtolower($_POST['tire_model_2']));
    $content['TIRE_SIZE_2'] = (empty($_POST['tire_size_2'])) ? '' : $_POST['tire_size_2'];
    $content['TIRE_DOT_2'] = (empty($_POST['tire_dot_2'])) ? '' : $_POST['tire_dot_2'];
    $content['ORIGINAL_PART_NUMBER_2'] = (empty($_POST['original_part_number_2'])) ? '' : $_POST['original_part_number_2'];
    $content['CLAIM_PART_NUMBER_2'] = (empty($_POST['claim_part_number_2'])) ? '' : $_POST['claim_part_number_2'];
    $content['ORIGINAL_TIRE_PRICE_2'] = (empty($_POST['original_tire_price_1'])) ? '' : $_POST['original_tire_price_1'];
    $content['CLAIM_TIRE_PRICE_2'] = (empty($_POST['claim_tire_price_1'])) ? '' : $_POST['claim_tire_price_1'];
    $content['ORIGINAL_TREAD_DEPTH_2'] = (empty($_POST['original_tread_depth_2'])) ? '' : $_POST['original_tread_depth_2'];
    $content['REMAINING_TREAD_DEPTH_2'] = (empty($_POST['remaining_tread_depth_2'])) ? '' : $_POST['remaining_tread_depth_2'];
    $content['TIRE_DAMAGE_DESC_2'] = (empty($_POST['tire_damage_desc_2'])) ? '' : $_POST['tire_damage_desc_2'];
    $content['TIRE_COVERAGE_2'] = (empty($_POST['coverage_2'])) ? '' : $_POST['coverage_2'];
    $content['TIRE_PERCENTAGE_2'] = (empty($_POST['percent_2'])) ? '' : $_POST['percent_2'];

    if (!empty($content['TIRE_MAKE_1'])) {
        $content['TIRE_1'] = 'block';
    }

    if (!empty($content['TIRE_MAKE_2'])) {
        $content['TIRE_2'] = 'block';
    }
    $content['VEHICLE_MAKES'] = $model->getMakesDropdown($db);

    if (!empty($_POST['next_page'])) {
        $claim_id = $model->insertClaim($content, $site, $_SESSION['demo'], $db);
        $_SESSION['claim_id'] = $claim_id;
        header("Location: upload_invoice");
    }


} else {
    $body_copy = file_get_contents('templates/login.html');
    $content['FORM_ACTION'] = 'new_claim';
    $content['PAGE_TITLE'] = '<p class="content_header">File A Claim</p>';
}

$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);

}

echo $finished_page;
