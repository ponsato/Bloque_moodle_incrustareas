<?php
global $CFG, $PAGE, $DB;

require_once('../../config.php');
//print_r($DB);
//require_once($CFG->dirroot . '/comment/lib.php');
//require_once($CFG->dirroot . '/files/externallib.php');
//echo $CFG->dirroot;


$recojo_campos = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
echo $recojo_campos.'<br/>';
$recojo_campos = explode('?=', $recojo_campos);
$recojo_id = $recojo_campos[1];
$recojo_ruta = $recojo_campos[2];
echo $recojo_id.'<br/>'; 
echo '<br/>'.$recojo_ruta; 


// Obtengo la ruta del archivo
$primera_carpeta = substr($recojo_ruta, 0, 2);
$segunda_carpeta = substr($recojo_ruta, 2, 2);
echo '<br>'.$primera_carpeta;
echo '<br>'.$segunda_carpeta;

$ruta_valida = $CFG->dataroot.'\filedir\\'.$primera_carpeta.'\\'.$segunda_carpeta.'\\'.$recojo_ruta;

echo '<br>'.$ruta_valida;

//include($ruta_valida);
$contenido = fopen($ruta_valida, "r+");
$contenido_final = fgets($contenido);
//$contenido_final = fclose($contenido_final);

echo $contenido_final;





?>


