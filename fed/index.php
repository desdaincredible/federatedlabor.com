<?php
$site = 'fed';
define("ROOT", __DIR__ . "/");
set_include_path(ROOT . '/php');

require_once('lib/db/config.php');
require_once('lib/db/db.php');
require_once 'lib/PHPMailer.php';
require_once 'lib/Exception.php';
require_once('model/site_model.php');

$page_id = (!empty($_GET['id'])) ? $_GET['id'] : 'index';

$page_id = preg_replace("/[^a-zA-Z0-9_]/", "", $page_id);

$pages = array('index', 'contact_us', 'how_it_works',
    'sign_up', 'login', 'dealer_index', 'upload_invoice',
    'faqs', 'new_labor', 'new_labor_save', 'claims', 'claim_by_id',
    'claim_check', 'claim_statement', 'show_models',
    'claim_success', 'upload_error', 'error');
if (in_array($page_id, $pages)) {
    $include_page = $page_id . '.php';
} else {
    $page_id = 'index';
    $include_page = 'index.php';
}
//print_r($include_page);

require_once($include_page);

