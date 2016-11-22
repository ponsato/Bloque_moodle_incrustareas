<?php 
    global $CFG, $PAGE, $DB;
    require_once('../../config.php');

    $url_delete_file = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    
    $recojo_campos_a_borrar = explode('?=', $url_delete_file);
    $recojo_id_borrar = $recojo_campos_a_borrar[1];
    $recojo_ruta_borrar = $recojo_campos_a_borrar[2];
    
    // Borro el archivo
    if($DB->delete_records("files", array('itemid'=>$recojo_id_borrar))) {
        echo "<script languaje='javascript' type='text/javascript'>alert('Archivo borrado con éxito'); window.opener.location.reload(); window.close();</script>";
    } else {
        echo "<script languaje='javascript' type='text/javascript'>alert('Error al borrar el archivo'); window.opener.location.reload(); window.close();</script>";
    }
        


?>