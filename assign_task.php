<?php
    global $CFG, $PAGE, $DB;
    require_once('../../config.php');
    
    // Obtengo datos de la url
    $url_assign_task = "http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
    $recojo_campos_a_pintar = explode('?=', $url_assign_task);
    $itemid_archivo = $recojo_campos_a_pintar[1];
    $ruta_archivo = $recojo_campos_a_pintar[2];
    $id_course = $recojo_campos_a_pintar[3];

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
    // ESTE CÓDIGO FUNCIONA PARA SEPARAR Y PINTAR ACTIVIDADES. Guardo como seguridad, modifico para que escriba en base de datos
    /*for ($i=1; $i<count($elementos); $i++) {
        if(strpos($elementos[$i]->nodeValue, 'Unidad de aprendizaje')) {
            echo '<h2>'.$elementos[$i]->nodeValue.'</h2>';
        } else {
            // Obtengo los párrafos pertenecientes a cada unidad
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
            // Inicializo párrafos para que pase de tema
            $parrafos = array();
        }
       
    }*/


/****************************** DB *********************************************/
    // VARIABLES con los valores necesarios para agregar tareas en cada tabla

    // MDL_COURSE_MODULES -> columnas
        //id = $id_course_modules -> obtengo el último agregada para incrementarlo en 1
            $ultimo_id_course_modules = 'SELECT MAX(id) AS id FROM mdl_course_modules';
            $recojo_id_course = $DB->get_record_sql($ultimo_id_course_modules);
            $id_course_modules = $recojo_id_course->id;
            $id_course_modules++;
        // course = $id_course
            $id_course = $id_course;
        // module = $module_type -> el típo de recurso a insertar. 1 tarea, 5 actividad (foro). Condición para distinguir entre actividad o tarea que asigna el valor a esta variable
            $module_tipe = 0;
        // instance -> equivale a la id de la tabla mdl_assign donde se añaden los enunciados, por lo que obtengo el último id para agregarlo
            $ultima_instance = 'SELECT MAX(id) AS id FROM mdl_assign';
            $recojo_instance = $DB->get_record_sql($ultima_instance);
            $instance = $recojo_instance->id;
            $instance++;
        // section -> el número del tema donde añadir el recurso. Se incrementa automáticamente tantas veces como temas hay.
            $numero_capitulo = 0;
        // idnumber -> no es obligatorio, por lo que será NULL o vacío
            $idnumber_course_modules = '';
        // added -> fecha de inclusión. Se pondrá la misma que la del fichero subido
            $fecha_de_archivo = "SELECT * from mdl_files where id<(SELECT max(id) from mdl_files) and filename like '%actividades.htm%' ORDER BY id DESC";
            $fecha_creacion_archivo = $DB->get_record_sql($fecha_de_archivo);
            $fecha_course_module = $fecha_creacion_archivo->timecreated;
        // score -> puntuación. Será siempre cero hasta recibir calificación
            $score_course_module = 0;
        // indent -> si se ha indentado hacia la izquierda
            $indent_course_module = 0;
        // visible y visibleold -> se muestra o está oculto
            $visible_course_module = 1;
            $visible_old_course_module = 1;
        // groupmode y groupingid -> grupos permitidos
            $groupmode_course_module = 0;
            $groupingid_course_module = 0;
        // completion, completiongradeitemnumber, completionview y completionexpected -> Finalización
            $completion_course_module = 0;
            $completiongradeitemnumber = 'NULL';
            $completionview_course_module = 0;
            $completionexpected_course_module = 0;
        // showdescription -> muestra descripción
            $showdescription_course_module = 0;
        // availability -> disponibilidad
            $availability_course_moudle = 'NULL';

    // Sentencias para escribir en MDL_COURSE_MODULES
    $tabla_mdl_course_modules = "INSERT into mdl_course_modules
        (id, course, module, instance, section, idnumber, added, score, indent, visible, visibleold, groupmode, groupingid, completion, completiongradeitemnumber, completionview, completionexpected, showdescription, availability) VALUES ($id_course_modules, $id_course, $module_tipe, $instance, $numero_capitulo, $idnumber_course_modules, $fecha_course_module, $score_course_module, $indent_course_module, $visible_course_module, $visible_old_course_module, $groupmode_course_module, $groupingid_course_module, $completion_course_module, $completiongradeitemnumber, $completionview_course_module, $completionexpected_course_module, $showdescription_course_module, $availability_course_moudle)";


    // MDL_ASSIGN -> columnas
        // id -> se ha recogido anteriormente mediante la variable $instance de mdl_course_modules
            $id_assign = $instance;
        // course -> id del curso
            $id_course = $id_course;
        // name -> nombre del recurso. Se agrega automáticamente a la fila correspondiente
            $name_assign = '';
        // intro -> enunciado del recurso. Se agrega automáticamente a la fila correspondiente. Añadir coletilla.
            $intro_assign = '';
        // introformat -> formato de introducción
            $introformat_assign = 1;
        // alwaysshowdescription -> muestra descripción
            $alwaysshowdescription_assign = 1;
        // nosubmissions, submissiondrafts
            $nosubmissions_assign = 0;
            $submissiondrafts_assign = 0;
        // sendnotifications y sendlatenotifications -> enviar notificaciones al tutor
            $sendnotifications_assign = 1;
            $sendlatenotifications_assign = 1;
        // duedate
            $duedate_assign = 0;
        // allowsubmissionsfromdate
            $allowsubmissionsfromdate_assign = 0;
        // grade
            $grade_assign = 100;
        // timemodified
            $timemodified_assign = $fecha_course_module;
        // requiresubmissionstatement
            $requiresubmissionstatement_assing = 0;
        // completionsubmit
            $completionsubmit_assign = 0;
        // curoffdate
            $cutoffdate_assign = 0;
        // teamsubmission
            $teamsubmission_assign = 0;
        // requireallteammemberssubmit
            $requireallteammemberssubmit_assign = 0;
        // teamsubmissiongroupingid
            $teamsubmissiongroupingid_assign = 0;
        // blindmarking
            $blindmarking_assign = 0;
        // revealidentities
            $revealidentities_assign = 0;
        // attemptreopenmethod
            $attemptreopenmethod_assign = 'none';
        // maxattempts
            $maxattempts_assign = -1;
        // markingworkflow
            $markingworkflow_assign = 0;
            $markingallocation_assign = 0;
        // sendstudentnotifications
            $sendstudentnotifications_assign = 1;

    // Sentencias para escribir en MDL_ASSIGN
    $tabla_mdl_assign = "INSERT into mdl_assign 
        (id, course, name, intro, introformat, alwaysshowdescription, nosubmissions, submissiondrafts, sendnotifications, sendlatenotifications, duedate, allowsubmissionsfromdate, grade, timemodified, requiresubmissionstatement, completionsubmit, cutoffdate, teamsubmission, requireallteammemberssubmit, teamsubmissiongroupingid, blindmarking, revealidentities, attemptreopenmethod, maxattempts, markingworkflow, markingallocation, sendstudentnotifications) VALUES ($id_assign, $id_course, $name_assign, $intro_assign, $introformat_assign, $alwaysshowdescription_assign, $nosubmissions_assign, $submissiondrafts_assign, $sendnotifications_assign, $sendlatenotifications_assign, $duedate_assign, $allowsubmissionsfromdate_assign, $grade_assign, $timemodified_assign, $requiresubmissionstatement_assing, $completionsubmit_assign, $cutoffdate_assign, $teamsubmission_assign, $requireallteammemberssubmit_assign, $teamsubmissiongroupingid_assign, $blindmarking_assign, $revealidentities_assign, $attemptreopenmethod_assign, $maxattempts_assign, $markingworkflow_assign, $markingallocation_assign, $sendstudentnotifications_assign)";


    // MDL_GRADE_ITEMS -> columnas
        // id -> $id_grade_items obtengo el último agregada para incrementarlo en 1
            $ultimo_id_grade_items = 'SELECT MAX(id) AS id FROM mdl_grade_items';
            $recojo_id_grade_items = $DB->get_record_sql($ultimo_id_grade_items);
            $id_grade_items = $recojo_id_grade_items->id;
            $id_grade_items++;
        // courseid -> id del curso
            $courseid_grade_items = $id_course;
        // categoryid -> número de tema
            $categoryid_grade_items = $numero_capitulo;
        // itemname -> título del recurso
            $itemname_grade_items = $name_assign;
        // itemtype -> tipo de recurso
            $itemtype_grade_items = 'mod';
        // itemmodule -> módulo que lo genera
            $itemmodule_grade_items = 'assign';
        // iteminstance -> ya generado 
            $iteminstance_grade_items = $instance;
        // itemnumber 
            $itemnumber_grade_items = 0;
        // iteminfo, idnumber, calculation, gradetype, grademax, grademin, scaleid, outcomeid, gradepass
            $iteminfo_grade_items = 'NULL';
            $idnumber_grade_items = 'NULL';
            $calculation_grade_items = 'NULL';
            $gradetype_grade_items = 1;
            $grademax_grade_items = 100.00000;
            $grademin_grade_items = 0.00000;
            $scaleid_grade_items = 'NULL';
            $outcomeid_grade_items = 'NULL';
            $gradepass_grade_items = 0.00000;
            $multflactor_grade_items = 1.00000;
            $plusfactor_grade_items = 0.00000;
            $aggregationcoef_grade_items = 0.00000;
            $aggregationcoef2_grade_items = 0.00000;
        // sortorder -> orden en que se muestra en el libro de calificaciones PENDIENTE DE ASIGNAR
            $sortorder_grade_items = '';
        // display
            $display_grade_items = 0;
            $decimals_grade_items = 'NULL';
            $hidden_grade_items = 0;
            $locked_grade_items = 0;
            $locktime_grade_items = 0;
            $needsupdate_grade_items = 0;
            $weightoverride_grade_items = 0;
        // timecreated -> ya declarado
            $timecreated_grade_items = $fecha_course_module;
            $timemodified_grade_items = $fecha_course_module;

    // Sentencias para escribir en MDL_GRADE_ITEMS
    $tabla_mdl_grade_items = "INSERT into mdl_grade_items 
        (id, courseid, categoryid, itemname, itemtype, itemmodule, iteminstance, itemnumber, iteminfo, idnumber, calculation, gradetype, grademax, grademin, scaleid, outcomeid, gradepass, multflactor, plusfactor, aggregationcoef, aggregationcoef2, sortorder, display, decimals, hidden, locked, locktime, needsupdate, weightoverride, timecreated, timemodified) VALUES ($id_grade_items, $courseid_grade_items, $categoryid_grade_items, $itemname_grade_items, $itemtype_grade_items, $itemmodule_grade_items, $iteminstance_grade_items, $itemnumber_grade_items, $iteminfo_grade_items, $idnumber_grade_items, $calculation_grade_items, $gradetype_grade_items, $grademax_grade_items, $grademin_grade_items, $scaleid_grade_items, $outcomeid_grade_items, $gradepass_grade_items, $multflactor_grade_items, $plusfactor_grade_items, $aggregationcoef_grade_items, $aggregationcoef2_grade_items, $sortorder_grade_items, $display_grade_items, $decimals_grade_items, $hidden_grade_items, $locked_grade_items, $locktime_grade_items, $needsupdate_grade_items, $weightoverride_grade_items, $timecreated_grade_items, $timemodified_grade_items)";

    // MDL_GRADE_ITEMS_HISTORY -> columnas   CREO QUE ES AUTOMÁTICA, COMPROBAR
    


    
