<?php
session_start();
require_once('/home/ntwclaim/public_html/library/db/config.php');
require_once('/home/ntwclaim/public_html/library/db/db.php');
$db = new Database();
require_once('/home/ntwclaim/public_html/library/model/site_model.php');
$model = new model($db);


$dealer_update_success = $model->selfUpdateDealer($db);

if ($dealer_update_success == 'success') {
    echo "Successfully updated";


} else {

    echo "There was a problem updating the information. Please try again, or contact us for assistance";


}