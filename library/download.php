<?php
$filename = stripslashes($_GET['download_file']);
$line = file_get_contents('downloads/' . $filename);
header('Content-type: text/plain');
header('Content-disposition: attachment; filename=' . $filename);
print($line);
