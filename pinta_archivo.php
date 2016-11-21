<?php

$recojo_id = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
$recojo_id = explode('?=', $recojo_id);
$recojo_id = $recojo_id[1];
echo $recojo_id;



$context = context_module::instance($recojo_id);

$fileinfo = array (
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $recojo_id,
    'contextid' => $context->id,
    'filepath' => '/',
    'filename' => 'actividades.txt');

$fs = get_file_storage();
$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], 
        $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
echo $file;






?>


