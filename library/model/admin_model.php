<?php

class model
{
    function getHeaderValues($db, $site, $template = 'index')
    {
        $content['meta'] = '';
        $content['script'] = '';
        $content['css'] = '';
        $content['title'] = '';
        $sql = "SELECT * FROM page_header_info WHERE (site='all' OR site=:site) AND (page=:template OR page='all')";
        $db->query($sql);
        $db->bind(':site', $site, PDO::PARAM_STR);
        $db->bind(':template', $template, PDO::PARAM_STR);
        $get_headers = $db->resultset();

        /* Prepare for content display */
        $header_count = count($get_headers);

        if ($header_count > 0) {

            for ($i = 0; $i < $header_count; ++$i) {
                $content[$get_headers[$i]['type']] .= $get_headers[$i]['value'] . "\n";
            }
        }

        return $content;

    }

    function checkSession($db)
    {
        $valid_dealer = 0;
        $now = date('Y-m-d H:i:s');
        if (isset($_SESSION['admin_id'])) {
            $sql = "SELECT * FROM current_users WHERE session_id=:session_id && dealer_id=:dealer_id";
            $db->query($sql);
            $db->bind(':session_id', session_id(), PDO::PARAM_STR);
            $db->bind(':dealer_id', $_SESSION['admin_id'], PDO::PARAM_INT);
            $row = $db->single();

            if (!empty($row['dealer_id'])) {
                $valid_dealer = 1;
                $sql = "UPDATE current_users SET date='$now' WHERE session_id=:session_id && dealer_id=:dealer_id";
                $db->query($sql);
                $db->bind(':session_id', session_id(), PDO::PARAM_STR);
                $db->bind(':dealer_id', $_SESSION['admin_id'], PDO::PARAM_INT);
                $db->execute();


            }
        }
        if (empty($valid_dealer)) {
            /* Remove any current session */
            $_SESSION = array();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

        }
        return $valid_dealer;
    }

    function newSession(&$db, $username, $password)
    {

        $valid_dealer = 0;
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM administrators  WHERE username= :username AND password=:password";
        $db->query($sql);
        $db->bind(':username', $username, PDO::PARAM_INT);
        $db->bind(':password', $password, PDO::PARAM_INT);
        $row = $db->single();

        if (!empty($row['id'])) {
            $_SESSION['admin_id'] = $row['id'];

            $sql = "INSERT INTO current_users (session_id, dealer_id, date) VALUES (:session_id, :dealer_id, :now)";
            $db->query($sql);
            $db->bind(':session_id', session_id(), PDO::PARAM_STR);
            $db->bind(':dealer_id', $row['id'], PDO::PARAM_INT);
            $db->bind(':now', $now, PDO::PARAM_STR);
            $db->execute();

            $valid_dealer = 1;

        }

        /*Clean up old sessions */
        $latest_date = date('Y-m-d H:i:s', strtotime($now . ' -4 hours'));
        $sql = "DELETE FROM current_users WHERE date < :latest_date";

        $db->query($sql);
        $db->bind(':latest_date', $latest_date, PDO::PARAM_INT);
        $db->execute();


        if (empty($valid_dealer)) {
            /* Remove any current session */
            $_SESSION = array();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

        }
        return $valid_dealer;


    }

    function deleteSession($db)
    {
        $sql = "DELETE FROM current_users WHERE session_id=:session_id && dealer_id=:dealer_id";
        $db->query($sql);
        $db->bind(':session_id', session_id(), PDO::PARAM_STR);
        $db->bind(':dealer_id', $_SESSION['admin_id'], PDO::PARAM_INT);
        $db->execute();

    }

    function insertDealer($content, $site, $db)
    {
        $now = date('Y-m-d');
//        print_r($content);

        foreach ($_POST as $k => $v) {
//            print_r($k);
            $$k = $v;
        }
//        die();

        $business_phone = str_replace(' ', '', $business_phone);
        $business_phone = str_replace('(', '', $business_phone);
        $business_phone = str_replace(')', '', $business_phone);
        $business_phone = str_replace('-', '', $business_phone);


        /* Update the dealer info */
        $sql = "INSERT INTO dealer_registration (business_name, business_address, business_city, business_state, business_zip, business_phone, business_fax, business_email, contact_first_name, contact_last_name, contact_title, site) VALUES ( :business_name, :business_address, :business_city, :business_state, :business_zip, :business_phone, :business_fax, :business_email, :contact_first_name, :contact_last_name, :contact_title, :site )";

        $db->query($sql);
        $db->bind(':business_name', $business_name, PDO::PARAM_STR);
        $db->bind(':business_address', $business_address, PDO::PARAM_STR);
        $db->bind(':business_city', $business_city, PDO::PARAM_STR);
        $db->bind(':business_state', $business_state, PDO::PARAM_STR);
        $db->bind(':business_zip', $business_zip, PDO::PARAM_STR);
        $db->bind(':business_phone', $business_phone, PDO::PARAM_STR);
        if (empty($business_fax)) {
            $db->bind(':business_fax', NULL, PDO::PARAM_INT);
        } else {
            $db->bind(':business_fax', $business_fax, PDO::PARAM_STR);
        }
        $db->bind(':business_email', $business_email, PDO::PARAM_STR);
        $db->bind(':contact_first_name', $contact_first_name, PDO::PARAM_STR);
        $db->bind(':contact_last_name', $contact_last_name, PDO::PARAM_STR);
        $db->bind(':contact_title', $contact_title, PDO::PARAM_STR);
        $db->bind(':site', $site, PDO::PARAM_STR);

        if ($db->execute()) {
            return 'success';


        } else {
            header("Location: error?eid=7");

        }


    }

