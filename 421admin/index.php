<?php
$site = 'ntw';
define("ROOT", __DIR__ . "/");
set_include_path(ROOT . '../library');

$page_id = (!empty($_GET['id'])) ? $_GET['id'] : 'admin_index';
$page_id = preg_replace("/[^a-zA-Z0-9_]/", "", $page_id);
$pages = array(
    'admin_index',
    'dealer_message',
    'dealer_registration', 'dealer_check',
    'get_dealer', 'show_dealers', 'dealer_edit', 'dealer_update',
    'get_plan', 'show_plans', 'admin_plan_edit', 'show_claims',
    'admin_claims', 'add_dealers', 'car_edit', 'search_car',
    'tire_edit', 'error', 'success',
    'admin_claim_search_ajax',
    'admin_claim_labor_search_ajax',
    'show_claims_labor',
    'admin_claims_labor',
    'download_csv_tire',
    'download_csv_labor'
);

if (in_array($page_id, $pages)) {
    $include_page = $page_id . '.php';

} else {
    $page_id = 'admin_index';
    $include_page = 'admin_index.php';
}

require_once($include_page);

