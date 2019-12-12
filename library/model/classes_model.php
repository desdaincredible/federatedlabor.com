<?php

class model
{
    function getHeaderValues($db, $site, $template = 'index')
    {
        $content['meta'] = '';
        $content['script'] = '';
        $content['css'] = '';
        $content['title'] = '';
        $sql = "SELECT * FROM page_header_info WHERE (site='all' OR site='$site') AND (page='$template' OR page='all')";
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
            $content[$row['type']] .= $row['value'] . "\n";
        }
        return $content;

    }

    function checkSession($db)
    {
        $valid_student = 0;
        $now = date('Y-m-d H:i:s');
        if (isset($_SESSION['student_id'])) {
            $sql = "SELECT * FROM current_users WHERE session_id='" . session_id() . "' && dealer_id=" . $_SESSION['student_id'];
            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);
            if (!empty($row['dealer_id'])) {
                $valid_student = 1;
                $sql = "UPDATE current_users SET date='$now' WHERE session_id='" . session_id() . "' && dealer_id=" . $_SESSION['student_id'];
            }
        }
        if (empty($valid_student)) {
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
        return $valid_student;

    }

    function newSession($db, $username, $password)
    {
        $valid_student = 0;
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM course_users  WHERE user_email='$username' AND user_password='$password'";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        if (!empty($row['student_id'])) {
            $_SESSION['student_id'] = $row['student_id'];
            $sql = "INSERT INTO current_users (session_id, dealer_id, date) VALUES ('" . session_id() . "', " . $row['student_id'] . ", '$now')";
            $result = $db->sql_query($sql);
            $valid_student = 1;
            if (empty($row['user_first_name'])) {
                $valid_student = 2;

            }


        }


        /*Clean up old sessions */
        $sql = "DELETE FROM current_users WHERE date < '" . date('Y-m-d H:i:s', strtotime($now . ' -4 hours')) . "'";
        $result = $db->sql_query($sql);
        if (empty($valid_student)) {
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
        return $valid_student;
    }

    function deleteSession($db)
    {
        $sql = "DELETE FROM current_users WHERE session_id='" . session_id() . "' && dealer_id=" . $_SESSION['student_id'];
        $result = $db->sql_query($sql);

    }

    function getCourses($type, $db)
    {

        $sql = "SELECT course_id, course_title FROM  courses WHERE course_type=$type ORDER BY display_order";
        $result = $db->sql_query($sql);
        return $db->sql_fetchrowset($result);

    }

    function getCourseTitle($cid, $db)
    {

        $sql = "SELECT course_title FROM courses WHERE course_id=$cid";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        return $row['course_title'];

    }

    function getCourseInfo($cid, $db)
    {

        $sql = "SELECT * FROM  courses WHERE course_id=$cid";
        $result = $db->sql_query($sql);
        return $db->sql_fetchrow($result);

    }

    function getOnlineCourseId($course_number, $db)
    {

        $sql = "SELECT course_id FROM  courses WHERE course_number='$course_number'";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        return $row['course_id'];

    }

    function logCourse($cid, $db)
    {

        $sql = "SELECT access_date FROM course_signups WHERE course_id=$cid AND user_id = " . $_SESSION['student_id'] . " AND access_date IS NULL";
        $result = $db->sql_query($sql);
        $count = mysql_num_rows($result);
        if ($count > 0) {
            $now = date('Y-m-d H:i:s');
            $sql = "UPDATE course_signups SET access_date = '$now' WHERE course_id=$cid AND user_id = " . $_SESSION['student_id'] . " AND access_date IS NULL";
            $result = $db->sql_query($sql);

        }

    }

    function updateStudent($db, $content)
    {
        $now = date('Y-m-d');
        $criteria = '';
        $change_status = '';
        reset($content);
        while (list($k, $v) = each($content)) {
            ${strtolower($k)} = mysql_real_escape_string($v);
        }

        /* Update the student info */

        $sql = "UPDATE course_users  SET user_business_name = '$user_business_name', user_first_name = '$user_first_name', user_last_name = '$user_last_name', user_address_1 = '$user_address_1', user_address_2 = '$user_address_2', user_city = '$user_city', user_state = '$user_state', user_zip = '$user_zip', user_phone = '$user_area$user_exchange$user_digits'WHERE student_id = " . $_SESSION['student_id'];
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=13");
            break;
        } else {
            return 'success';
        }

    }

    function getClasses($db)
    {
        $sql = "SELECT * FROM course_signups INNER JOIN courses ON course_signups.course_id = courses.course_id WHERE user_id = " . $_SESSION['student_id'] . " ORDER BY course_signups.course_id";
        $result = $db->sql_query($sql);

        $classes['current_classes'] = '';
        $classes['completed_classes'] = '';

        while ($row = $db->sql_fetchrow($result)) {
            if (!empty($row['final_score'])) {
                if (empty($classes['completed_classes'])) {
                    $classes['completed_classes'] .= '<li>&nbsp;</li><li><b>Completed Classes</b></li>';
                }

                $classes['completed_classes'] .= '<li><a href="take_class?cid=' . $row['course_id'] . '">' . $row['course_title'] . '</a></li>';


            } else {
                if (empty($classes['current_classes'])) {
                    $classes['current_classes'] .= '<li><b>Current Classes</b></li>';
                }

                $classes['current_classes'] .= '<li><a href="take_class?cid=' . $row['course_id'] . '">' . $row['course_title'] . '</a></li>';

            }


        }
        return $classes;

    }

    function getClassInfo($cid, $db)
    {
        $counter = 0;
        $class_info = array();
        $sql = "SELECT * FROM course_files WHERE course_id=$cid ORDER BY section_id, display_order";

        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result)) {
            $sql_2 = "SELECT exam_id, exam_title FROM course_exams WHERE course_id=$cid AND section_id = " . $row['section_id'];
            $result_2 = $db->sql_query($sql_2);
            $row_2 = $db->sql_fetchrow($result_2);
            if (empty($row_2)) {

                $row_2['exam_id'] = '';
                $row_2['exam_title'] = '';
            }
            $row = array_merge($row, $row_2);
            $class_info[$counter] = $row;
            $counter++;
        }
        $sql_2 = "SELECT exam_id, exam_title FROM course_exams WHERE course_id=$cid AND section_id = 0";
        $result_2 = $db->sql_query($sql_2);
        $row = $db->sql_fetchrow($result_2);
        $class_info[$counter] = $row;


        return $class_info;
    }


    function getExamInfo($eid, $db)
    {
        $sql = "SELECT * FROM course_exams WHERE exam_id=$eid";

        $result = $db->sql_query($sql);
        return $db->sql_fetchrow($result);
    }

    function getExamQuestions($eid, $db)
    {
        $counter = 0;
        $exam_questions = array();
        $sql = "SELECT * FROM course_questions WHERE exam_id=$eid";

        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
            $sql_2 = "SELECT correct, answer_text FROM course_answers WHERE question_id=" . $row['question_id'];
            $result_2 = $db->sql_query($sql_2);
            while ($row_2 = $db->sql_fetchrow($result_2)) {
                $row['answers'][] = $row_2;

            }
            $exam_questions[$counter] = $row;
            $counter++;

        }
        return $exam_questions;
    }

    function examCompleted($cid, $db)
    {
        $sql = "SELECT final_score FROM course_signups WHERE course_id=$cid AND user_id=" . $_SESSION['student_id'];
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        if (!empty($row['final_score'])) {
            return 1;


        }
        return 0;

    }

    function insertScore($cid, $score, $db)
    {
        $now = date('Y-m-d G:i:s');
        $sql = "UPDATE course_signups SET final_score=$score, pass_date='$now' WHERE course_id=$cid AND user_id=" . $_SESSION['student_id'];
        $result = $db->sql_query($sql);
    }


    function calculateExamScore($eid, $student_answer, $db)
    {

        $sql = "SELECT count(question_id) AS total_questions FROM course_questions WHERE exam_id=$eid";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);

        $test_score['num_questions'] = $row['total_questions'];
        $test_score['correct_answers'] = 0;

        foreach ($student_answer as $key => $value) {

            if ($value != 'count') {
                $sql = "SELECT correct FROM course_answers WHERE question_id = $key AND answer_text='" . mysql_real_escape_string($value) . "'";
                $result = $db->sql_query($sql);
                $row = $db->sql_fetchrow($result);

                if ($row['correct'] == 1) {
                    $test_score['correct_answers'] = ($test_score['correct_answers'] + 1);
                }
            }
        }

        return $test_score;


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
            break;
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
            break;
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
            break;
        } else {
            $sql = "DELETE FROM tire_makes WHERE tire_make_id=$id";
            $result = $db->sql_query($sql);
            if (mysql_error()) {

                header("Location: error?eid=9");
                break;
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
            break;
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
            break;
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
            break;
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


        $sql = "SELECT * FROM dealer_registration
			WHERE dealer_id = $dealer_id";


        $result = $db->sql_query($sql);
        $dealer_info = $db->sql_fetchrow($result);

        return $dealer_info;

    }

    function searchDealers($search_info, $db)
    {
        $dealer_info = '';
        if (empty($search_info['start'])) {
            if ($search_info['term'] == 'view_inactive') {
                $sql = "SELECT count(dealer_id) AS dealer_count FROM dealer_registration WHERE inactive > 0 ORDER by " . $search_info['order'] . " " . $search_info['direction'];
            } else {
                if ($search_info['operator'] != 'equals') {
                    if ($search_info['operator'] == 'starts') {
                        $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                    } else {
                        $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                    }


                } else {

                    $criteria = "WHERE " . $search_info['criteria'] . " = '" . $search_info['term'] . "'";
                }

                $sql = "SELECT count(dealer_id) AS dealer_count FROM dealer_registration $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'];
            }

            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);
            $dealer_count = $row['dealer_count'];

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


        if ($search_info['term'] == 'view_inactive') {
            $sql = "SELECT * from dealer_registration WHERE inactive > 0 ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
        } else {
            if ($search_info['operator'] != 'equals') {
                if ($search_info['operator'] == 'starts') {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                } else {
                    $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                }


            }

            $sql = "SELECT * from dealer_registration $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
        }
        $result = $db->sql_query($sql);
        $i = 0;
        while ($dealer_row = $db->sql_fetchrow($result)) {
            $dealer_info .= '<form method="post" name="dealer_update_' . $i . '" action="dealer_update" class="clearBoth"><div class="dealer_id"><a href="dealer_edit?did=' . $dealer_row['dealer_id'] . '">' . $dealer_row['dealer_id'] . '</a></div><div class="contact_last_name">' . $dealer_row['contact_last_name'] . '</div><div class="business_name">' . $dealer_row['business_name'] . '</div><div class="set_status"><select size="1" name="set_status"><option>Select Status</option><option value="past_due">Past Due</option><option value="general">De-Activate</option><option value="delete">Delete</option><option value="activate">Re-Activate</option></select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1"><input type="hidden" name="dealer_id" value="' . $dealer_row['dealer_id'] . '"></div></form>';
            $i++;
        }

        if ($_SESSION['max_page'] > 1) {
            $dealer_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="show_dealers?start=1&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="show_dealers?start=' . $prev . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="show_dealers?start=' . $next . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="show_dealers?start=' . $last . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';


        }


        return $dealer_info;

    }

    function searchPlans($search_info, $db)
    {
        $plan_info = '';
        if (empty($search_info['start'])) {
            if ($search_info['term'] == 'view_inactive') {
                $sql = "SELECT count(plan_number) AS plan_count FROM plan_registration ORDER by " . $search_info['order'] . " " . $search_info['direction'];
            } else {
                if ($search_info['operator'] != 'equals') {
                    if ($search_info['operator'] == 'starts') {
                        $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

                    } else {
                        $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


                    }


                } else {

                    $criteria = "WHERE " . $search_info['criteria'] . " = '" . $search_info['term'] . "'";
                }

                $sql = "SELECT count(plan_number) AS plan_count FROM plan_registration $criteria";
            }
            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);
            $plan_count = $row['plan_count'];
            if ($plan_count % 10 == 0) {

                $_SESSION['max_page'] = $plan_count / 10;
            } else {
                $_SESSION['max_page'] = floor($plan_count / 10) + 1;
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


        if ($search_info['operator'] != 'equals') {
            if ($search_info['operator'] == 'starts') {
                $criteria = "WHERE " . $search_info['criteria'] . " LIKE '" . $search_info['term'] . "%'";

            } else {
                $criteria = "WHERE " . $search_info['criteria'] . " LIKE '%" . $search_info['term'] . "%'";


            }


        }

        $sql = "SELECT * from plan_registration INNER JOIN dealer_registration ON plan_registration.dealer_id=dealer_registration.dealer_id $criteria ORDER by " . $search_info['order'] . " " . $search_info['direction'] . " LIMIT $limit, 10";
        $result = $db->sql_query($sql);
        $i = 0;
        while ($plan_row = $db->sql_fetchrow($result)) {
            $plan_info .= '<form method="post" class="admin_plan_edit" name="admin_plan_update_' . $i . '" id="admin_plan_update_' . $i . '" action="admin_plan_edit" class="clearBoth"><div class="plan_id"><a href="admin_plan_edit?plan_number=' . $plan_row['id'] . '">' . $plan_row['plan_number'] . '</a></div><div class="customer_name">' . $plan_row['customer_first_name'] . ' ' . $plan_row['customer_last_name'] . '</div><div class="business_name">' . $plan_row['business_name'] . '</div><div class="delete_plan"><input type="submit" value="Delete Plan" name="delete_plan"><input type="hidden" name="plan_number" value="' . $plan_row['plan_number'] . '"></div></form><div class="clearBoth"></div>';
            $i++;
        }

        if ($_SESSION['max_page'] > 1) {
            $plan_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="show_plans?start=1&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="show_plans?start=' . $prev . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="show_plans?start=' . $next . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="show_plans?start=' . $last . '&amp;order=' . $search_info['order'] . '&amp;direction=' . $search_info['direction'] . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';


        }


        return $plan_info;

    }

    function deletePlan($plan_number, $db)
    {
        $regex = '/\D*/';
        preg_match($regex, $plan_number, $matches);
        $plan_id = str_replace($matches[0], '', $plan_number);


        $sql = "DELETE FROM plan_tires WHERE plan_number=$plan_id";
        $result = $db->sql_query($sql);
        if (mysql_error()) {

            header("Location: error?eid=11");
            break;
        } else {
            $sql = "DELETE FROM plan_registration  WHERE id=$plan_id";
            $result = $db->sql_query($sql);
            if (mysql_error()) {

                header("Location: error?eid=11");
                break;
            } else {
                return 'success';
            }
        }
    }

    function planUpdate($plan_info, $plan_id, $db)
    {


        /* Put current tire info into an array in case of error and needing to roll back */

        $sql = "SELECT * from plan_tires where plan_number = $plan_id";
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
            $original_tires[$row['tire_number']] = $row;
        }
        $sql = "DELETE from plan_tires where plan_number = $plan_id";
        $result = $db->sql_query($sql);

        echo mysql_error() . '<br>';
        $counter = 0;
        for ($i = 1; $i <= 6; ++$i) {
            if (!empty($plan_info['tires'][$i]['make'])) {

                if (empty($plan_info['tires'][$i]['replaced'])) {
                    $plan_info['tires'][$i]['replaced'] = 0;

                }
                $sql = "INSERT INTO plan_tires (plan_number, tire_number, make, model, size, DOT, price, replaced) values ($plan_id, $i, '" . $plan_info['tires'][$i]['make'] . "', '" . $plan_info['tires'][$i]['model'] . "','" . $plan_info['tires'][$i]['size'] . "', '" . $plan_info['tires'][$i]['DOT'] . "', " . $plan_info['tires'][$i]['price'] . ", " . $plan_info['tires'][$i]['replaced'] . ")";
                $result = $db->sql_query($sql);
                /* If error, roll back to the original values */
                if (mysql_error()) {
                    /* Delete any previous insertions */
                    $sql = "DELETE from plan_tires where plan_number = $plan_id";
                    $result = $db->sql_query($sql);

                    $tire_count = count($original_tires);
                    /* Insert the original tire info */
                    for ($i = 1; $i <= $tire_count; $i++) {
                        $sql = "INSERT INTO plan_tires (plan_number, tire_number, make, model, size, DOT, price, replaced) values ($plan_id, $i, '" . $original_tires[$i]['make'] . "', '" . $original_tires[$i]['model'] . "','" . $original_tires[$i]['size'] . "', '" . $original_tires[$i]['DOT'] . "', " . $original_tires[$i]['price'] . ", " . $original_tires[$i]['replaced'] . ")";
                        $result = $db->sql_query($sql);
                    }
                    header("Location: error?eid=12");
                    break;
                }
                $counter++;
            }

        }
        $tire_count = $counter;


        $sql = "UPDATE plan_registration SET customer_first_name = '" . $plan_info['customer_first_name'] . "', customer_last_name = '" . $plan_info['customer_last_name'] . "', customer_phone='" . $plan_info['customer_phone'] . "', invoice_number='" . $plan_info['invoice_number'] . "',vehicle_year='" . $plan_info['vehicle_year'] . "', vehicle_make ='" . $plan_info['vehicle_make'] . "', vehicle_model='" . $plan_info['vehicle_model'] . "', vehicle_mileage=" . $plan_info['vehicle_mileage'] . ", number_of_tires=$tire_count WHERE id = $plan_id";
        $result = $db->sql_query($sql);
        /* If error, roll back to the original values */
        if (mysql_error()) {
            /* Delete any previous insertions */
            $sql = "DELETE from plan_tires where plan_number = $plan_id";
            $result = $db->sql_query($sql);

            $tire_count = count($original_tires);
            /* Insert the original tire info */
            for ($i = 1; $i <= $tire_count; $i++) {
                $sql = "INSERT INTO plan_tires (plan_number, tire_number, make, model, size, DOT, price, replaced) values ($plan_id, $i, '" . $original_tires[$i]['make'] . "', '" . $original_tires[$i]['model'] . "','" . $original_tires[$i]['size'] . "', '" . $original_tires[$i]['DOT'] . "', " . $original_tires[$i]['price'] . ", " . $original_tires[$i]['replaced'] . ")";
                $result = $db->sql_query($sql);
            }
            header("Location: error?eid=12");
            break;
        } else {
            return 'success';
        }

    }


    function searchClaims($claim_date, $order, $direction, $start, $db)
    {
        $claim_info = '';

        if (empty($start)) {
            $sql = "SELECT count(id) AS claim_count from plan_claims WHERE claim_date = '$claim_date'";
            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);
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
        $sql = "SELECT *  from plan_claims WHERE claim_date = '$claim_date' ORDER by $order $direction LIMIT $limit, 10";

        $result = $db->sql_query($sql);
        while ($claim_row = $db->sql_fetchrow($result)) {

            if ($claim_row['claim_date'] == '0000-00-00 00:00:00') {
                $claim_date = '';
            } else {
                $claim_date = date('m/d/Y', strtotime($claim_row['claim_date']));
            }
            $claim_info .= '<div class="claim_id"><a href="admin_claims?cid=' . $claim_row['claim_id'] . '">' . $claim_row['claim_id'] . '</a></div><div class="claim_date">' . $claim_date . '</div><div class="dealer_id">' . $claim_row['dealer_id'] . '</div><div class="claim_total">' . $claim_row['claim_total'] . '</div>';
        }
        if ($_SESSION['max_page'] > 1) {
            $claim_info .= '<div style="text-align: right; float: left; width: 98%;"><br><a href="show_claims?start=1&amp;order=' . $order . '&amp;direction=' . $direction . '"><img border="0" src="images/dbl_left_arrow.gif" width="17" height="11"> First</a> <a href="show_claims?start=' . $prev . '&amp;order=' . $order . '&amp;direction=' . $direction . '"><img border="0" src="images/left_arrow.gif" width="11" height="11"> Prev</a>&nbsp;&nbsp;&nbsp;<a href="show_claims?start=' . $next . '&amp;order=' . $order . '&amp;direction=' . $direction . '">Next <img border="0" src="images/right_arrow.gif" width="11" height="11"></a> <a href="show_claims?start=' . $last . '&amp;order=' . $order . '&amp;direction=' . $direction . '">Last <img border="0" src="images/dbl_right_arrow.gif" width="17" height="11"></a></div>';


        }

        return $claim_info;

    }

    function downloadPlans($plan_date, $db)
    {
        $plan_info = '';
        $text_header = "\"plan_number\"\t\"id\"\t\"site\"\t\"plan_date\"\t\"customer_first_name\"\t\"customer_last_name\"\t\"customer_phone\"\t\"invoice_number\"\t\"number_of_tires\"\t\"tire1_make\"\t\"tire1_model\"\t\"tire1_size\"\t\"tire1_DOT\"\t\"tire1_price\"\t\"tire2_make\"\t\"tire2_model\"\t\"tire2_size\"\t\"tire2_DOT\"\t\"tire2_price\"\t\"tire3_make\"\t\"tire3_model\"\t\"tire3_size\"\t\"tire3_DOT\"\t\"tire3_price\"\t\"tire4_make\"\t\"tire4_model\"\t\"tire4_size\"\t\"tire4_DOT\"\t\"tire4_price\"\t\"tire5_make\"\t\"tire5_model\"\t\"tire5_size\"\t\"tire5_DOT\"\t\"tire5_price\"\t\"tire6_make\"\t\"tire6_model\"\t\"tire6_size\"\t\"tire6_DOT\"\t\"tire6_price\"\t\"vehicle_year\"\t\"vehicle_make\"\t\"vehicle_model\"\t\"vehicle_mileage\"\t\"date\"\t\"tire_total\"\t\"dealer_id\"\t\"dealer_name\"\t\"dealer_phone\"\n";

        $sql = "SELECT * FROM plan_registration
			INNER JOIN dealer_registration
			ON plan_registration.dealer_id = dealer_registration.dealer_id
			WHERE plan_date = '$plan_date'";

        $result = $db->sql_query($sql);


        while ($plan_row = $db->sql_fetchrow($result)) {


            $plan_info .= '"' . $plan_row['id'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['plan_number'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['site'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['plan_date'] . '"' . "\t";
            $plan_info .= '"' . str_replace('"', '""', $plan_row['customer_first_name']) . '"' . "\t";
            $plan_info .= '"' . str_replace('"', '""', $plan_row['customer_last_name']) . '"' . "\t";
            $plan_info .= '"' . $plan_row['customer_phone'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['invoice_number'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['number_of_tires'] . '"' . "\t";

            $sql2 = "SELECT * FROM plan_tires WHERE plan_number=" . $plan_row['id'] . " ORDER BY tire_number";
            $result2 = $db->sql_query($sql2);
            $tire_price = 0;
            $counter = 1;
            while ($tire_row = $db->sql_fetchrow($result2)) {
                $plan_info .= '"' . str_replace('"', '""', $tire_row['make']) . '"' . "\t";
                $plan_info .= '"' . str_replace('"', '""', $tire_row['model']) . '"' . "\t";
                $plan_info .= '"' . $tire_row['size'] . '"' . "\t";
                $plan_info .= '"' . $tire_row['DOT'] . '"' . "\t";
                $plan_info .= '"' . $tire_row['price'] . '"' . "\t";
                $tire_price = $tire_price + $tire_row['price'];
                $counter++;
            }

            for ($i = $counter; $i <= 6; ++$i) {
                $plan_info .= '""' . "\t" . '""' . "\t" . '""' . "\t" . '""' . "\t" . '""' . "\t";

            }

            $plan_info .= '"' . str_replace('"', '""', $plan_row['vehicle_year']) . '"' . "\t";
            $plan_info .= '"' . str_replace('"', '""', $plan_row['vehicle_make']) . '"' . "\t";
            $plan_info .= '"' . str_replace('"', '""', $plan_row['vehicle_model']) . '"' . "\t";
            $plan_info .= '"' . $plan_row['vehicle_mileage'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['plan_date'] . '"' . "\t";
            $plan_info .= '"' . $tire_price . '"' . "\t";
            $plan_info .= '"' . $plan_row['dealer_id'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['business_name'] . '"' . "\t";
            $plan_info .= '"' . $plan_row['business_phone'] . '"' . "\n";
        }


        $name = '';

        if (!empty($plan_info)) {
            $plan_info = $text_header . $plan_info;

            $name = 'sales_' . date('m-d-Y', strtotime($plan_date)) . '.txt';
            $filename = 'downloads/' . $name;


            if (!$handle = fopen($filename, 'w+')) {
                echo "Cannot open file ($filename)";
                exit;
            }


            if (fwrite($handle, $plan_info) === FALSE) {
                echo "Cannot write to file ($filename)<br>";
                exit;
            }

            fclose($handle);
        }

        return $name;

    }

    function getYear($field, $table, $orderby, $db)
    {

        $sql = "SELECT $field FROM $table ORDER BY $orderby ASC LIMIT 1";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $earliest_year = date('Y', strtotime($row[$field]));
        return $earliest_year;
    }

    function getPlanContent($plan_id, $db, $demo = 0)
    {
        if ($demo == 1) {
            $plan_table = 'plan_registration';
            $tires_table = 'plan_tires';
        } else {
            $plan_table = 'plan_registration';
            $tires_table = 'plan_tires';

        }
        //$plan_table = 'plan_registration';
        //$tires_table = 'plan_tires';


        $sql = "SELECT * FROM $plan_table INNER JOIN dealer_registration ON " . $plan_table . ".dealer_id = dealer_registration.dealer_id WHERE " . $plan_table . ".id = $plan_id";

        $result = $db->sql_query($sql);
        $plan_info = $db->sql_fetchrow($result);

        $sql = "SELECT * FROM $tires_table WHERE plan_number = $plan_id";
        $result = $db->sql_query($sql);
        while ($row2 = $db->sql_fetchrow($result)) {
            $tire[] = $row2;
        }
        $plan_info['tire'] = $tire;
        return $plan_info;

    }


    function getClaimContent($claim_id, $db)
    {

        $sql = "SELECT * FROM plan_claims
			INNER JOIN dealer_registration
			ON plan_claims.dealer_id = dealer_registration.dealer_id
			INNER JOIN plan_registration
			ON plan_claims.plan_id = plan_registration.id
			INNER JOIN plan_tires
			ON plan_tires.plan_number = plan_claims.plan_id
			WHERE plan_claims.claim_id = $claim_id AND plan_tires.tire_number = plan_claims.tire_number";


        $result = $db->sql_query($sql);
        $claim_info = $db->sql_fetchrow($result);
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
}
