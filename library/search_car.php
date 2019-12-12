<?php
session_start();
$message = '';
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/admin_model.php');
$model = new model($db);

/* Check to see if they are already logged in */
$valid_dealer = $model->checkSession($db);

$content = $model->getHeaderValues($db, $site, $page_id);
$header = file_get_contents('templates/header.html');
$content['ADMIN_MENU'] = file_get_contents('templates/admin_menu.html');


if (!empty($valid_dealer)) {
    $search_info['start'] = (empty($_GET['start'])) ? '' : $_GET['start'];
    $search_info['order'] = (empty($_GET['order'])) ? 'default' : $_GET['order'];
    $search_info['direction'] = (empty($_GET['direction'])) ? 'DESC' : $_GET['direction'];
    if ($search_info['direction'] == 'DESC') {
        $new_direction = 'ASC';
        $arrow = 'down';
    } else {
        $new_direction = 'DESC';
        $arrow = 'up';
    }

    $content['CAR_HEADER'] = '';
    if (!empty($_POST['update_make'])) {

        if (!empty($_POST['delete_make'])) {
            $car_update = $model->deleteMake($_POST['car_make_id'], $db);
        } else {
            $car_update = $model->updateMake($_POST['car_make_id'], mysql_real_escape_string($_POST['make_name']), $db);

        }
    }
    if (!empty($_POST['update_model'])) {

        if (!empty($_POST['delete_model'])) {
            $car_update = $model->deleteModel($_POST['car_model_id'], $db);
        } else {
            $car_update = $model->updateModel($_POST['car_make_id'], $_POST['car_model_id'], mysql_real_escape_string($_POST['model_name']), $db);

        }
    }

    if (!empty($_POST['criteria']) && $_POST['criteria'] == 'make_name' && empty($_POST['unlinked'])) {

        if ($search_info['order'] == 'default') {
            $content['CAR_HEADER'] .= '<div><a href="search_car?order=make_name&amp;direction=ASC"><b>Car Makes</b></a></div>';
        } else {
            $content['CAR_HEADER'] .= '<div><a href="search_car?order=make_name&amp;direction=' . $new_direction . '"><b>Car Makes</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
        }


    }

    if (!empty($_POST['show_cars'])) {
        $search_info['operator'] = (empty($_POST['operator'])) ? '' : $_POST['operator'];
        $search_info['criteria'] = (empty($_POST['criteria'])) ? '' : $_POST['criteria'];
        $search_info['unlinked'] = (empty($_POST['unlinked'])) ? '' : $_POST['unlinked'];
        if (!empty($search_info['unlinked'])) {
            $search_info['term'] = 'view_unlinked';
            $search_info['criteria'] = 'model_name';

        } else {
            $search_info['term'] = (empty($_POST['term'])) ? '' : $_POST['term'];


        }
        $search_info['direction'] = 'ASC';
        $_SESSION['term'] = $search_info['term'];
        $_SESSION['operator'] = $search_info['operator'];
        $_SESSION['criteria'] = $search_info['criteria'];
        $_SESSION['unlinked'] = $search_info['unlinked'];


    } else {
        $search_info['term'] = $_SESSION['term'];
        $search_info['operator'] = $_SESSION['operator'];
        $search_info['criteria'] = $_SESSION['criteria'];
        $search_info['unlinked'] = $_SESSION['unlinked'];


    }

    if ($search_info['criteria'] == 'model_name') {
        $default_car_header = '<div class="makes"><a href="search_car?order=car_make_id&amp;direction=ASC"><b>Car Makes</b></a></div><div class="models"><a href="search_car?order=model_name&amp;direction=ASC"><b>Car Models</b></a></span></div>';
        $default_make_header = '<div class="makes"><a href="search_car?order=car_make_id&amp;direction=ASC"><b>Car Makes</b></a></div>';
        $default_model_header = '<div class="models"><a href="search_car?order=model_name&amp;direction=ASC"><b>Car Models</b></a></div>';

        if ($search_info['direction'] == 'DESC') {
            $new_direction = 'ASC';
            $arrow = 'down';
        } else {
            $new_direction = 'DESC';
            $arrow = 'up';


        }


        switch ($search_info['order']) {
            case 'make_name':
                $content['CAR_HEADER'] .= '<div class="makes"><a href="search_car?order=car_make_id&amp;direction=' . $new_direction . '"><b>Car Makes</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';
                $content['CAR_HEADER'] .= $default_model_header;

                break;
            case 'model_name':
                $content['CAR_HEADER'] .= $default_make_header;
                $content['CAR_HEADER'] .= '<div class="models"><a href="search_car?order=model_name&amp;direction=' . $new_direction . '"><b>Car Models</b> <img border="0" style="vertical-align: middle" src="images/' . $arrow . '_arrow.gif" width="11" height="11"></a></div>';

                break;
            default:
                $content['CAR_HEADER'] .= $default_car_header;

                break;
        }
    }
    if (!empty($content['CAR_HEADER'])) {
        $content['CAR_HEADER'] .= '<div class="clearBoth"></div>';

    }


    if ($search_info['order'] == 'default') {
        $search_info['order'] = $search_info['criteria'];

    }


    $car_models = $model->searchModels($search_info, $db);
    if (empty($car_models)) {
        $content['CAR_INFO'] = '<p style="font-style: italic">There are no makes/models matching your search</p>';

    } else {
        $content['CAR_INFO'] = $car_models;

    }


    $body_copy = file_get_contents('templates/' . $page_id . '.html');


} else {
    $body_copy = file_get_contents('templates/login.html');
}
$content['MESSAGE'] = $message;
$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;
