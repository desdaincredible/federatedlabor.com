<?php
$site = 'abs';
set_include_path(get_include_path()
    . PATH_SEPARATOR . str_replace('admin34f6', '', $_SERVER['DOCUMENT_ROOT']) . '/library'
);

require_once('db/config.php');
require_once('db/db.php');
$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
require_once('model/site_model.php');
$model = new model($db);
$term = mysql_real_escape_string(strtolower($_GET['term']));
$make_id = (empty($_GET['vmid'])) ? 0 : mysql_real_escape_string(strtolower($_GET['vmid']));

echo $model->getModels($db, $term, $make_id);
