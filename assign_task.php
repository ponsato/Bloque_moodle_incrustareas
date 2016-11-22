<?php
    global $CFG, $PAGE, $DB;
    require_once('../../config.php');
    
    $url_assign_task = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    echo $url_assign_task;
?>