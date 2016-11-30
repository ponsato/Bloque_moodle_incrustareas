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
    // Obtengo cada uno de los elementos
    foreach ($documentopath->query('//div') as $elemento) {
        $elementos[] = $elemento;
    }
    
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


$numero_capitulo = 0;
/****************************** DB *********************************************/
    // VARIABLES con los valores necesarios para agregar tareas en cada tabla

// MDL_ASSIGN -> columnas
        $mdl_assign = new stdClass();
    // id -> se ha recogido anteriormente mediante la variable $instance de mdl_course_modules
        $ultima_instance_assign = 'SELECT MAX(id) AS id FROM mdl_assign';
        $recojo_instance_assign = $DB->get_record_sql($ultima_instance_assign);
        $instance_assign = $recojo_instance_assign->id;
        $instance_assign++;
        $mdl_assign->id = $instance_assign;
    // course -> id del curso
        $mdl_assign->course = $id_course;
    // name -> nombre del recurso. Se agrega automáticamente a la fila correspondiente
        $mdl_assign->name = '';
    // intro -> enunciado del recurso. Se agrega automáticamente a la fila correspondiente. Añadir coletilla.
        $mdl_assign->intro = '';
    // introformat -> formato de introducción
        $mdl_assign->introformat = 1;
    // alwaysshowdescription -> muestra descripción
        $mdl_assign->alwaysshowdescription = 0;
    // nosubmissions, submissiondrafts
        $mdl_assign->nosubmissions = 0;
        $mdl_assign->submissiondrafts = 0;
    // sendnotifications y sendlatenotifications -> enviar notificaciones al tutor
        $mdl_assign->sendnotifications = 1;
        $mdl_assign->sendlatenotifications = 0;
    // duedate
        $mdl_assign->duedate = 0;
    // allowsubmissionsfromdate
        $mdl_assign->allowsubmissionsfromdate = 0;
    // grade
        $mdl_assign->grade = 100;
    // timemodified
        $fecha_de_archivo = "SELECT * from mdl_files where id<(SELECT max(id) from mdl_files) and filename like '%actividades.htm%' ORDER BY id DESC";
        $fecha_creacion_archivo = $DB->get_record_sql($fecha_de_archivo);
        $fecha_course_module = $fecha_creacion_archivo->timecreated;
        $mdl_assign->timemodified = $fecha_course_module;
    // requiresubmissionstatement
        $mdl_assign->requiresubmissionstatement = 0;
    // completionsubmit
        $mdl_assign->completionsubmit = 0;
    // cutoffdate
        $mdl_assign->cutoffdate = 0;
    // teamsubmission
        $mdl_assign->teamsubmission = 0;
    // requireallteammemberssubmit
        $mdl_assign->requireallteammemberssubmit = 0;
    // teamsubmissiongroupingid
        $mdl_assign->teamsubmissiongroupingid = 0;
    // blindmarking
        $mdl_assign->blindmarking = 0;
    // revealidentities
        $mdl_assign->revealidentities = 0;
    // attemptreopenmethod
        $mdl_assign->attemptreopenmethod = 'none';
    // maxattempts
        $mdl_assign->maxattempts = -1;
    // markingworkflow
        $mdl_assign->markingworkflow = 0;
        $mdl_assign->markingallocation = 0;
    // sendstudentnotifications
        $mdl_assign->sendstudentnotifications = 1;

    
    // Sentencia para escribir en MDL_ASSIGN
        //$escribe_assign = $DB->insert_record('assign', $mdl_assign);



