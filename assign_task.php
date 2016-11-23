<?php
    global $CFG, $PAGE, $DB;
    require_once('../../config.php');
    
    $url_assign_task = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    //echo $url_assign_task;
    
    $recojo_campos_a_pintar = explode('?=', $url_assign_task);
    $itemid_archivo = $recojo_campos_a_pintar[1];
    $ruta_archivo = $recojo_campos_a_pintar[2];
    $ir_course = $recojo_campos_a_pintar[3];

    echo $itemid_archivo.'<br/>';
    echo $ruta_archivo.'<br/>';
    echo $ir_course.'<br/>';

    $primera_carpeta = substr($ruta_archivo, 0, 2);
    $segunda_carpeta = substr($ruta_archivo, 2, 2);
    $ruta_archivo = $CFG->dataroot.'\filedir\\'.$primera_carpeta.'\\'.$segunda_carpeta.'\\'.$ruta_archivo;

    $contenido = fopen($ruta_archivo, "r+") or exit ("No se ha podido abrir!");
    
    // Preparo el archivo para ser leido
    $contenido_html = file_get_contents($ruta_archivo);
    $documento = new DOMDocument();
    libxml_use_internal_errors(true);
    $documento->loadHTML($contenido_html);
    libxml_use_internal_errors(false);
    $documento -> saveHTML();
    $documentopath = new DOMXPath($documento);

    
    
    for($i=0; $i<=$documentopath->query('//p[@class="MsoTitle"]'); $i++) {
        echo 'pintate algo';
    }
    /*$tema = $documentopath->query('//p[@class="MsoTitle"]');
    print_r($tema);

    foreach($tema as $temas){
        $tema = strval($temas->nodeValue); 
    }
    echo 'aqui<br/><br/>';
    print_r($tema);

    $todo = $documentopath->query('//div[@class="WordSection1"]');
    foreach($todo as $todos){
        $todo = strval($todos->nodeValue); 
    }*/
    
    //echo $todo;


?>