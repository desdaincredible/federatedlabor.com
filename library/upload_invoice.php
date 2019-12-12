<?php
session_start();
$message = '';
require_once('db/config.php');
require_once('db/db.php');
$db = new Database();
require_once('model/site_model.php');
$model = new model($db);

$valid_claim = '';

/* Check to see if they are already logged in */

if ($_SESSION['valid_claim'] == 1) {
    $valid_claim = 1;
}

$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');


if (!empty($valid_claim)) {
    $body_copy = file_get_contents('templates/' . $page_id . '.html');
    $content['SECOND_TIRE'] = '';
    $content['claim_id'] = $_SESSION['claim_id'];

    if (empty($_POST['next_doc'])) {
        $content['NEXT_DOC'] = 1;
        $content['UPLOAD_INSTRUCTION'] = 'Please upload the original tire invoice';


    } else {
        $content['NEXT_DOC'] = 2;
        $content['UPLOAD_INSTRUCTION'] = 'Please upload the new tire invoice';
    }

    if (!empty($_POST['second_tire'])) {
        $content['SECOND_TIRE'] = '1';


    }

    if (isset($_POST['submit_document'])) {


        $current_doc = 'invoice_' . intval($_POST['next_doc']);
        $target_dir = "invoices/";

        if ($current_doc == 'invoice_1') {
            $target_file = $target_dir . date('Y-m-d') . '_' . $_SESSION['claim_id'] . '_original';
        } else {
            $target_file = $target_dir . date('Y-m-d') . '_' . $_SESSION['claim_id'] . '_claim';
        }


        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        if ($_FILES["$current_doc"]['type'] == 'application/pdf') {
            $check = 1;
        } else {
            $check = getimagesize($_FILES["$current_doc"]['tmp_name']);
        }

        if ($check === false) {
            $uploadOk = 0;
            $message = 'Only image and pdf files are allowed.';
            if ($current_doc = 'invoice_1') {
                $content['NEXT_DOC'] = 1;
                $content['UPLOAD_INSTRUCTION'] = 'Please upload the original tire invoice';
            } else {
                $content['NEXT_DOC'] = 2;
                $content['UPLOAD_INSTRUCTION'] = 'Please upload the new tire invoice';
            }

        } else {
            // Allow certain file formats
            if ($_FILES[$current_doc]['type'] != 'application/pdf' && $_FILES["$current_doc"]['type'] != 'image/jpeg' && $_FILES["$current_doc"]['type'] != 'image/png' && $_FILES["$current_doc"]['type'] != 'image/gif') {
                $uploadOk = 0;
                $message = 'Only PDF, JPG, JPEG, PNG & GIF files are allowed.';

                if ($current_doc == 'invoice_1') {
                    $content['NEXT_DOC'] = 1;
                    $content['UPLOAD_INSTRUCTION'] = 'Please upload the original tire invoice';
                } else {
                    $content['NEXT_DOC'] = 2;
                    $content['UPLOAD_INSTRUCTION'] = 'Please upload the new tire invoice';
                }
                exit;
            } else {
                $ext = pathinfo($_FILES["$current_doc"]['name'], PATHINFO_EXTENSION);
                $target_file = $target_file . '.' . $ext;

                if (!move_uploaded_file($_FILES["$current_doc"]['tmp_name'], $target_file)) {
                    $message = 'There was an error uploading your file. Please call 866-830-4189 to complete your claim';
                    $body_copy = file_get_contents('templates/upload_error.html');

                }//if (!move_uploaded_file(
                else {
                    if ($current_doc == 'invoice_1') {
                        $upload = $model->insertUploadName($db, 'orig_inv_filename', $_SESSION['claim_id'], $_SESSION['demo'], $target_file);

                    }
                    if ($current_doc == 'invoice_2') {
                        $upload = $model->insertUploadName($db, 'claim_inv_filename', $_SESSION['claim_id'], $_SESSION['demo'], $target_file);
                        header("Location: claim_success");
                    }


                }

            }//if($imageFileType != "jpg"

        }//if($check === false) {


    }

} else {
    $body_copy = file_get_contents('templates/login.html');
    $content['FORM_ACTION'] = 'new_claim';
    $content['PAGE_TITLE'] = '<p class="content_header">File A Claim</p>';

}

$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);

}

echo $finished_page;