// MDL_GRADE_ITEMS -> columnas
        $mdl_grade_items = new stdClass();
    // id -> $id_grade_items equivale a id de la tabla assign
        /*$ultimo_id_grade_items = 'SELECT MAX(id) AS id FROM mdl_grade_items';
        $recojo_id_grade_items = $DB->get_record_sql($ultimo_id_grade_items);
        $id_grade_items = $recojo_id_grade_items->id;
        $id_grade_items++;*/
        $mdl_grade_items->id = $instance_assign;
    // courseid -> id del curso
        $mdl_grade_items->courseid = $id_course;
    // categoryid -> número de tema
        //$mdl_grade_items->categoryid = $numero_capitulo;
    // itemname -> título del recurso
        $mdl_grade_items->itemname = $name_assign;
    // itemtype -> tipo de recurso
        $mdl_grade_items->itemtype = 'mod';
    // itemmodule -> módulo que lo genera
        $mdl_grade_items->itemmodule = 'assign';
    // iteminstance -> ya generado 
        /*$ultima_instance_grade_items = 'SELECT MAX(id) AS id FROM mdl_assign';
        $recojo_instance_grade_items = $DB->get_record_sql($ultima_instance_grade_items);
        $instance_grade_items = $recojo_instance_grade_items->id;
        $instance_grade_items++;
        $mdl_grade_items->iteminstance = $instance_grade_items;*/
    // itemnumber 
        $mdl_grade_items->itemnumber = 0;
    // iteminfo, idnumber, calculation, gradetype, grademax, grademin, scaleid, outcomeid, gradepass
        //$mdl_grade_items->idnumber = "NULL";
        //$mdl_grade_items->calculation = "NULL";
        $mdl_grade_items->gradetype = 1;
        $mdl_grade_items->grademax = 100.00000;
        $mdl_grade_items->grademin = 0.00000;
        //$mdl_grade_items->scaleid = "";
        //$mdl_grade_items->outcomeid = "";
        $mdl_grade_items->gradepass = 0.00000;
        $mdl_grade_items->multfactor = 1.00000;
        $mdl_grade_items->plusfactor = 0.00000;
        $mdl_grade_items->aggregationcoef = 0.00000;
        $mdl_grade_items->aggregationcoef2 = 0.00000;
    // sortorder -> orden en que se muestra en el libro de calificaciones PENDIENTE DE ASIGNAR
        $mdl_grade_items->sortorder = '';
    // display
        $mdl_grade_items->display = 0;
        //$mdl_grade_items->decimals = "";
        $mdl_grade_items->hidden = 0;
        $mdl_grade_items->locked = 0;
        $mdl_grade_items->locktime = 0;
        $mdl_grade_items->needsupdate = 0;
        $mdl_grade_items->weightoverride = 0;
    // timecreated -> ya declarado
        $mdl_grade_items->timecreated = $fecha_course_module;
        $mdl_grade_items->timemodified = $fecha_course_module;


    // Sentencia para escribir en MDL_GRADE_ITEMS
        //$escribe_grade_items = $DB->insert_record('grade_items', $mdl_grade_items);




// MDL_COURSE_MODULES -> columnas
        $mdl_course_modules = new stdClass();
    //id = $id_course_modules -> obtengo el último agregada para incrementarlo en 1
        $ultimo_id_course_modules = 'SELECT MAX(id) AS id FROM mdl_course_modules';
        $recojo_id_course = $DB->get_record_sql($ultimo_id_course_modules);
        $id_course_modules = $recojo_id_course->id;
        $id_course_modules++;
        $mdl_course_modules->id = $id_course_modules;
    // course = $id_course
        $mdl_course_modules->course = $id_course;
    // module  -> el típo de recurso a insertar. 1 tarea, 5 actividad (foro). Condición para distinguir entre actividad o tarea que asigna el valor a esta variable
        $mdl_course_modules->module = 0;
    // instance -> equivale a la id de la tabla mdl_assign donde se añaden los enunciados, por lo que obtengo el último id para agregarlo
        /*$ultima_instance_course_modules = 'SELECT MAX(instance) AS instance FROM mdl_course_modules';
        $recojo_instance_course_modules = $DB->get_record_sql($ultima_instance_course_modules);
        $instance_course_modules = $recojo_instance_course_modules->id;
        $instance_course_modules++;*/
        // Equivale a la id del elemento en la tabla mdl_assign, la asigno antes de insertar filas
        //$mdl_course_modules->instance = $mdl_assign->id;
    // section -> el número del tema donde añadir el recurso. Se incrementa automáticamente tantas veces como temas hay.
        //$mdl_course_modules->section = $numero_capitulo;
    // idnumber -> no es obligatorio, por lo que será NULL o vacío
        $mdl_course_modules->idnumber = '';
    // added -> fecha de inclusión. Se pondrá la misma que la del fichero subido
        $mdl_course_modules->added = $fecha_course_module;
    // score -> puntuación. Será siempre cero hasta recibir calificación
        $mdl_course_modules->score = 0;
    // indent -> si se ha indentado hacia la izquierda
        $mdl_course_modules->indent = 0;
    // visible y visibleold -> se muestra o está oculto
        $mdl_course_modules->visible = 1;
        $mdl_course_modules->visibleold = 1;
    // groupmode y groupingid -> grupos permitidos
        $mdl_course_modules->groupmode = 0;
        $mdl_course_modules->groupingid = 0;
    // completion, completiongradeitemnumber, completionview y completionexpected -> Finalización
        $mdl_course_modules->completion = 0;
        //$mdl_course_modules->completiongradeitemnumber = "";
        $mdl_course_modules->completionview = 0;
        $mdl_course_modules->completionexpected = 0;
    // showdescription -> muestra descripción
        $mdl_course_modules->showdescription = 0;
    // availability -> disponibilidad
        //$mdl_course_modules->availability = "NULL";


    // Sentencia para escribir en MDL_COURSE_MODULES
        //$escribe_course_modules = $DB->insert_record('course_modules', $mdl_course_modules);


