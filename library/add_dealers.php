<?php
$message = '';
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/admin_model.php');
$model = new model($db);


$row = 1;
if (($handle = fopen("../invoices/NTW-Dealers-edit.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row > 1) {

            $content['business_name'] = $data[0];
            $content['business_address'] = $data[1];
            $content['business_city'] = $data[2];
            $content['business_state'] = $data[3];
            $content['business_zip'] = $data[4];
            $content['business_phone'] = $data[5];
            $content['business_fax'] = $data[6];

            $contact = explode(" ", $data[7]);

            if (count($contact) == 2) {
                $content['contact_first_name'] = $contact[0];
                $content['contact_last_name'] = $contact[1];
            } else {
                $content['contact_first_name'] = 'Please enter name';
                $content['contact_last_name'] = $data[7];


            }
            $content['business_email'] = $data[8];

            $content['contact_title'] = '';
            if (strtolower($data[9]) == 'inactive') {
                $content['inactive'] = 1;
            } else {
                $content['inactive'] = 0;

            }

            $num = count($data);
            echo "<p> $num fields in line $row: <br /></p>\n";

            for ($c = 0; $c < $num; $c++) {
                echo $data[$c] . "<br />\n";
            }


            $insertCsvDealer = $model->insertCsvDealer($content, $site, $db);


        }
        $row++;

    }
    fclose($handle);
}