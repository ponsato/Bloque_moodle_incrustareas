<?php
    global $CFG, $PAGE, $DB;
    require_once('../../config.php');
    
    // Obtengo datos de la url
    $url_assign_task = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    $recojo_campos_a_pintar = explode('?=', $url_assign_task);
    $itemid_archivo = $recojo_campos_a_pintar[1];
    $ruta_archivo = $recojo_campos_a_pintar[2];
    $ir_course = $recojo_campos_a_pintar[3];

    $primera_carpeta = substr($ruta_archivo, 0, 2);
    $segunda_carpeta = substr($ruta_archivo, 2, 2);
    $ruta_archivo = $CFG->dataroot.'\filedir\\'.$primera_carpeta.'\\'.$segunda_carpeta.'\\'.$ruta_archivo;

    // Preparo el archivo para ser leido
    $contenido = fopen($ruta_archivo, "r+") or exit ("No se ha podido abrir!");
    $contenido_html = file_get_contents($ruta_archivo);
    $documento = new DOMDocument();
    libxml_use_internal_errors(true);
    $documento->loadHTML($contenido_html);
    libxml_use_internal_errors(false);
    $documento -> saveHTML();
    $documentopath = new DOMXPath($documento);

    // Obtengo el número de temas del documento
    foreach ($documentopath->query('//p[@class="MsoTitle"]') as $titulos_capitulos) {
        $titulos_capituloss[] = $titulos_capitulos->nodeValue;
    }
    print_r($titulos_capituloss);

    // Obtengo el número de actividades de cada tema
    foreach ($documentopath->query('//div') as $elemento) {
        $elementos[] = $elemento;
    }
    for ($i=0; $i<=count($elementos); $i++) {
        foreach($elementos[$i] as $hijos) {
            
        }
        
    }
    


?>