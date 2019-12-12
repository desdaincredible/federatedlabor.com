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
    $content['PAGE_TITLE'] = 'File a New Claim';
    $content['CLAIM_FILER'] = (empty($_POST['claim_filer'])) ? '' : $_POST['claim_filer'];
    $content['INVOICE_NUMBER'] = (empty($_POST['invoice_number'])) ? '' : $_POST['invoice_number'];
    $content['INVOICE_DATE'] = (empty($_POST['invoice_date'])) ? '' : $_POST['invoice_date'];
    $content['ORIG_MILEAGE'] = (empty($_POST['orig_mileage'])) ? '' : $_POST['orig_mileage'];
    $content['CURRENT_MILEAGE'] = (empty($_POST['current_mileage'])) ? '' : $_POST['current_mileage'];
    $content['VEHICLE_YEAR'] = (empty($_POST['vehicle_year'])) ? '' : $_POST['vehicle_year'];
    $content['VEHICLE_MAKE'] = (empty($_POST['vehicle_make'])) ? '' : $_POST['vehicle_make'];
    $content['VEHICLE_MODEL'] = (empty($_POST['vehicle_model'])) ? '' : $_POST['vehicle_model'];

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
