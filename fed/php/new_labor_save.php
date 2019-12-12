<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new Database();
$model = new model($db);

$valid_claim = '';
/* Check to see if they are already logged in */

if (isset($_SESSION['valid_claim']) && $_SESSION['valid_claim'] == 1) {
    $valid_claim = 1;
}


if (!empty($valid_claim)) {
    // user logged in
    $id = $model->saveAndGetId($db);
    if ($id) {
//        header("Location: /fed/claim_by_id?claim_id=$id&success");
        if ($_SERVER['HTTP_HOST'] == 'ntwclaimslocal.com') {
            header("Location: claim_by_id?claim_id=$id&success");
        } else {
            $emailPageBody = file_get_contents('templates/claim_by_id.html');
            $claim = $model->getClaimById($db, $id);
            $emailContent['PAGE_TITLE'] = 'Labor Claims';
            foreach ($claim as $k => $v) {
                $emailContent[$k] = $v;
            }

            foreach ($emailContent as $key => $value) {
                $emailPageBody = str_replace('{' . strtoupper($key) . '}', $value, $emailPageBody);
            }
            // print_r($emailPageBody);
            // print_r("Hello Rasel");
            // die();

            $email = new PHPMailer(TRUE);
            $email->setFrom('donotreply@ntwclaims.net', 'Federated Labor Website');
            $email->addAddress('claims@ntwclaims.net', 'New Labor Claims');
            $email->addCC('dhillis@abswarranty.net', 'Destiny Hillis');
            // $email->addCC('dmcneese@abswarranty.net', 'Daniel McNeese');
            // $email->addCC('gpetty@abswarranty.net', 'Gennica Petty');

            $email->Subject = 'New Federated Labor Claim';
            $email->isHTML(TRUE);
            $email->Body = $emailPageBody;
            $email->AltBody = $emailPageBody;
//            $email->addAttachment($claim[0]['orig_inv_filename'], str_replace('invoices/', '', $claim[0]['orig_inv_filename']));
//            $email->addAttachment($claim[0]['claim_inv_filename'], str_replace('invoices/', '', $claim[0]['claim_inv_filename']));

            $email->send();

            $dealer = $model->getDealerInfo($db, $_SESSION['dealer_id']);

            if ($dealer['business_email'] && count($dealer['business_email']) > 0) {
                // send mail
                $dealerEmail = new PHPMailer(TRUE);
                $dealerEmail->setFrom('donotreply@ntwclaims.net', 'Federated Labor Website');
                $dealerEmail->addAddress('dhillis@abswarranty.net', 'Federated Labor Claims');

                $dealerEmail->Subject = 'New Federated Labor Claim';
                $dealerEmail->isHTML(TRUE);
                $dealerEmail->Body = $emailPageBody;
                $dealerEmail->AltBody = $emailPageBody;

//                $dealerEmail->addAttachment($claim[0]['orig_inv_filename'], str_replace('invoices/', '', $claim[0]['orig_inv_filename']));
//                $dealerEmail->addAttachment($claim[0]['claim_inv_filename'], str_replace('invoices/', '', $claim[0]['claim_inv_filename']));
                $dealerEmail->send();
            }

            header("Location: claim_by_id?claim_id=$id&success");

        }

    } else {
        echo 'Unable to save.';
    }
    exit();
} else {
    header("Location: /");
    exit();
}
