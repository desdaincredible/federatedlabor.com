<?php
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/classes_model.php');
$model = new model($db);


$content = $model->getHeaderValues($db, $site, $page_id);
$content['training_type'] = 'Classroom';
$content['online'] = '';
$content['class_list'] = '';
$class_list = $model->getCourses(0, $db);
$class_count = count($class_list);

$header = file_get_contents('templates/header.html');

if (!empty($_REQUEST['send_message'])) {

    $server = 'http://www.google.com/recaptcha/api/verify';
    $privatekey = '6Lcn3cESAAAAAFPv9DAywbCt1tv4dQE6ii4Gvt3n';
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $challenge = $_POST['recaptcha_challenge_field'];
    $response = $_POST['recaptcha_response_field'];
    $data = "challenge=$challenge&privatekey=$privatekey&remoteip=$remoteip&response=$response";

    // Set the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the request as a POST FIELD for curl.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Get response from the server.
    $httpResponse = curl_exec($ch);

    if (stristr($httpResponse, 'true') === false) {
        $body_copy = file_get_contents('templates/' . $page_id . '.html');
        $message = "Incorrect Catchpa Entry--Please try again";

    } else {
        switch ($site) {
            default:
                $site_orig = 'ABS Training';
                break;
        }

        $message = "The following person has contacted you at $site_orig:\n\n";
        $message .= "---------------------------------------------------------------------------\n";


        $message .= "Customer Name:     " . $_POST['contact_first_name'] . " " . $_POST['contact_last_name'] . "\n";
        $message .= "Company Name:     " . $_POST['company_name'] . "\n";
        $message .= "Contact Phone:     " . $_POST['telephone'] . "\n";
        $message .= "Contact Email:	" . $_POST['email'] . "\n";
        $message .= "Message:           " . $_POST['contact_message'] . "\n";
        $message .= "---------------------------------------------------------------------------\n";


        $subject = "Contact Us: $site_orig";
        mail("shalgren@abs-inc.biz", $subject, $message, "from: website@abswarranty.net\nCc:rickruel@abs-inc.biz\n");
        $body_copy = file_get_contents('templates/thank_you.html');

    }
} else {
    $body_copy = file_get_contents('templates/training_register.html');
}


$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

