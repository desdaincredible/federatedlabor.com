<?php

session_start();

$db = new Database();

$model = new model($db);


$valid_claim = '';
$show_form = '';
$info_page = '';
$message = '';



/* Check to see if they are already logged in */



if (isset($_SESSION['valid_claim']) && $_SESSION['valid_claim'] == 1) {

    $valid_claim = 1;

}



if (!empty($_POST['dealer_zip']) && !empty($_POST['dealer_phone'])) {

    $valid_claim = $model->claimLogin($db, $site);

    if (empty($valid_claim)) {

        $message = '<p style="text-align: center;color: #990000"><strong>Incorrect login, please check your information and try again, or call 1-888-450-2816.</p>';

    }

}



$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');



if (!empty($valid_claim)) {

    $body_copy = file_get_contents('templates/' . $page_id . '.html');

} else {

    $body_copy = file_get_contents('templates/login.html');

    $content['FORM_ACTION'] = 'claim_by_id';

    $content['PAGE_TITLE'] = 'Claim Submitted';

}

$content['MESSAGE'] = $message;



if (isset($_GET['claim_id'])) {

    $claim = $model->getClaimById($db, $_GET['claim_id']);

    if ($claim) {
        $content['PAGE_TITLE'] = 'Labor Claims';
        foreach($claim as $k => $v) {
            $content[$k] = $v;
        }

    } else {
        $content['PAGE_TITLE'] = 'No Claim found for this Id.';
    }

} else {
    $content['PAGE_TITLE'] = 'No Id Provided.';
}



$footer = file_get_contents('templates/footer.html');

$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}


echo $finished_page;