    function insertCsvDealer($content, $site, $db)
    {
        $now = date('Y-m-d');

        foreach ($content as $k => $v) {
            $$k = $v;
        }

        $business_phone = str_replace(' ', '', $business_phone);
        $business_phone = str_replace('(', '', $business_phone);
        $business_phone = str_replace(')', '', $business_phone);
        $business_phone = str_replace('-', '', $business_phone);


        /* Update the dealer info */
        $sql = "INSERT INTO dealer_registration (business_name, business_address, business_city, business_state, business_zip, business_phone, business_fax, business_email, contact_first_name, contact_last_name, contact_title, inactive) VALUES ( :business_name, :business_address, :business_city, :business_state, :business_zip, :business_phone, :business_fax, :business_email, :contact_first_name, :contact_last_name, :contact_title, :inactive )";

        $db->query($sql);
        $db->bind(':business_name', $business_name, PDO::PARAM_STR);
        $db->bind(':business_address', $business_address, PDO::PARAM_STR);
        $db->bind(':business_city', $business_city, PDO::PARAM_STR);
        $db->bind(':business_state', $business_state, PDO::PARAM_STR);
        $db->bind(':business_zip', $business_zip, PDO::PARAM_STR);
        $db->bind(':business_phone', $business_phone, PDO::PARAM_STR);
        if (empty($business_fax)) {
            $db->bind(':business_fax', NULL, PDO::PARAM_INT);
        } else {
            $db->bind(':business_fax', $business_fax, PDO::PARAM_STR);
        }
        $db->bind(':business_email', $business_email, PDO::PARAM_STR);
        $db->bind(':contact_first_name', $contact_first_name, PDO::PARAM_STR);
        $db->bind(':contact_last_name', $contact_last_name, PDO::PARAM_STR);
        $db->bind(':contact_title', $contact_title, PDO::PARAM_STR);
        $db->bind(':inactive', $inactive, PDO::PARAM_STR);

        if ($db->execute()) {
            return 'success';


        } else {
            header("Location: error?eid=7");

        }


    }


    function updateDealer($content, $site, $db)
    {

        $now = date('Y-m-d');

        foreach ($content as $k => $v) {
            $$k = $v;
        }

        if (!empty($business_phone)) {

            $business_phone = str_replace(' ', '', $business_phone);
            $business_phone = str_replace('(', '', $business_phone);
            $business_phone = str_replace(')', '', $business_phone);
            $business_phone = str_replace('-', '', $business_phone);
        }

        if ($change_status == 1) {

            if ($inactive == -1) {
                $sql = "DELETE FROM dealer_registration WHERE dealer_id=:dealer_id";
                $db->query($sql);
                $db->bind(':dealer_id', $dealer_id, PDO::PARAM_INT);

            } else {
                /* Update the status info */
                $sql = "UPDATE dealer_registration  SET inactive=:inactive WHERE dealer_id=:dealer_id";
                $db->query($sql);
                $db->bind(':dealer_id', $dealer_id, PDO::PARAM_INT);
                $db->bind(':inactive', $inactive, PDO::PARAM_INT);

            }

        } else {

            /* Update the dealer info */
            $sql = "UPDATE dealer_registration SET business_name=:business_name, business_address=:business_address, business_city=:business_city, business_state=:business_state, business_zip=:business_zip, business_phone=:business_phone, business_fax=:business_fax, business_email=:business_email, contact_first_name=:contact_first_name, contact_last_name=:contact_last_name, contact_title=:contact_title WHERE dealer_id=:dealer_id";
            //$sql = "UPDATE dealer_registration SET business_name=$business_name, business_address=$business_address, business_city=$business_city, business_state=$business_state, business_zip=$business_zip, business_phone=$business_phone, business_fax=$business_fax, business_email=$business_email, contact_first_name=$contact_first_name, contact_last_name=$contact_last_name, contact_title=$contact_title WHERE dealer_id=$dealer_id";

            $db->query($sql);
            $db->bind(':business_name', $business_name, PDO::PARAM_STR);
            $db->bind(':business_address', $business_address, PDO::PARAM_STR);
            $db->bind(':business_city', $business_city, PDO::PARAM_STR);
            $db->bind(':business_state', $business_state, PDO::PARAM_STR);
            $db->bind(':business_zip', $business_zip, PDO::PARAM_STR);
            $db->bind(':business_phone', $business_phone, PDO::PARAM_STR);
            if (empty($business_fax)) {
                $db->bind(':business_fax', NULL, PDO::PARAM_INT);
            } else {
                $db->bind(':business_fax', $business_fax, PDO::PARAM_STR);
            }
            $db->bind(':business_email', $business_email, PDO::PARAM_STR);
            $db->bind(':contact_first_name', $contact_first_name, PDO::PARAM_STR);
            $db->bind(':contact_last_name', $contact_last_name, PDO::PARAM_STR);
            $db->bind(':contact_title', $contact_title, PDO::PARAM_STR);
            $db->bind(':dealer_id', $orig_dealer_id, PDO::PARAM_INT);
            $db->execute();
        }

        if ($db->execute()) {
            return 'success';


        } else {
            header("Location: error?eid=8");

        }


    }

