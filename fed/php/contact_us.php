<?php
session_start();

$db = new Database();
$model = new model($db);

$message = '';
$content = $model->getHeaderValues($db, $site, $page_id);

$header = file_get_contents('templates/header.html');

/*Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

if (!empty($valid_dealer)) {
    $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_logout.html'), $header);

} else {
    $header = str_replace('{LOGIN_STATUS}', file_get_contents('templates/header_login.html'), $header);
}
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
            case 'rho':
                $site_orig = 'roadhazardonline.com';
                break;
            case 'car':
                $site_orig = 'carroadhazard.com';
                break;
            case 'cwt':
                $site_orig = 'cwtroadhazard.com';
                break;
            case 'tmx':
                $site_orig = 'treadmaxxroadhazard.com';
                break;
            case 'ppr':
                $site_orig = 'partsplusroadhazard.com';
                break;
            case 'apr':
                $site_orig = 'autoprideroadhazard.com';
                break;
            case 'acc':
                $site_orig = 'acccroadhazard.com';
                break;
            case 'pnr':
                $site_orig = 'pnroadhazard.com';
                break;
            case 'arh':
                $site_orig = 'autoroadhazard.com';
                break;
            case 'mtp':
                $site_orig = 'mytireplan.com';
                break;
            case 'frh':
                $site_orig = 'federatedroadhazard.com';
                break;
            case 'ase':
                $site_orig = 'aseroadhazard.com';
                break;
            default:
                $site_orig = 'myroadhazard.com';
                break;
        }


        $message = "The following person has requested information via the Sign Up form at $site_orig:\n\n";
        $message .= "---------------------------------------------------------------------------\n\n";
        $message .= "Customer Name: " . $_POST['contact_name'] . "\n";
        $message .= "Business Name: " . $_POST['business_name'] . "\n";
        $message .= "Address: " . $_POST['address'] . "\n";
        $message .= "City: " . $_POST['city'] . "\n";
        $message .= "State: " . $_POST['state'] . "\n";
        $message .= "Zip: " . $_POST['zip'] . "\n";
        $message .= "Contact Phone: " . $_POST['telephone'] . "\n";
        $message .= "Contact Email: " . $_POST['email'] . "\n\n";
        $message .= "---------------------------------------------------------------------------\n";


        $subject = "Sign Up Request: $site_orig";

        mail("steve@abswarranty.net", $subject, $message, "from: website@$site_orig\nCc:rick@abswarranty.net,tstephens@abswarranty.net\n");
        //mail("zola@zolaweb.com",$subject,$message,"from: website@$site_orig\n");
        $body_copy = file_get_contents('templates/thank_you.html');
    }
} else {
    $body_copy = file_get_contents('templates/' . $page_id . '.html');
}
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
