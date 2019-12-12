<?php
session_start();
require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/classes_model.php');
$model = new model($db);


$content = $model->getHeaderValues($db, $site, $page_id);
$content['training_type'] = 'Online';
$content['online'] = 'online ';
$content['class_list'] = '';
$class_list = $model->getCourses(1, $db);
$class_count = count($class_list);
for ($i = 0; $i < $class_count; ++$i) {

    $content['class_list'] .= '<li><a href="course_info?cid=' . $class_list[$i]['course_id'] . '">' . $class_list[$i]['course_title'] . '</a></li>';
}

$header = file_get_contents('templates/header.html');


$body_copy = file_get_contents('templates/course_list.html');


$footer = file_get_contents('templates/footer.html');
$finished_page = $header . $body_copy . $footer;


foreach ($content as $key => $value) {
    $finished_page = str_replace('{' . strtoupper($key) . '}', $value, $finished_page);
}

echo $finished_page;

