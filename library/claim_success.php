<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


session_start();
$message = '';


/* Exception class. */
require 'src/Exception.php';

/* The main PHPMailer class. */
require 'src/PHPMailer.php';

/* SMTP class, needed if you want to use SMTP. */
//require 'src/SMTP.php';

$email = new PHPMailer(TRUE);


require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/site_model.php');
$model = new model($db);

$valid_claim = '';

/* Check to see if they are already logged in */

if (isset($_SESSION['valid_claim']) && $_SESSION['valid_claim'] == 1) {
    $valid_claim = 1;
}

$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');


if (!empty($valid_claim)) {
    $body_copy = file_get_contents('templates/' . $page_id . '.html');

    $claim_content = $model->getClaimContent($db, $_SESSION['claim_id'], $_SESSION['demo']);
    $content['PAGE_TITLE'] = 'Claim Success!';

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
    $content['VEHICLE_MAKE_NAME'] = $model->getMakeName($db, $content['VEHICLE_MAKE']);

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
    $content['COVERAGE_1'] = (empty($claim_content[0]['coverage'])) ? '' : $claim_content[0]['coverage'];
    $content['PERCENTAGE_1'] = (empty($claim_content[0]['coverage_percentage'])) ? '' : $claim_content[0]['coverage_percentage'];


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
    $content['COVERAGE_2'] = (empty($claim_content[1]['coverage'])) ? '' : $claim_content[1]['coverage'];
    $content['PERCENTAGE_2'] = (empty($claim_content[1]['coverage_percentage'])) ? '' : $claim_content[1]['coverage_percentage'];


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

if (!empty($valid_claim)) {

    /* Prepare mailer */

    $text_part = file_get_contents('templates/email_text.txt');
    if (!empty($content['TIRE_MAKE_2'])) {
        $text_part = $text_part . file_get_contents('templates/email_text_tire_2.txt');
    }

    reset($content);
    foreach ($content as $key => $value) {
        $text_part = str_replace('{' . strtoupper($key) . '}', $value, $text_part);

    }

    $html_part = file_get_contents('templates/email_html.html');
    if (!empty($content['TIRE_MAKE_2'])) {
        $html_part = $html_part . file_get_contents('templates/email_html_tire_2.html');

    }

    $html_part = $html_part . '</table>';

    reset($content);
    foreach ($content as $key => $value) {
        $html_part = str_replace('{' . strtoupper($key) . '}', $value, $html_part);
    }

    $email->setFrom('donotreply@ntwclaims.net', 'NTW Website');
    $email->addAddress('claims@ntwclaims.net', 'NTW Claims');
    $email->addCC('zola@zolaweb.com', 'Zola');
    $email->addCC('dmcneese@abswarranty.net', 'Daniel McNeese');
    $email->addCC('gpetty@abswarranty.net', 'Gennica Petty');


    $email->Subject = 'New NTW claim';
    $email->isHTML(TRUE);
    $email->Body = $html_part;
    $email->AltBody = $text_part;

    $email->addAttachment($claim_content[0]['orig_inv_filename'], str_replace('invoices/', '', $claim_content[0]['orig_inv_filename']));
    $email->addAttachment($claim_content[0]['claim_inv_filename'], str_replace('invoices/', '', $claim_content[0]['claim_inv_filename']));


    /* Send the mail. */
    if (!$email->send()) {
        /* PHPMailer error. */
        echo $email->ErrorInfo;
    }

    $dealer = $model->getDealer($_SESSION['dealer_id'], $db);
    if ($dealer['business_email'] && count($dealer['business_email']) > 0) {
        // send mail
        $clientEmail = new PHPMailer(TRUE);
        $clientEmail->setFrom('donotreply@ntwclaims.net', 'NTW Website');
        $clientEmail->addAddress($dealer['business_email'], 'NTW Claims');


        $clientEmail->Subject = 'New NTW claim';
        $clientEmail->isHTML(TRUE);
        $clientEmail->Body = $html_part;
        $clientEmail->AltBody = $text_part;

        $clientEmail->addAttachment($claim_content[0]['orig_inv_filename'], str_replace('invoices/', '', $claim_content[0]['orig_inv_filename']));
        $clientEmail->addAttachment($claim_content[0]['claim_inv_filename'], str_replace('invoices/', '', $claim_content[0]['claim_inv_filename']));


        /* Send the mail. */
        if (!$clientEmail->send()) {
            /* PHPMailer error. */
            echo $clientEmail->ErrorInfo;
        }
    }


}
$model->deleteSession($db);
echo $finished_page;
