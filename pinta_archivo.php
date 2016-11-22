<?php
    global $CFG, $PAGE, $DB;
    require_once('../../config.php');

// Obtengo datos de la url
    $recojo_campos = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    $recojo_campos = explode('?=', $recojo_campos);
    $recojo_id = $recojo_campos[1];
    $recojo_ruta = $recojo_campos[2];

// Obtengo la ruta del archivo
    $primera_carpeta = substr($recojo_ruta, 0, 2);
    $segunda_carpeta = substr($recojo_ruta, 2, 2);
    $ruta_valida = $CFG->dataroot.'\filedir\\'.$primera_carpeta.'\\'.$segunda_carpeta.'\\'.$recojo_ruta;
    //echo '<br>'.$ruta_valida.'<br/>';

// Pinto el contenido del archivo    
    $contenido = fopen($ruta_valida, "r+") or exit ("No se ha podido abrir!");
    header('Content-Type: text/html; charset=iso-8859-1');
    include($ruta_valida);
    fclose($contenido);

?>