/* En esta tabla deberá actualizarse únicamente los campos pertenecientes a los temas del curso, en la columna sequence, que se hace directamente en la misma función

// MDL_COURSE_SECTIONS -> columnas
        $mdl_course_sections = nex stdClass();
    //id = $id_course_modules -> obtengo el último agregada para incrementarlo en 1
        $ultimo_id_course_sections = 'SELECT MAX(id) AS id FROM mdl_course_sections';
        $recojo_id_course_sections = $DB->get_record_sql($ultimo_id_course_modules);
        $id_course_sections = $recojo_id_course_sections->id;
        $id_course_sections++;
        $mdl_course_sections->id = $id_course_sections;
    //course = curso
        $mdl_course_sections->course = $id_course;
    //section = capítulo
        $mdl_course_sections->section = $numero_capitulo;
    // name = título del recurso
        $mdl_course_sections->name = '';
    // summary
        $mdl_course_sections->summary = '';
    // summaryformat
        $mdl_course_sections->summaryformat = 1;
    // sequence -> aquí hay que recoger lo que hay en cada uno de section pertenecientes al id del curso, y agregar el $id_course_modules a continuación separado con ,
        $mdl_course_sections->sequence = 1;
    // visible
        $mdl_course_sections->visible = 1;
    // availabillity
        $mdl_course_sections->availability = "NULL";

    // Sentencia para escribir en MDL_COURSE_SECTIONS
        $escribe_course_sections = $DB->insert_record('course_sections', $mdl_course_sections);
        
*/

// MDL_FORUM -> columnas
        $mdl_forum = new stdClass();
    //id = $id_course_modules -> obtengo el último agregada para incrementarlo en 1
        $ultimo_id_forum = 'SELECT MAX(id) AS id FROM mdl_forum';
        $recojo_id_forum = $DB->get_record_sql($ultimo_id_forum);
        $id_forum = $recojo_id_forum->id;
        $id_forum++;
        $mdl_forum->id = $id_forum;
        $mdl_forum->course = $id_course;
        $mdl_forum->type = 'general';
        /*$mdl_forum->name = '';
        $mdl_forum->intro = '';*/
        $mdl_forum->introformat = 1;
        $mdl_forum->assessed = 0;
        $mdl_forum->assesstimestart = 0;
        $mdl_forum->assesstimefinish = 0;
        $mdl_forum->scale = 1;
        $mdl_forum->maxbytes = 512000;
        $mdl_forum->maxattachments = 9;
        $mdl_forum->forcesubscribe = 0;
        $mdl_forum->trackingtype = 1;
        $mdl_forum->rsstype = 0;
        $mdl_forum->rssarticles = 0;
        $mdl_forum->timemodified = $fecha_course_module;
        $mdl_forum->warnafter = 0;
        $mdl_forum->blockafter= 0;
        $mdl_forum->blockperiod = 0;
        $mdl_forum->completiondiscussions = 0;
        $mdl_forum->completionreplies = 0;
        $mdl_forum->completionposts = 0;
        $mdl_forum->displaywordcount = 0;
        
