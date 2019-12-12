<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class model
{
    function getHeaderValues(&$db, $site, $template = 'index')
    {
        $content['meta'] = '';
        $content['script'] = '';
        $content['css'] = '';
        $content['title'] = '';
        $content['site_name'] = '';
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

    function getContent(&$db, $template = 'index')
    {
        $sql = "SELECT * FROM main_content WHERE page_id = :template";
        $db->query($sql);
        $db->bind(':template', $template, PDO::PARAM_STR);
        $content = $db->single();
        return $content;
    }

    function checkSession(&$db)
    {
        $valid_claim = 0;
        $now = date('Y-m-d H:i:s');
        if (isset($_SESSION['dealer_id'])) {
            $sql = "SELECT * FROM current_users WHERE session_id=:session_id && dealer_id=:dealer_id";
            $db->query($sql);
            $db->bind(':session_id', session_id(), PDO::PARAM_STR);
            $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_INT);
            $row = $db->single();

            if (!empty($row['dealer_id'])) {
                $valid_claim = 1;
                $sql = "UPDATE current_users SET date='$now' WHERE session_id=:session_id && dealer_id=:dealer_id";
                $db->query($sql);
                $db->bind(':session_id', session_id(), PDO::PARAM_STR);
                $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_INT);
                $db->execute();


            }
        }
        if (empty($valid_claim)) {
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
        return $valid_claim;

    }

    function deleteSession(&$db)
    {
        $sql = "DELETE FROM current_users WHERE session_id=:session_id && dealer_id=:dealer_id";
        $db->query($sql);
        $db->bind(':session_id', session_id(), PDO::PARAM_STR);
        $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_INT);
        $db->execute();


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

    function claimLogin(&$db, $site)
    {
        $valid_claim = 0;
        $now = date('Y-m-d H:i:s');

        $phone = $_POST['dealer_phone'];
        $phone = str_replace(" ", '', $phone);
        $phone = str_replace("(", '', $phone);
        $phone = str_replace(")", '', $phone);
        $phone = str_replace("-", '', $phone);

        $sql = "SELECT * FROM dealer_registration WHERE business_phone=:business_phone AND business_zip=:business_zip";
//        print_r($phone);
//        print_r(PHP_EOL);
//        print_r($_POST['dealer_zip']);
//        die();
        $db->query($sql);
        $db->bind(':business_phone', $phone, PDO::PARAM_STR);
        $db->bind(':business_zip', $_POST['dealer_zip'], PDO::PARAM_STR);
        $row = $db->single();

        if (!empty($row['dealer_id'])) {
            if ($row['inactive'] > 0) {
                $clear_session = $this->deleteSession($db);
                unset($_SESSION);
                header('Location: error?eid=' . $row['inactive']);
                exit;

            }
            $_SESSION['dealer_id'] = $row['dealer_id'];
            $_SESSION['demo'] = $row['demo'];
            $_SESSION['valid_claim'] = 1;

            $sql = "UPDATE dealer_registration SET last_login='$now' WHERE dealer_id=:dealer_id";
            $db->query($sql);
            $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_INT);
            $db->execute();

            $sql = "INSERT INTO current_users (session_id, dealer_id, demo, date) VALUES (:session_id, :dealer_id, :demo, :now)";
            $db->query($sql);
            $db->bind(':session_id', session_id(), PDO::PARAM_STR);
            $db->bind(':dealer_id', $row['dealer_id'], PDO::PARAM_INT);
            $db->bind(':demo', $row['demo'], PDO::PARAM_INT);
            $db->bind(':now', $now, PDO::PARAM_STR);


            $db->execute();
            $valid_claim = 1;


        }
        /*Clean up old sessions */
        $latest_date = date('Y-m-d H:i:s', strtotime($now . ' -4 hours'));
        $sql = "DELETE FROM current_users WHERE date < :latest_date";

        $db->query($sql);
        $db->bind(':latest_date', $latest_date, PDO::PARAM_INT);
        $db->execute();


        return $valid_claim;

    }

    function selfUpdateDealer(&$db)
    {
        $now = date('Y-m-d');

        foreach ($_POST as $k => $v) {
            $$k = $v;
        }


        $business_phone = str_replace(' ', '', $business_phone);
        $business_phone = str_replace('(', '', $business_phone);
        $business_phone = str_replace(')', '', $business_phone);
        $business_phone = str_replace('-', '', $business_phone);

        /* Update the dealer info */
        $sql = "UPDATE dealer_registration SET business_name=:business_name, business_address=:business_address, business_city=:business_city, business_state=:business_state, business_zip=:business_zip, business_phone=:business_phone, business_fax=:business_fax, business_email=:business_email, contact_first_name=:contact_first_name, contact_last_name=:contact_last_name, contact_title=:contact_title WHERE dealer_id=:dealer_id";

        $db->query($sql);
        $db->bind(':business_name', $business_name, PDO::PARAM_STR);
        $db->bind(':business_address', $business_address, PDO::PARAM_STR);
        $db->bind(':business_city', $business_city, PDO::PARAM_STR);
        $db->bind(':business_state', $business_state, PDO::PARAM_STR);
        $db->bind(':business_zip', $business_zip, PDO::PARAM_STR);
        $db->bind(':business_phone', $business_phone, PDO::PARAM_STR);
        $db->bind(':business_fax', $business_fax, PDO::PARAM_STR);
        $db->bind(':business_email', $business_email, PDO::PARAM_STR);
        $db->bind(':contact_first_name', $contact_first_name, PDO::PARAM_STR);
        $db->bind(':contact_last_name', $contact_last_name, PDO::PARAM_STR);
        $db->bind(':contact_title', $contact_title, PDO::PARAM_STR);
        $db->bind(':dealer_id', $dealer_id, PDO::PARAM_INT);
        $db->execute();


        return 'success';


    }

    function formatPhone($phone_number)
    {

        $formatted_phone = '(' . substr($phone_number, 0, 3) . ') ' . substr($phone_number, 3, 3) . '-' . substr($phone_number, 6);


        return $formatted_phone;


    }

    function getDealerMessage($db)
    {

        $sql = "SELECT * FROM dealer_message";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        return $row['dealer_message'];

    }

    function getMakes(&$db, $term)
    {
        $sql = "SELECT * FROM car_makes WHERE make_name LIKE '%$term%'";
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {

            if (empty($json)) {
                $json = '[{"value": "' . $row['make_name'] . '",  "make_id": "' . $row['car_make_id'] . '"}';

            } else {
                $json .= ', {"value": "' . $row['make_name'] . '",  "make_id": "' . $row['car_make_id'] . '"}';
            }


        }
        if (empty($json)) {
            $json = '[{"value": "No Suggestions Found",  "make_id": "0"}]';

        } else {
            $json .= ']';

        }
        return $json;

    }

    function getMakeName(&$db, $id)
    {
        $sql = "SELECT make_name FROM car_makes WHERE car_make_id = $id";
        $db->query($sql);
        $row = $db->single();

        if (empty($row['make_name'])) {
            $row['make_name'] = '';


        }

        return $row['make_name'];

    }

    function getMakesDropdown(&$db, $id = '')
    {
        $sql = "SELECT * FROM car_makes ORDER BY make_name ASC";

        $db->query($sql);
        $get_makes = $db->resultset();

        /* Prepare for content display */
        $make_count = count($get_makes);

        if ($make_count > 0) {
            $makes = '';
            for ($i = 0; $i < $make_count; ++$i) {

                if ($id != $get_makes[$i]['car_make_id']) {
                    $makes .= '<option value="' . $get_makes[$i]['car_make_id'] . '">' . $get_makes[$i]['make_name'] . '</option>' . "\n";
                } else {
                    $makes .= '<option value="' . $get_makes[$i]['car_make_id'] . '" selected>' . $get_makes[$i]['make_name'] . '</option>' . "\n";
                }
            }
        }

        return $makes;

    }


    function getRepairCodesDropdown(&$db)
    {
        $sql = "SELECT * FROM repair_codes";

        $db->query($sql);
        $repairCodes = $db->resultset();
        /* Prepare for content display */
        $count = count($repairCodes);
//        print_r($repairCodes);
        $repairs = '';
        if ($count > 0) {
            foreach ($repairCodes as $code) {
                $string = $code['repair_code'] . ', ' . $code['repair_type'] . ', ' . $code['component'];
                $repairs .= '<option value="' . $code['repair_code'] . '">' . $string . '</option>' . "\n";
            }
        }

        return $repairs;
    }

    function getModels(&$db, $term, $make_id)
    {
        $add_make = '';
        if (!empty($make_id)) {
            $add_make = " AND (car_make_id = :make_id OR car_make_id = 0)";

        }

        $sql = "SELECT * FROM car_models WHERE model_name LIKE :term$add_make";

        $db->query($sql);
        $db->bind(':term', "%$term%", PDO::PARAM_STR);

        if (!empty($add_make)) {
            $db->bind(':make_id', $make_id, PDO::PARAM_INT);
        }
        $get_models = $db->resultset();


        $model_count = count($get_models);

        if ($model_count > 0) {
            $json = '';
            for ($i = 0; $i < $model_count; ++$i) {
                if (empty($json)) {
                    $json = '[{"value": "' . $get_models[$i]['model_name'] . '",  "make_id": "' . $get_models[$i]['car_make_id'] . '"}';


                } else {
                    $json .= ', {"value": "' . $get_models[$i]['model_name'] . '",  "make_id": "' . $get_models[$i]['car_make_id'] . '"}';
                }

            }
        }

        if (empty($json)) {
            $json = '[{"value": "No Suggestions Found",  "make_id": "0"}]';


        } else {
            $json .= ']';

        }
        return $json;

    }

    function yearDropdown()
    {
        $currently_selected = date('Y'); 
        $earliest_year = 1900; 
        $latest_year = date('Y'); 
    
        foreach ( range( $latest_year, $earliest_year ) as $i ) {
          $json = '[{"value"'.$i.'"'.($i === $currently_selected ? ' selected="selected"' : '').'>'.$i.']';
        }
        return $json;
    }

    function datePicker() {
        $( "#datepicker" ).datepicker();
    }

    function getTireMakes(&$db, $term)
    {
        $sql = "SELECT * FROM tire_makes WHERE make_name LIKE :term";


        $db->query($sql);
        $db->bind(':term', "$term%", PDO::PARAM_STR);

        $get_makes = $db->resultset();
        $make_count = count($get_makes);


        if ($make_count > 0) {
            $json = '';
            for ($i = 0; $i < $make_count; ++$i) {
                if (empty($json)) {
                    $json = '[{"value": "' . $get_makes[$i]['make_name'] . '",  "make_id": "' . $get_makes[$i]['tire_make_id'] . '"}';

                } else {
                    $json .= ', {"value": "' . $get_makes[$i]['make_name'] . '",  "make_id": "' . $get_makes[$i]['tire_make_id'] . '"}';
                }
            }


        }
        if (empty($json)) {
            $json = '[{"value": "No Suggestions Found",  "make_id": "0"}]';

        } else {
            $json .= ']';

        }
        return $json;

    }


    function getTireMakeId(&$db, $term)
    {
        $make_id = '';
        $sql = "SELECT tire_make_id FROM tire_makes WHERE make_name = :term";

        $db->query($sql);
        $db->bind(':term', $term, PDO::PARAM_STR);
        $row = $db->single();
        $make_id = $row['tire_make_id'];

        return $make_id;

    }

    function getTireModels(&$db, $term, $make_id)
    {
        $add_make = '';
        if (!empty($make_id)) {
            $add_make = " AND (tire_make_id = :make_id OR tire_make_id = 0)";

        }

        $sql = "SELECT * FROM tire_models WHERE model_name LIKE :term$add_make";

        $db->query($sql);
        $db->bind(':term', "%$term%", PDO::PARAM_STR);

        if (!empty($add_make)) {
            $db->bind(':make_id', $make_id, PDO::PARAM_INT);
        }
        $get_models = $db->resultset();


        $model_count = count($get_models);

        if ($model_count > 0) {
            $json = '';
            for ($i = 0; $i < $model_count; ++$i) {
                if (empty($json)) {
                    $json = '[{"value": "' . $get_models[$i]['model_name'] . '",  "make_id": "' . $get_models[$i]['car_make_id'] . '"}';


                } else {
                    $json .= ', {"value": "' . $get_models[$i]['model_name'] . '",  "make_id": "' . $get_models[$i]['car_make_id'] . '"}';
                }

            }
        }

        if (empty($json)) {
            $json = '[{"value": "No Suggestions Found",  "make_id": "0"}]';


        } else {
            $json .= ']';

        }
        return $json;

    }

    function getClaimContent(&$db, $claim_id, $demo)
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

    function getTireInfo($plan_id, $tire_dot, $demo, $site, &$db)
    {

        if ($demo == 1) {
            $plan_table = 'demo_registration';
            $tires_table = 'demo_tires';
        } else {
            $plan_table = 'plan_registration';
            $tires_table = 'plan_tires';

        }


        $plan_id = preg_replace('/\D/', '', $plan_id);
        /* Check for expired plan */

        $now = date('Y-m-d');
        $sql = "SELECT plan_date FROM $plan_table WHERE id=$plan_id";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);


        $plan_expire = date('Y-m-d', strtotime($row['plan_date'] . "+3 years"));

        if ($site == 'mtp') {
            $plan_expire = date('Y-m-d', strtotime($row['plan_date'] . "+4 years"));
        }


        if ($now > $plan_expire) {
            $tire_info = '';
            return $tire_info;

        }


        $sql = "SELECT * FROM $tires_table WHERE plan_number = $plan_id AND replaced=0 AND DOT='$tire_dot' ORDER BY tire_number LIMIT 1";
        $result = $db->sql_query($sql);
        $tire_info = $db->sql_fetchrow($result);
        return $tire_info;

    }

    function getDealers(&$db, $site = '')
    {
        $sql = "SELECT dealer_id, business_name FROM dealer_registration WHERE site='$site' ORDER BY business_name ASC";
        $result = $db->sql_query($sql);
        $dealers = '';
        while ($row = $db->sql_fetchrow($result)) {
            $dealers .= '<option value="' . $row['dealer_id'] . '">' . $row['business_name'] . '</option>' . "\n";
        }
        return $dealers;

    }

    function getDealerInfo(&$db, $dealer_id)
    {
        $sql = "SELECT * FROM dealer_registration WHERE dealer_id = :dealer_id";
        $db->query($sql);
        $db->bind(':dealer_id', $dealer_id, PDO::PARAM_STR);
        $content = $db->single();
        return $content;


    }

    function insertClaim($content, $site, $demo, &$db)
    {

        if ($demo == 1) {
            $tires_table = 'demo_tires';
            $claim_table = 'demo_claims';
        } else {
            $tires_table = 'dealer_tires';
            $claim_table = 'dealer_claims';
        }
        reset($content);
        while (list($k, $v) = each($content)) {
            ${strtolower($k)} = $v;
        }

        $now = date('Y-m-d H:i:s');

        /* Insert info into the claim table */

        $sql = "INSERT INTO $claim_table (dealer_id, invoice_id, invoice_date, claim_date, vehicle_year, vehicle_make, vehicle_model, original_vehicle_mileage, claim_vehicle_mileage, claim_filer) VALUES (:dealer_id, :invoice_number, :invoice_date, :claim_date, :vehicle_year, :vehicle_make, :vehicle_model, :orig_mileage, :current_mileage, :claim_filer)";

        $db->query($sql);

        $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_INT);
        $db->bind(':invoice_number', $invoice_number, PDO::PARAM_STR);
        $db->bind(':invoice_date', date('Y-m-d H:i:s', strtotime($invoice_date)), PDO::PARAM_STR);
        $db->bind(':claim_date', $now, PDO::PARAM_STR);
        $db->bind(':vehicle_year', $vehicle_year, PDO::PARAM_INT);
        $db->bind(':vehicle_make', $vehicle_make, PDO::PARAM_STR);
        $db->bind(':vehicle_model', $vehicle_model, PDO::PARAM_STR);
        $db->bind(':orig_mileage', $orig_mileage, PDO::PARAM_INT);
        $db->bind(':current_mileage', $current_mileage, PDO::PARAM_INT);
        $db->bind(':claim_filer', $claim_filer, PDO::PARAM_STR);
        $db->execute();


        $claim_id = $db->lastInsertId();

        echo $claim_id;

        $first_tire = $this->insertTire($db, $tires_table, $claim_id, $tire_make_1, $tire_model_1, $tire_size_1, $tire_dot_1, $original_part_number_1, $claim_part_number_1, $original_tire_price_1, $claim_tire_price_1, $original_tread_depth_1, $remaining_tread_depth_1, $tire_damage_desc_1);
        //echo "$tires_table, $claim_id, $tire_make_1, $tire_model_1, $tire_size_1, $tire_dot_1, $original_tire_price_1, $claim_tire_price_1, $original_tread_depth_1, $remaining_tread_depth_1, $tire_damage_desc_1";
        //exit;
        if (!empty($tire_make_2)) {
            $second_tire = $this->insertTire($db, $tires_table, $claim_id, $tire_make_2, $tire_model_2, $tire_size_2, $tire_dot_2, $original_part_number_2, $claim_part_number_2, $original_tire_price_2, $claim_tire_price_2, $original_tread_depth_2, $remaining_tread_depth_2, $tire_damage_desc_2);
        }

        return $claim_id;
    }

    function insertTire(&$db, $tires_table, $claim_id, $tire_make, $tire_model, $tire_size, $tire_dot, $original_part_number, $replace_part_number, $original_tire_price, $claim_tire_price, $original_tread_depth, $remaining_tread_depth, $tire_damage_desc)
    {

        $sql = "INSERT INTO $tires_table (claim_id ,make, model, size, DOT, original_part_number, claim_part_number, original_tire_price, claim_tire_price,  original_tread_depth, remaining_tread_depth, damage_desc) VALUES (:claim_id, :tire_make, :tire_model, :tire_size, :tire_dot, :original_part_number, :replace_part_number, :original_tire_price, :claim_tire_price, :original_tread_depth, :remaining_tread_depth, :tire_damage_desc)";
        //echo "INSERT INTO $tires_table (claim_id ,make, model, size, DOT, original_tread_depth, remaining_tread_depth, damage_desc) VALUES ($claim_id, $tire_make, $tire_model, $tire_size, $tire_dot, $original_tread_depth, $remaining_tread_depth, $tire_damage_desc)";
        $db->query($sql);

        $db->bind(':claim_id', $claim_id, PDO::PARAM_INT);
        $db->bind(':tire_make', $tire_make, PDO::PARAM_STR);
        $db->bind(':tire_model', $tire_model, PDO::PARAM_STR);
        $db->bind(':tire_size', $tire_size, PDO::PARAM_STR);
        $db->bind(':tire_dot', $tire_dot, PDO::PARAM_STR);
        $db->bind(':original_part_number', $original_part_number, PDO::PARAM_STR);
        $db->bind(':replace_part_number', $replace_part_number, PDO::PARAM_STR);
        $db->bind(':original_tire_price', $original_tire_price, PDO::PARAM_STR);
        $db->bind(':claim_tire_price', $claim_tire_price, PDO::PARAM_STR);
        $db->bind(':original_tread_depth', $original_tread_depth, PDO::PARAM_INT);
        $db->bind(':remaining_tread_depth', $remaining_tread_depth, PDO::PARAM_INT);
        $db->bind(':tire_damage_desc', $tire_damage_desc, PDO::PARAM_STR);
        $db->execute();


        $tire_id = $db->lastInsertId();
        return $tire_id;


    }

    function insertUploadName($db, $field, $claim_id, $demo, $filename)
    {

        if ($demo == 1) {
            $claim_table = 'demo_claims';
        } else {
            $claim_table = 'dealer_claims';
        }

        $sql = "UPDATE $claim_table SET $field = :filename WHERE claim_id = :claim_id";
        $db->query($sql);

        $db->bind(':claim_id', $claim_id, PDO::PARAM_INT);
        $db->bind(':filename', $filename, PDO::PARAM_STR);
        $db->execute();

        return 1;
    }

    function formatPhoneNumber($phone)
    {

        if (strlen($phone) == 7) {
            $exchange = substr($phone, 0, 3);
            $number = substr($phone, 3, 4);

            $phone = $exchange . '-' . $number;

        } elseif (strlen($phone) == 10) {

            $area_code = substr($phone, 0, 3);
            $exchange = substr($phone, 3, 3);
            $number = substr($phone, 6, 4);
            $phone = '(' . $area_code . ') ' . $exchange . '-' . $number;


        } else {
            /* Do nothing*/

        }

        return $phone;

    }

    function getClaimById($db, $id)
    {
        $sql = "select * from labor_claims lc left join car_makes cm on (cm.car_make_id = lc.vehicle_make) left join repair_codes rc on (rc.repair_code = lc.repair_code) where lc.claim_id=:claim_id and lc.dealer_id=:dealer_id";
        $db->query($sql);

        $db->bind(':claim_id', $id, PDO::PARAM_INT);
        $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_STR);
        $content = $db->single();
        return $content;
    }

    function saveAndGetId($db)
    {

        $sql = "INSERT INTO labor_claims (dealer_id,invoice_number,original_repair_date,sub_invoice_number,sub_repair_date,original_repair_mileage,current_mileage,customer_first_name,customer_last_name,customer_phone,customer_email,vehicle_year,vehicle_make,vehicle_model,repair_code,original_labor_price,labor_price,labor_hour,sub_labor_price,repair_description,created_at ) 
                                  VALUES (:dealer_id,:invoice_number,:original_repair_date,:sub_invoice_number,:sub_repair_date,:original_repair_mileage,:current_mileage,:customer_first_name,:customer_last_name,:customer_phone,:customer_email,:vehicle_year,:vehicle_make,:vehicle_model,:repair_code,:original_labor_price,:labor_price,:labor_hour,:sub_labor_price,:repair_description,:created_at)";
        $db->query($sql);
        $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_STR);
        $db->bind(':invoice_number', $_POST['invoice_number'], PDO::PARAM_STR);
        $db->bind(':original_repair_date', $_POST['original_repair_date'], PDO::PARAM_STR);
        $db->bind(':sub_invoice_number', $_POST['sub_invoice_number'], PDO::PARAM_STR);
        $db->bind(':sub_repair_date', $_POST['sub_repair_date'], PDO::PARAM_STR);
        $db->bind(':original_repair_mileage', $_POST['original_repair_mileage'], PDO::PARAM_STR);
        $db->bind(':current_mileage', $_POST['current_mileage'], PDO::PARAM_STR);
        $db->bind(':customer_first_name', $_POST['customer_first_name'], PDO::PARAM_STR);
        $db->bind(':customer_last_name', $_POST['customer_last_name'], PDO::PARAM_STR);
        $db->bind(':customer_phone', $_POST['customer_phone'], PDO::PARAM_INT);
        $db->bind(':customer_email', $_POST['customer_email'], PDO::PARAM_INT);

        $db->bind(':vehicle_year', $_POST['vehicle_year'], PDO::PARAM_STR);
        $db->bind(':vehicle_make', $_POST['vehicle_make'], PDO::PARAM_STR);
        $db->bind(':vehicle_model', $_POST['vehicle_model'], PDO::PARAM_STR);
        $db->bind(':repair_code', $_POST['repair_code'], PDO::PARAM_STR);
        $db->bind(':original_labor_price', $_POST['original_labor_price'], PDO::PARAM_STR);
        $db->bind(':labor_price', $_POST['labor_price'], PDO::PARAM_STR);
        $db->bind(':labor_hour', $_POST['labor_hour'], PDO::PARAM_STR);
        $db->bind(':sub_labor_price', $_POST['sub_labor_price'], PDO::PARAM_STR);
        $db->bind(':repair_description', $_POST['repair_description'], PDO::PARAM_STR);
        $db->bind(':created_at',date('Y-m-d'), PDO::PARAM_STR);
        $db->execute();

        $id = $db->lastInsertId();

        $files = $_FILES;
        $origInvFileName = $files['orig_inv_filename']['name'];
        $origInvFileNameExt = pathinfo($origInvFileName, PATHINFO_EXTENSION);
        $origInvFileNameSaveLocation = "invoices/" . $_POST['original_repair_date'] . "_labor_" . $id . "." . $origInvFileNameExt;
        move_uploaded_file($files['orig_inv_filename']['tmp_name'], $origInvFileNameSaveLocation);

        $claimInvFileName = $files['claim_inv_filename']['name'];
        $claimInvFileNameExt = pathinfo($claimInvFileName, PATHINFO_EXTENSION);
        $claimInvFileNameSaveLocation = "invoices/" . $_POST['original_repair_date'] . "_labor_" . $id . "." . $claimInvFileNameExt;
        move_uploaded_file($files['claim_inv_filename']['tmp_name'], $claimInvFileNameSaveLocation);
        // now the file uploads
        $sql = "UPDATE labor_claims SET orig_inv_filename=:orig_inv_filename, claim_inv_filename=:claim_inv_filename where claim_id=:claim_id";
        $db->query($sql);
        $db->bind(':orig_inv_filename', $origInvFileNameSaveLocation, PDO::PARAM_STR);
        $db->bind(':claim_inv_filename', $claimInvFileNameSaveLocation, PDO::PARAM_STR);
        $db->bind(':claim_id', $id, PDO::PARAM_INT);
        $db->execute();

//        $email = new PHPMailer(TRUE);
//        $email->setFrom('donotreply@ntwclaims.net', 'NTW Website');
//        $email->addAddress('mary@maxms.com', 'Marry');
//        $email->addCC('fazalrasel@gmail.com', 'Fazal Rasel');
//
//
//        $email->Subject = 'New NTW claim';
//        $email->isHTML(TRUE);
//        $email->Body = "<h1>Hello Test</h1>";
//        $email->AltBody = "Hello Test";
//
////        $email->addAttachment($claim_content[0]['orig_inv_filename'], str_replace('invoices/', '', $claim_content[0]['orig_inv_filename']));
////        $email->addAttachment($claim_content[0]['claim_inv_filename'], str_replace('invoices/', '', $claim_content[0]['claim_inv_filename']));
//        $email->send();
        return $id;
    }

    function getAllClaims($db)
    {
        $sql = "SELECT * FROM labor_claims where dealer_id=:dealer_id ORDER BY claim_id desc";
        $db->query($sql);
        $db->bind(':dealer_id', $_SESSION['dealer_id'], PDO::PARAM_STR);
        $claims = $db->resultset();
        return $claims;
    }
}