/*******************************************************************************/
    
    // Pinto actividades en la base de datos
    for ($i=1; $i<count($elementos); $i++) {
        if(strpos($elementos[$i]->nodeValue, 'Unidad de aprendizaje')) {
            $numero_capitulo++;
            echo $numero_capitulo.'<br/>';
        } else {
            // Obtengo los párrafos pertenecientes a cada unidad
            foreach ($elementos[$i]->getElementsbyTagname('p') as $parrafo) {
                $parrafos[] = $parrafo->nodeValue;
            }
            for($j=0; $j<count($parrafos); $j++) {
                if ($j==0) {
                    // Condición para obtener el tipo de recurso -> $module_type
                    if(strstr($parrafos[0], 'Actividad colaborativa')) {
                        $module_type = 5;
                        // Asigno el nombre de la tarea
                        $name_assign = $parrafos[0];
                    } else if (strstr($parrafos[0], 'Tarea de evaluación')) {
                        $module_type = 1;
                        // Asigno el nombre de la tarea
                        $name_assign = $parrafos[0];
                    } else { $module_type = 0; }
                    
                    echo '<strong>'.$parrafos[$j].'</strong></br>';
                } else {
                    echo $parrafos[$j].'</br>';   
                }
            }
            echo '<br/>';
            // Inicializo párrafos para que pase de tema
            $intro_assign = implode('<br/>', $parrafos);
            $parrafos = array();
        }
    }
echo $tabla_mdl_course_modules.'<br/><br/><br/>';
echo $tabla_mdl_assign.'<br/><br/><br/>';
echo $tabla_mdl_grade_items.'<br/><br/><br/>';
//$DB->insert_record($table, $dataobject, $returnid=true, $bulk=false)



?>