/*******************************************************************************/




// Pinto actividades en la base de datos
    for ($i=1; $i<count($elementos); $i++) {
        if(strpos($elementos[$i]->nodeValue, 'Unidad de aprendizaje')) {
            // Distingo capítulos
            $numero_capitulo++;
            $mdl_grade_items->categoryid = $numero_capitulo;
            // Obtengo la id de mdl_course_sections para insertarla en mdl_course_modules, ya que tiene que coincidir
            $section_mdl_course_modules = "SELECT id FROM mdl_course_sections WHERE course = $id_course and section = $numero_capitulo";
            $section_mdl_course_modules_resultado = $DB->get_record_sql($section_mdl_course_modules);
            $mdl_course_modules->section = $section_mdl_course_modules_resultado->id;
        } else {
            // Obtengo los párrafos pertenecientes a cada unidad
            foreach ($elementos[$i]->getElementsbyTagname('p') as $parrafo) {
                $parrafos[] = $parrafo->nodeValue;
            }
            for($j=0; $j<count($parrafos); $j++) {
                if ($j==0) {
                    // Condición para obtener el tipo de recurso -> $module_type
                    if(strstr($parrafos[0], 'Actividad colaborativa')) {
                        //  CAMBIAR A 9 PARA FOROS
                        $module_type = 9;
                        $mdl_course_modules->module = $module_type;
                        // Asigno el nombre de la actividad
                        $parrafos[0] = "<h2>".$parrafos[0]."</h2>";
                        $name_assign = $parrafos[0];
                    } else if (strstr($parrafos[0], 'Tarea de evaluación')) {
                        $module_type = 1;
                        $mdl_course_modules->module = $module_type;
                        // Asigno el nombre de la tarea
                        $parrafos[0] = "<h2>".$parrafos[0]."</h2>";
                        $name_assign = $parrafos[0];
                    } else { $module_type = 0; }
//                    echo '<strong>'.$parrafos[$j].'</strong></br>';
                    
                    $mdl_assign->name = $parrafos[$j];
                    $mdl_grade_items->itemname = $parrafos[$j];
                    $mdl_forum->name = $parrafos[$j];
                } else {
//                    echo $parrafos[$j].'</br>';
                    if((strstr($parrafos[$j], 'Objetivos')) || (strstr($parrafos[$j], 'Enunciado')) || (strstr($parrafos[$j], 'Duración')) || (strstr($parrafos[$j], 'Recursos de apoyo')) || (strstr($parrafos[$j], 'Criterio de evaluación asociado')) || (strstr($parrafos[$j], 'Orientaciones para la entrega'))) {
                        $parrafos[$j] = "<p style='font-size:12pt; font-weight:bold; font-family:verdana, arial, helvetica, sans-serif;'>".$parrafos[$j]."</p>";
                    } else {
                        $parrafos[$j] = "<p style='font-size:12pt; font-family:verdana, arial, helvetica, sans-serif;'>".$parrafos[$j]."</p>";
                    }
                    
                    $intro_assign = implode('', $parrafos);
                    $mdl_assign->intro = $intro_assign;
                    $mdl_forum->intro = $intro_assign;
                }
            }
            
            if ($mdl_course_modules->module == 1) {
                
            // Sentencia para escribir en MDL_ASSIGN
                $escribe_assign = $DB->insert_record('assign', $mdl_assign);
                
                // Obtengo la id del elemento creado en mdl_assign para asignar el mismo valor a instance en la tabla mdl_course_modules
                $id_para_instance_assign = "SELECT id FROM mdl_assign ORDER BY id DESC";
                $id_para_instance_assign_resultado = $DB->get_record_sql($id_para_instance_assign);
                $mdl_course_modules->instance = $id_para_instance_assign_resultado->id;
            
            // Sentencia para escribir en MDL_COURSE_MODULES
                $escribe_course_modules = $DB->insert_record('course_modules', $mdl_course_modules);    
            
                // Obtengo la id del elemento creado en mdl_course_modules para asignar el mismo valor a sequence en la tabla mdl_course_sections
                $id_para_sequence = "SELECT id FROM mdl_course_modules ORDER BY id DESC";
                $id_para_sequence_resultado = $DB->get_record_sql($id_para_sequence);
            
                // Obtengo el contenido actual de sequence para agregar el nuevo valor al final
                $secuence_contenido = "SELECT sequence FROM mdl_course_sections WHERE id = $mdl_course_modules->section and section = $numero_capitulo";
                $secuence_contenido_resultado = $DB->get_record_sql($secuence_contenido);
                // Agrego el nuevo valor al final
                $secuence_contenido_resultado = $secuence_contenido_resultado->sequence.','.$id_para_sequence_resultado->id;
                            
                $secuence_nuevo = new stdClass();
                $secuence_nuevo->id = $mdl_course_modules->section;
                $secuence_nuevo->sequence = $secuence_contenido_resultado;
                                        
                $DB->update_record('course_sections', $secuence_nuevo);
            
                $item_instance_grade_items = 'SELECT MAX(id) AS id FROM mdl_assign';
                $item_instance_grade_items_resultado = $DB->get_record_sql($item_instance_grade_items);
                $mdl_grade_items->iteminstance = $item_instance_grade_items_resultado->id;
            
            
            // Sentencia para escribir en MDL_GRADE_ITEMS
                $escribe_grade_items = $DB->insert_record('grade_items', $mdl_grade_items);
            } else if ($mdl_course_modules->module == 9) {
                
            // Sentencia para escribir en MDL_FORUM
                $escribe_forum = $DB->insert_record('forum', $mdl_forum);
                
                // Obtengo la id del elemento creado en mdl_forum para asignar el mismo valor a instance en la tabla mdl_course_modules
                $id_para_instance_assign = "SELECT id FROM mdl_forum ORDER BY id DESC";
                $id_para_instance_assign_resultado = $DB->get_record_sql($id_para_instance_assign);
                $mdl_course_modules->instance = $id_para_instance_assign_resultado->id;
            
            // Sentencia para escribir en MDL_COURSE_MODULES
                $escribe_course_modules = $DB->insert_record('course_modules', $mdl_course_modules);    
            
                // Obtengo la id del elemento creado en mdl_course_modules para asignar el mismo valor a sequence en la tabla mdl_course_sections
                $id_para_sequence = "SELECT id FROM mdl_course_modules ORDER BY id DESC";
                $id_para_sequence_resultado = $DB->get_record_sql($id_para_sequence);
            
                // Obtengo el contenido actual de sequence para agregar el nuevo valor al final
                $secuence_contenido = "SELECT sequence FROM mdl_course_sections WHERE id = $mdl_course_modules->section and section = $numero_capitulo";
                $secuence_contenido_resultado = $DB->get_record_sql($secuence_contenido);
                // Agrego el nuevo valor al final
                $secuence_contenido_resultado = $secuence_contenido_resultado->sequence.','.$id_para_sequence_resultado->id;
                            
                $secuence_nuevo = new stdClass();
                $secuence_nuevo->id = $mdl_course_modules->section;
                $secuence_nuevo->sequence = $secuence_contenido_resultado;
                                        
                $DB->update_record('course_sections', $secuence_nuevo);
            
                $item_instance_grade_items = 'SELECT MAX(id) AS id FROM mdl_assign';
                $item_instance_grade_items_resultado = $DB->get_record_sql($item_instance_grade_items);
                $mdl_grade_items->iteminstance = $item_instance_grade_items_resultado->id;
                
            
            } else {
                echo "<script languaje='javascript' type='text/javascript'>alert('Se jodión la marrana');</script>";
            }
            
            
            
            
            
            
            
            // Inicializo párrafos para que pase de tema
            /*$intro_assign = implode('<br/>', $parrafos);
            $mdl_assign->intro = $intro_assign;*/
            $parrafos = array();
            
        }
    }

    purge_all_caches();
    echo "<script languaje='javascript' type='text/javascript'>alert('Actividades añadidas con éxito!!!'); window.opener.location.reload(); window.close();</script>";


    
    


//print_r($mdl_assign);
//$DB->insert_record($table, $dataobject, $returnid=true, $bulk=false)



?>