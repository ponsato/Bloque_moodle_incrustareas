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
    //print_r($titulos_capituloss);

    // Obtengo cada uno de los elementos
    foreach ($documentopath->query('//div') as $elemento) {
        $elementos[] = $elemento;
    }
    //print_r($elementos[1]->nodeValue);
    
    // Distingo los títulos de unidad
    for ($i=1; $i<count($elementos); $i++) {
        if(strpos($elementos[$i]->nodeValue, 'Unidad de aprendizaje')) {
            echo '<h2>'.$elementos[$i]->nodeValue.'</h2>';
        } else {
            foreach ($elementos[$i]->getElementsbyTagname('p') as $parrafo) {
                $parrafos[] = $parrafo->nodeValue;
            }
            for($j=0; $j<count($parrafos); $j++) {
                if ($j==0) {
                    echo '<strong>'.$parrafos[$j].'</strong></br>';
                } else {
                    echo $parrafos[$j].'</br>';        
                }
                
            }
            echo '<br/>';
            $parrafos = array();
        }
       
    }

    // Obtengo sus párrafos para distinguir actividad de cambio de tema y poder asociar cada actividad a su tema
    /*for ($i=0; $i<=count($elementos); $i++) {
        foreach($elementos[$i] as $hijo) {
            $hijos[$i] = $hijo->query('//p');
            echo 'd';
        }
    }
    print_r($hijos);*/
    


?>