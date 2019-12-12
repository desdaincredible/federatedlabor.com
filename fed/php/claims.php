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
        $message = '<p style="text-align: center;color: #990000"><strong>Incorrect login, please check your information and try again, or call:</strong> <br><br><strong>T3 local claims:</strong> 1-866-830-4191<br>
		<strong>Tread Car Dealer claims:</strong> 1-855-429-2790</p>';
    }
}

$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');

if (!empty($valid_claim)) {
    $body_copy = file_get_contents('templates/' . $page_id . '.html');
} else {
    $body_copy = file_get_contents('templates/login.html');
    $content['FORM_ACTION'] = 'claims';
    $content['PAGE_TITLE'] = '<p class="content_header">Claims History</p>';
}
$content['MESSAGE'] = $message;
$claims = $model->getAllClaims($db);
if (count($claims) > 0) {
    $tbody = '';
    $dealer = $model->getDealerInfo($db, $_SESSION['dealer_id']);
    foreach ($dealer as $k => $v) {
        $content[$k] = $v;
    }
    foreach ($claims as $claim) {
        $tr = "<tr>";
        $tr .= "<td><a href='./claim_by_id?claim_id=" . $claim['claim_id'] . "'>" . $claim['claim_id'] . "</a></td>";
        $tr .= '<td>' . $claim['invoice_number'] . '</td >';
        $tr .= '<td>' . $claim['original_repair_date'] . '</td >';
        $tr .= '<td>' . $claim['customer_first_name'] . '</td >';
        $tr .= '<td>' . $claim['customer_last_name'] . '</td >';
        $tr .= '</tr>';
        $tbody .= $tr;
    }
//    print_r($tbody);
    $content['TBODY'] = $tbody;
} else {
    $content['TBODY'] = 'No claims found . ';
}

$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