    function getMakes($db, $direction = '')
    {
        if (empty($direction)) {
            $direction = 'ASC';

        }

        $sql = "SELECT * FROM car_makes ORDER BY make_name $direction";
        $result = $db->sql_query($sql);
        $makes = '';
        while ($row = $db->sql_fetchrow($result)) {
            $makes .= '<option value="' . $row['car_make_id'] . '">' . $row['make_name'] . '</option>' . "\n";


        }
        return $makes;

    }

    function insertMake($item, $db)
    {
        $sql = "INSERT INTO car_makes (make_name) VALUES('$item')";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");
        } else {
            return 'success';
        }


    }

    function updateMake($id, $item, $db)
    {
        $sql = "UPDATE car_makes SET make_name='$item' WHERE car_make_id=$id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';
        }


    }

    function deleteMake($id, $db)
    {
        $sql = "DELETE FROM car_models WHERE car_make_id=$id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            $sql = "DELETE FROM car_makes WHERE car_make_id=$id";
            $result = $db->sql_query($sql);
            if (mysql_error()) {

                header("Location: error?eid=9");

            } else {
                return 'success';
            }
        }


    }

    function insertModel($id, $item, $db)
    {
        $sql = "INSERT INTO car_models (car_make_id, model_name) VALUES($id, '$item')";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");
        } else {
            return 'success';
        }


    }

    function updateModel($id, $make_id, $item, $db)
    {
        $sql = "UPDATE car_models SET car_make_id=$id, model_name='$item' WHERE car_model_id=$make_id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';
        }


    }

    function deleteModel($id, $db)
    {
        $sql = "DELETE FROM car_models WHERE car_model_id=$id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';


        }


    }


    function searchModels($search_info, $db)
    {
        $model_info = '';
        if (empty($search_info['start'])) {
            if ($search_info['term'] == 'view_unlinked') {
                $sql = "SELECT count(car_model_id) AS model_count FROM car_models  WHERE car_make_id = 0 ORDER by " . $search_info['order'] . " " . $search_info['direction'];
            } else {

                if ($search_info['operator'] == 'starts') {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                } else {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                }

                if ($search_info['criteria'] == 'make_name') {
                    $sql = "SELECT count(car_make_id) AS model_count FROM car_makes  $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'];


                } else {
                    $sql = "SELECT count(car_model_id) AS model_count FROM car_models $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'];
                }


            }

            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);

            $model_count = $row['model_count'];

            if ($model_count % 10 == 0) {

                $_SESSION['max_page'] = $model_count / 10;
            } else {
                $_SESSION['max_page'] = floor($model_count / 10) + 1;
            }

            $search_info['start'] = 1;
            $prev = 1;
            if ($_SESSION['max_page'] < 2) {
                $next = 1;
                $last = 1;

            } else {
                $next = 2;
                $last = $_SESSION['max_page'];

            }

        } else {
            $last = $_SESSION['max_page'];

            if ($search_info['start'] > 1) {
                $prev = $search_info['start'] - 1;
            } else {
                $prev = 1;

            }

            if ($search_info['start'] < $_SESSION['max_page']) {
                $next = $search_info['start'] + 1;


            } else {
                $next = $_SESSION['max_page'];

            }


        }
        if ($search_info['start'] == 1) {
            $limit = 0;

        } else {
            $limit = ($search_info['start'] - 1) * 10;

        }


        $makes = $this->getMakes($db, $search_info['direction']);

        if ($search_info['term'] == 'view_unlinked') {
            $sql = "SELECT * FROM car_models  WHERE car_make_id = 0  ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
        } else {
            if ($search_info['operator'] != 'equals') {
                if ($search_info['operator'] == 'starts') {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                } else {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                }


            }
            if ($search_info['criteria'] == 'make_name') {
                $sql = "SELECT * FROM car_makes $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";

            } else {
                $sql = "SELECT * FROM car_models $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
            }
        }

        $result = $db->sql_query($sql);
        $i = 0;
        while ($model_row = $db->sql_fetchrow($result)) {
            if (isset($search_info['criteria']) && $search_info['criteria'] == 'make_name') {
                $model_info .= '<form method="post" name="car_update_' . $i . '" action="search_car" class="clearBoth">';
                $model_info .= '<div><input type="text" size="20" name="make_name" id="make_name_" value="' . $model_row['make_name'] . '">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="delete_make" value="1"> Check to delete&nbsp;&nbsp;<input type="submit" value="Submit" name="update_make"><input type="hidden" name="car_make_id" value="' . $model_row['car_make_id'] . '"></div></form>';
            } else {
                $model_info .= '<form method="post" name="car_update_' . $i . '" action="search_car" class="clearBoth">';
                $model_info .= '<div><select name="car_make_id" size="1">';
                $update_makes = str_replace('value="' . $model_row['car_make_id'] . '"', 'value="' . $model_row['car_make_id'] . '" selected', $makes);
                $model_info .= $update_makes . '</select><input style= type="text" size="20" name="model_name" id="model_name_" value="' . $model_row['model_name'] . '">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="delete_model" value="1"> Check to delete&nbsp;&nbsp;<input type="submit" value="Submit" name="update_model"><input type="hidden" name="car_model_id" value="' . $model_row['car_model_id'] . '"></div></form>';

            }
            $i++;
        }

        if ($_SESSION['max_page'] > 1) {
            $model_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="search_car?start=1&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="search_car?start=' . $prev . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="search_car?start=' . $next . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="search_car?start=' . $last . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';
        }
        return $model_info;

    }


    function getModels($db, $term, $make_id)
    {
        $add_make = '';
        if (!empty($make_id)) {
            $add_make = " AND (car_make_id = $make_id OR car_make_id = 0)";

        }

        $sql = "SELECT * FROM car_models WHERE model_name LIKE '%$term%'$add_make";
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {

            if (empty($json)) {
                $json = '[{"value": "' . $row['model_name'] . '",  "make_id": "' . $row['car_make_id'] . '"}';


            } else {
                $json .= ', {"value": "' . $row['model_name'] . '",  "make_id": "' . $row['car_make_id'] . '"}';
            }


        }
        if (empty($json)) {
            $json = '[{"value": "No Suggestions Found",  "make_id": "0"}]';


        } else {
            $json .= ']';

        }
        return $json;

    }

    /*	function getTireMakes($db, $term){
            $sql = "SELECT * FROM tire_makes WHERE make_name LIKE '%$term%'";
            $result = $db->sql_query($sql);
            while($row = $db->sql_fetchrow($result)){

                if(empty($json)){
                    $json = '[{"value": "'.$row['make_name'].'",  "make_id": "'.$row['tire_make_id'].'"}';

                }
                else{
                    $json .= ', {"value": "'.$row['make_name'].'",  "make_id": "'.$row['tire_make_id'].'"}';
                }


            }
            if(empty($json)){
                    $json = '[{"value": "No Suggestions Found",  "make_id": "0"}]';

            }
            else{
                $json .= ']';

            }
            return $json;

        }

    */


    function getTireMakes($db, $direction = '')
    {
        if (empty($direction)) {
            $direction = 'ASC';

        }

        $sql = "SELECT * FROM tire_makes ORDER BY make_name $direction";
        $result = $db->sql_query($sql);
        $makes = '';
        while ($row = $db->sql_fetchrow($result)) {
            $makes .= '<option value="' . $row['tire_make_id'] . '">' . $row['make_name'] . '</option>' . "\n";


        }
        return $makes;

    }


    function insertTireMake($item, $db)
    {
        $sql = "INSERT INTO tire_makes (make_name) VALUES('$item')";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';
        }


    }

    function updateTireMake($id, $item, $db)
    {
        $sql = "UPDATE tire_makes SET make_name='$item' WHERE tire_make_id=$id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';
        }


    }

    function deleteTireMake($id, $db)
    {
        $sql = "DELETE FROM tire_models WHERE tire_make_id=$id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            $sql = "DELETE FROM tire_makes WHERE tire_make_id=$id";
            $result = $db->sql_query($sql);
            if (mysql_error()) {

                header("Location: error?eid=9");

            } else {
                return 'success';
            }
        }


    }

    function insertTireModel($id, $item, $db)
    {
        $sql = "INSERT INTO tire_models (tire_make_id, model_name) VALUES($id, '$item')";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';
        }


    }

    function updateTireModel($id, $make_id, $item, $db)
    {
        $sql = "UPDATE tire_models SET tire_make_id=$id, model_name='$item' WHERE tire_model_id=$make_id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';
        }


    }

    function deleteTireModel($id, $db)
    {
        $sql = "DELETE FROM tire_models WHERE tire_model_id=$id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=9");

        } else {
            return 'success';


        }


    }


    function searchTireModels($search_info, $db)
    {
        $model_info = '';
        if (empty($search_info['start'])) {
            if ($search_info['term'] == 'view_unlinked') {
                $sql = "SELECT count(tire_model_id) AS model_count FROM tire_models  WHERE tire_make_id = 0 ORDER by " . $search_info['order'] . " " . $search_info['direction'];
            } else {

                if ($search_info['operator'] == 'starts') {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                } else {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                }

                if ($search_info['criteria'] == 'make_name') {
                    $sql = "SELECT count(tire_make_id) AS model_count FROM tire_makes  $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'];


                } else {
                    $sql = "SELECT count(tire_model_id) AS model_count FROM tire_models $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'];
                }


            }

            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);

            $model_count = $row['model_count'];

            if ($model_count % 10 == 0) {

                $_SESSION['max_page'] = $model_count / 10;
            } else {
                $_SESSION['max_page'] = floor($model_count / 10) + 1;
            }

            $search_info['start'] = 1;
            $prev = 1;
            if ($_SESSION['max_page'] < 2) {
                $next = 1;
                $last = 1;

            } else {
                $next = 2;
                $last = $_SESSION['max_page'];

            }

        } else {
            $last = $_SESSION['max_page'];

            if ($search_info['start'] > 1) {
                $prev = $search_info['start'] - 1;
            } else {
                $prev = 1;

            }

            if ($search_info['start'] < $_SESSION['max_page']) {
                $next = $search_info['start'] + 1;


            } else {
                $next = $_SESSION['max_page'];

            }


        }
        if ($search_info['start'] == 1) {
            $limit = 0;

        } else {
            $limit = ($search_info['start'] - 1) * 10;

        }


        $makes = $this->getTireMakes($db, $search_info['direction']);

        if ($search_info['term'] == 'view_unlinked') {
            $sql = "SELECT * FROM tire_models  WHERE tire_make_id = 0  ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
        } else {
            if ($search_info['operator'] != 'equals') {
                if ($search_info['operator'] == 'starts') {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                } else {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                }


            }
            if ($search_info['criteria'] == 'make_name') {
                $sql = "SELECT * FROM tire_makes $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";

            } else {
                $sql = "SELECT * FROM tire_models $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
            }
        }

        $result = $db->sql_query($sql);
        $i = 0;
        while ($model_row = $db->sql_fetchrow($result)) {
            if (isset($search_info['criteria']) && $search_info['criteria'] == 'make_name') {
                $model_info .= '<form method="post" name="tire_update_' . $i . '" action="search_tire" class="clearBoth">';
                $model_info .= '<div><input type="text" size="20" name="make_name" id="make_name_" value="' . $model_row['make_name'] . '">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="delete_make" value="1"> Check to delete&nbsp;&nbsp;<input type="submit" value="Submit" name="update_make"><input type="hidden" name="tire_make_id" value="' . $model_row['tire_make_id'] . '"></div></form>';
            } else {
                $model_info .= '<form method="post" name="tire_update_' . $i . '" action="search_tire" class="clearBoth">';
                $model_info .= '<div><select name="tire_make_id" size="1">';
                $update_makes = str_replace('value="' . $model_row['tire_make_id'] . '"', 'value="' . $model_row['tire_make_id'] . '" selected', $makes);
                $model_info .= $update_makes . '</select><input style= type="text" size="20" name="model_name" id="model_name_" value="' . $model_row['model_name'] . '">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="delete_model" value="1"> Check to delete&nbsp;&nbsp;<input type="submit" value="Submit" name="update_model"><input type="hidden" name="tire_model_id" value="' . $model_row['tire_model_id'] . '"></div></form>';

            }
            $i++;
        }

        if ($_SESSION['max_page'] > 1) {
            $model_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="search_tire?start=1&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="search_tire?start=' . $prev . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="search_tire?start=' . $next . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="search_tire?start=' . $last . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';
        }
        return $model_info;

    }

    function getDealer($dealer_id, $db)
    {
        $dealer_info = '';


        $sql = "SELECT * FROM dealer_registration WHERE dealer_id=:dealer_id";

        $db->query($sql);
        $db->bind(':dealer_id', $dealer_id, PDO::PARAM_INT);
        $dealer_info = $db->single();
        if ($dealer_info !== FALSE) {
            return $dealer_info;


        } else {
            header("Location: error?eid=3");

        }


    }

    function searchDealers($search_info, $db)
    {
        $dealer_info = '';
        /* check search criteria */

        $allowed_criteria = array('contact_last_name', 'dealer_id', 'dealer_phone', 'business_name');
        if (!in_array($search_info['criteria'], $allowed_criteria)) {
            $search_info['criteria'] = 'business_name';

        }

        if (empty($search_info['start'])) {


            if ($search_info['term'] == 'view_inactive') {
                $sql = "SELECT count(dealer_id) AS dealer_count FROM dealer_registration WHERE inactive > 0";
                $db->query($sql);

            } else {
                if ($search_info['operator'] != 'equals') {
                    if ($search_info['operator'] == 'starts') {
                        $criteria = "WHERE " . $search_info['criteria'] . " LIKE :search_term";
                        $search_term = $search_info['term'] . '%';
                    } else {
                        $criteria = "WHERE " . $search_info['criteria'] . " LIKE :search_term";
                        $search_term = '%' . $search_info['term'] . '%';


                    }


                } else {

                    $criteria = "WHERE " . $search_info['criteria'] . " = :search_term";
                    $search_term = $search_info['term'];

                }

                $sql = "SELECT count(dealer_id) AS dealer_count FROM dealer_registration $criteria";
                $db->query($sql);
                $db->bind(':search_term', $search_term, PDO::PARAM_STR);

            }
            $number_of_rows = $db->execute();
            $dealer_count = $number_of_rows->fetchColumn();


            if ($dealer_count % 10 == 0) {

                $_SESSION['max_page'] = $dealer_count / 10;
            } else {
                $_SESSION['max_page'] = floor($dealer_count / 10) + 1;
            }

            $search_info['start'] = 1;
            $prev = 1;
            if ($_SESSION['max_page'] < 2) {
                $next = 1;
                $last = 1;

            } else {
                $next = 2;
                $last = $_SESSION['max_page'];

            }

        } else {
            $last = $_SESSION['max_page'];

            if ($search_info['start'] > 1) {
                $prev = $search_info['start'] - 1;
            } else {
                $prev = 1;

            }

            if ($search_info['start'] < $_SESSION['max_page']) {
                $next = $search_info['start'] + 1;


            } else {
                $next = $_SESSION['max_page'];

            }


        }
        if ($search_info['start'] == 1) {
            $limit = 0;

        } else {
            $limit = ($search_info['start'] - 1) * 10;

        }
        $search_direction = !empty($search_info['direction']) ? $search_info['direction'] : 'ASC';
        if ($search_direction != 'ASC' && $direction != 'DESC') {
            $direction = 'ASC';

        }
        $search_order = !empty($search_info['order']) ? $search_info['order'] : $search_info['criteria'];


        if ($search_info['term'] == 'view_inactive') {
            $sql = "SELECT * from dealer_registration WHERE inactive > 0 ORDER BY $search_order $search_direction LIMIT $limit, 10";
            $db->query($sql);
        } else {
            if ($search_info['operator'] != 'equals') {
                if ($search_info['operator'] == 'starts') {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE :search_term";
                    $search_term = $search_info['term'] . '%';
                } else {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE :search_term";
                    $search_term = '%' . $search_info['term'] . '%';


                }


            }

            $sql = "SELECT * from dealer_registration $criteria ORDER BY $search_order $search_direction LIMIT $limit, 10";
            $db->query($sql);
            $db->bind(':search_term', $search_term, PDO::PARAM_STR);

        }


        $get_dealers = $db->resultset();

        /* Prepare for content display */
        $dealer_count = count($get_dealers);

        if ($dealer_count > 0) {

            for ($i = 0; $i < $dealer_count; ++$i) {
                $dealer_info .= '<form method="post" name="dealer_update_' . $i . '" action="dealer_update" class="clearBoth"><div class="dealer_id"><a href="dealer_edit?did=' . $get_dealers[$i]['dealer_id'] . '">' . $get_dealers[$i]['dealer_id'] . '</a></div><div class="contact_last_name">' . $get_dealers[$i]['contact_last_name'] . '</div><div class="business_name">' . $get_dealers[$i]['business_name'] . '</div><div class="set_status"><select size="1" name="set_status"><option>Select Status</option><option value="past_due">Past Due</option><option value="general">De-Activate</option><option value="delete">Delete</option><option value="activate">Re-Activate</option></select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1"><input type="hidden" name="dealer_id" value="' . $get_dealers[$i]['dealer_id'] . '"></div></form>';
            }

            if ($_SESSION['max_page'] > 1) {
                $dealer_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="show_dealers?start=1&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="show_dealers?start=' . $prev . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="show_dealers?start=' . $next . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="show_dealers?start=' . $last . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';

            }
        }


        return $dealer_info;

    }

    function searchClaims($claim_date, $order, $direction, $start, $db)
    {
        $claim_info = '';

        if (empty($start)) {
            $sql = "SELECT count(claim_id) AS claim_count from dealer_claims WHERE claim_date = :claim_date";
            $db->query($sql);
            $db->bind(':claim_date', $claim_date, PDO::PARAM_STR);
            $row = $db->single();
            $claim_count = $row['claim_count'];

            if ($claim_count % 10 == 0) {

                $_SESSION['max_page'] = $claim_count / 10;
            } else {
                $_SESSION['max_page'] = floor($claim_count / 10) + 1;
            }

            $start = 1;
            $prev = 1;
            if ($_SESSION['max_page'] < 2) {
                $next = 1;
                $last = 1;

            } else {
                $next = 2;
                $last = $_SESSION['max_page'];

            }

        } else {
            $last = $_SESSION['max_page'];

            if ($start > 1) {
                $prev = $start - 1;
            } else {
                $prev = 1;

            }

            if ($start < $_SESSION['max_page']) {
                $next = $start + 1;


            } else {
                $next = $_SESSION['max_page'];

            }


        }
        if ($start == 1) {
            $limit = 0;

        } else {
            $limit = ($start - 1) * 10;

        }
        $search_direction = !empty($direction) ? $direction : 'ASC';
        if ($search_direction != 'ASC' && $direction != 'DESC') {
            $direction = 'ASC';

        }
        $search_order = !empty($order) ? $order : 'claim_date';


        $sql = "SELECT * from dealer_claims WHERE claim_date = :claim_date ORDER by $search_order $search_direction LIMIT $limit, 10";

        $db->query($sql);
        $db->bind(':claim_date', $claim_date, PDO::PARAM_STR);
        $get_claims = $db->resultset();

        /* Prepare for content display */
        $claims_count = count($get_claims);

        if ($claims_count > 0) {

            for ($i = 0; $i < $claims_count; ++$i) {

                if ($get_claims[$i]['claim_date'] == '0000-00-00 00:00:00') {
                    $claim_date = '';
                } else {
                    $claim_date = date('m/d/Y', strtotime($get_claims[$i]['claim_date']));
                }
                $claim_info .= '<div class="claim_id"><a href="admin_claims?cid=' . $get_claims[$i]['claim_id'] . '">' . $get_claims[$i]['claim_id'] . '</a></div><div class="claim_date">' . $claim_date . '</div><div class="dealer_id">' . $get_claims[$i]['dealer_id'] . '</div>';
            }
        }


        if ($_SESSION['max_page'] > 1) {
            $claim_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="show_claims?start=1&amp;order=' . $search_order . '&amp;direction=' . $search_direction . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="show_claims?start=' . $prev . '&amp;order=' . $search_order . '&amp;direction=' . $search_direction . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="show_claims?start=' . $next . '&amp;order=' . $search_order . '&amp;direction=' . $search_direction . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="show_claims?start=' . $last . '&amp;order=' . $search_order . '&amp;direction=' . $search_direction . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';


        }

        return $claim_info;

    }


    function getYear($field, $table, $orderby, $db)
    {

        $sql = "SELECT $field FROM $table ORDER BY $orderby ASC LIMIT 1";
        $db->query($sql);
        $row = $db->single();

        $earliest_year = date('Y', strtotime($row[$field]));

        return $earliest_year;

    }


    function getClaimContent($claim_id, &$db, $demo = 0)
    {

        if ($demo == 1) {
            $tires_table = 'demo_tires';
            $claim_table = 'demo_claims';
        } else {
            $tires_table = 'dealer_tires';
            $claim_table = 'dealer_claims';
        }

        $sql = "SELECT * FROM $claim_table
			INNER JOIN dealer_registration
			ON " . $claim_table . ".dealer_id = dealer_registration.dealer_id
			INNER JOIN $tires_table
			ON " . $tires_table . ".claim_id = " . $claim_table . ".claim_id
			WHERE " . $claim_table . ".claim_id = :claim_id AND " . $tires_table . ".claim_id  = " . $claim_table . ".claim_id";

        $db->query($sql);

        $db->bind(':claim_id', $claim_id, PDO::PARAM_STR);

        $claim_info = $db->resultset();

        return $claim_info;

    }

    function getClaimLaborContent($claim_id, &$db, $demo = 0)
    {

        $sql = "SELECT * FROM labor_claims lc left join dealer_registration dr on (dr.dealer_id = lc.dealer_id) where lc.claim_id=:claim_id";

        $db->query($sql);
        $db->bind(':claim_id', $claim_id, PDO::PARAM_STR);
        $claim_info = $db->resultset();

        return $claim_info;

    }

    function getMakeName($db, $id)
    {
        $sql = "SELECT make_name FROM car_makes WHERE car_make_id = $id";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        if (empty($row['make_name'])) {
            $row['make_name'] = '';


        }

        return $row['make_name'];

    }

    function getMakesDropdown($db, $id = '')
    {
        $sql = "SELECT * FROM car_makes ORDER BY make_name ASC";
        $result = $db->sql_query($sql);
        $makes = '';
        while ($row = $db->sql_fetchrow($result)) {
            if ($id != $row['car_make_id']) {
                $makes .= '<option value="' . $row['car_make_id'] . '">' . $row['make_name'] . '</option>' . "\n";


            } else {
                $makes .= '<option value="' . $row['car_make_id'] . '" selected>' . $row['make_name'] . '</option>' . "\n";

            }


        }
        return $makes;

    }

    function generatePassword($length = 6)
    {

        // start with a blank password
        $password = "";

        // define possible characters - any character in this string can be
        // picked for use in the password, so if you want to put vowels back in
        // or add special characters such as exclamation marks, this is where
        // you should do it
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);

        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }

        // set up a counter for how many characters are in the password so far
        $i = 0;

        // add random characters to $password until $length is reached
        while ($i < $length) {

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

            // have we already used this character in $password?
            if (!strstr($password, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $password .= $char;
                // ... and increase the counter by one
                $i++;
            }

        }

        // done!
        return $password;

    }

    function getDealerMessage($db)
    {

        $sql = "SELECT * FROM dealer_message";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        return $row['dealer_message'];

    }

    function editDealerMessage($db, $dealer_message)
    {
        $dealer_message = mysql_real_escape_string($dealer_message);
        $sql = "UPDATE dealer_message SET dealer_message = '$dealer_message'";
        $result = $db->sql_query($sql);
    }


    // Fazal Rasel
    function getClaims($db, $type, $value)
    {
        switch ($type) {
            case 'date': {
                $sql = "SELECT claim_id, claim_date, dealer_id from dealer_claims WHERE claim_date = :claim_date";
                $db->query($sql);
                $db->bind(':claim_date', $value, PDO::PARAM_STR);
                $get_claims = $db->resultset();
                return $get_claims;
            }
            case 'dealer_phone' : {
                $value = str_replace(" ", '', $value);
                $value = str_replace("(", '', $value);
                $value = str_replace(")", '', $value);
                $value = str_replace("-", '', $value);
                $sql = 'SELECT dealer_claims.claim_id, dealer_claims.claim_date, dealer_claims.dealer_id from dealer_claims 
                            left join dealer_registration on (dealer_claims.dealer_id = dealer_registration.dealer_id) 
                            WHERE dealer_registration.business_phone = :business_phone';
                $db->query($sql);
                $db->bind(':business_phone', $value, PDO::PARAM_STR);
                $get_claims = $db->resultset();
                return $get_claims;
            }
            case 'claim_number' : {
                $sql = "SELECT claim_id, claim_date, dealer_id from dealer_claims WHERE claim_id = :claim_id";
                $db->query($sql);
                $db->bind(':claim_id', $value, PDO::PARAM_STR);
                $get_claims = $db->resultset();
                return $get_claims;
            }
        }
    }

    function getClaimsLabor($db, $type, $value)
    {
        switch ($type) {
            case 'date': {
                $sql = "SELECT claim_id, original_repair_date, dealer_id from labor_claims WHERE created_at = :created_at";
                $db->query($sql);
                $db->bind(':created_at', $value, PDO::PARAM_STR);
                $get_claims = $db->resultset();
                return $get_claims;
            }
            case 'dealer_phone' : {
                $value = str_replace(" ", '', $value);
                $value = str_replace("(", '', $value);
                $value = str_replace(")", '', $value);
                $value = str_replace("-", '', $value);
                $sql = 'SELECT labor_claims.claim_id, labor_claims.original_repair_date, labor_claims.dealer_id from labor_claims 
                            left join dealer_registration on (labor_claims.dealer_id = dealer_registration.dealer_id) 
                            WHERE dealer_registration.business_phone = :business_phone';
                $db->query($sql);
                $db->bind(':business_phone', $value, PDO::PARAM_STR);
                $get_claims = $db->resultset();
                return $get_claims;
            }
            case 'claim_number' : {
                $sql = "SELECT claim_id, original_repair_date, dealer_id from labor_claims WHERE claim_id = :claim_id";
                $db->query($sql);
                $db->bind(':claim_id', $value, PDO::PARAM_STR);
                $get_claims = $db->resultset();
                return $get_claims;
            }
        }
    }

    // End Fazal Rasel


}
