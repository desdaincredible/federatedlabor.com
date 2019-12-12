<?php
$message = '';
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/site_model.php');
$model = new model($db);

/* Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

$content = $model->getHeaderValues($db, $site, $page_id);

if (!empty($valid_dealer)) {

    $header = '';
    $footer = '';
    $body_copy = file_get_contents('templates/' . $page_id . '.html');
    $tiretotal = 0;
    $claim_id = intval($_REQUEST['cid']);
    $claim_content = $model->getClaimContent($claim_id, $_SESSION['demo'], $db);

    if (empty($claim_content)) {
        header("Location: error?eid=4");
    }
    $claim_content = array_change_key_case($claim_content, CASE_UPPER);
    $content = array_merge($claim_content, $content);
    if ($content['DEMO'] == 1) {
        $content['DEMO'] = 'SAMPLE ';
        $content['SAMPLE'] = 'block';
        $content['CLAIM_ID'] = 'DEMO ' . strtoupper($site) . 'C' . $content['CLAIM_ID'];
    } else {
        $content['DEMO'] = '';
        $content['SAMPLE'] = 'none';
        $content['CLAIM_ID'] = strtoupper($site) . 'C' . $content['CLAIM_ID'];

    }
    $content['CLAIM_DATE'] = date('m/d/Y', strtotime($content['CLAIM_DATE']));
    if ($content['SERVICE_TYPE'] == 'replace') {
        $content['REPLACE_TIRE'] = '<th colspan="6">Replacement Tire Information</th></tr><tr><td><b>Make</b></td><td><b>Model</b></td><td><b>Size</b></td><td colspan="3">&nbsp;</td></tr><tr><td>' . $content['replacement_make'] . '</td><td>' . $content['replacement_model'] . '</td><td>' . $content['replacement_size'] . '</td><td colspan="3">&nbsp;</td></tr>';

    } else {
        $content['REPLACE_TIRE'] = '';
    }
    $content['SERVICE_TYPE'] = ucfirst($content['SERVICE_TYPE']);


} else {
    $content['LOGIN_STATUS'] = file_get_contents('templates/header_login.html');
    $header = file_get_contents('templates/header.html');
    $footer = file_get_contents('templates/footer.html');
    $body_copy = file_get_contents('templates/login.html');
}
$content['MESSAGE'] = $message;

$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

