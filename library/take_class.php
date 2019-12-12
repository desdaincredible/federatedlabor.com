<?php
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/classes_model.php');
$model = new model($db);
$valid_student = $model->checkSession($db);

if (empty($valid_student)) {
    $clear_session = $model->deleteSession($db);
    header('Location: student_index');

}
$cid = (!empty($_GET['cid'])) ? intval($_GET['cid']) : header('Location: student_index');

$model->logCourse($cid, $db);

$content = $model->getHeaderValues($db, $site, $page_id);

$classes = $model->getClasses($db);
$content = array_merge($content, $classes);

$content['COURSE_TITLE'] = $model->getCourseTitle($cid, $db);
$class_info = $model->getClassInfo($cid, $db);
if (!empty($class_info[0])) {

    $content['CLASS_MATERIAL'] = '';
    $sections = count($class_info);

    for ($i = 0; $i < $sections; ++$i) {

        if (empty($class_info[$i]['section_id'])) {
            if (!empty($class_info[$i]['exam_id'])) {
                $content['CLASS_MATERIAL'] .= '<p><a href="class_exam?eid=' . $class_info[$i]['exam_id'] . '"><b>Take the ' . $class_info[$i]['exam_title'] . '</a></b></p>';

            }
        } else {
            $content['CLASS_MATERIAL'] .= '<h2>Section ' . ($i + 1) . ': ' . $class_info[$i]['item_title'] . '</h2>';
            $content['CLASS_MATERIAL'] .= '<p>Download Course Material: <a href="' . $class_info[$i]['item_link'] . '" target="_blank">' . $class_info[$i]['item_title'] . '</a></p>';
            if (!empty($class_info[$i]['exam_id'])) {
                $content['CLASS_MATERIAL'] .= '<p><a href="class_exam?eid=' . $class_info[$i]['exam_id'] . '">Take the section ' . ($i + 1) . ' quiz</a></p>';
            }
            $content['CLASS_MATERIAL'] .= '<p>&nbsp;</p>';
        }

    }


}


$header = file_get_contents('templates/header.html');
$body_copy = file_get_contents('templates/' . $page_id . '.html');


$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

