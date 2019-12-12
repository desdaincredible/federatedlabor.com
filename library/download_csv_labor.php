<?php
session_start();
$message = '';
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();

if (isset($_SESSION['admin_id'])) {
    $sql = "Select * from labor_claims";
    $db->query($sql);
    $claims = $db->resultset();
    $date = date('Y_m_d_H_i_s');
    ob_clean();
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment;filename=labor_claims_$date.csv");
    if(isset($claims['0'])){
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($claims['0']));
        foreach($claims AS $values){
            fputcsv($fp, $values);
        }
        fclose($fp);
    }
    ob_flush();

} else {
    // not a valid user.
    echo 'You are not logged in.';
}

