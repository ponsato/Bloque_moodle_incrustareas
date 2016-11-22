<?php


//defined('MOODLE_INTERNAL') || die();
global $CFG, $PAGE, $DB;
require_once($CFG->dirroot . '/comment/lib.php');
require_once($CFG->dirroot . '/files/externallib.php');

 class block_incrustareas extends block_base {
    
    // Inicializo
    public function init() {
        global $PAGE;
        $this->title = get_string('incrustareas', 'block_incrustareas');
    }
    
    // Añado el título al bloque
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_incrustareas');            
            } else {
                $this->title = $this->config->title;
            }
        }
    }
    
    // Restrinjo el bloque únicamente para los cursos con formato temas
    public function applicable_formats() {
      return array(
           'site-index' => false,
          'course-view' => true, 
    'course-view-topics' => true,
    'course-view-social' => false,
                  'mod' => false, 
             'mod-quiz' => false
      );
    }
    
    // Contenido interno del bloque
    public function get_content() {
        global $CFG, $PAGE, $COURSE, $DB;
        $ruta_pinta_archivo = $CFG->wwwroot . '/blocks/incrustareas/pinta_archivo.php';
        $ruta_assign_task = $CFG->wwwroot . '/blocks/incrustareas/assign_task.php';
        $ruta_delete_file = $CFG->wwwroot . '/blocks/incrustareas/delete_file.php';
        
    //  Obtengo el contenido (nombre, fecha y texto) del archivo que se ha subido
        $sql = "SELECT * from mdl_files where id<(SELECT max(id) from mdl_files) and filename like '%actividades.htm%' ORDER BY id DESC";
        //$sql_resultado = $DB->get_record_sql($sql);
        if ($sql_resultado = $DB->get_record_sql($sql)) {
            $archivo_actividades = $sql_resultado->filename;
            $fecha_creacion = $sql_resultado->timecreated;
            $fecha_creacion = date('d-m-Y', $fecha_creacion);
            $id_archivo = $sql_resultado->itemid;
            $ruta_archivo = $sql_resultado->contenthash;
        } else {
            $archivo_actividades = '<h3>No hay archivo cargado</h3></br>';
            $ruta_archivo = '';
            $fecha_creacion = 'Utilice la opción de Configurar este bloque para subir uno.';
            $id_archivo = 'no existe';
        }
        //$coursecontext = context_course::instance($COURSE->id);
        if ($this->content !== NULL) {
            return $this->content;
        }
        if (!$CFG->usecomments) {
            $this->content = new stdClass();
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabledcomments');
            }
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';
        if (empty($this->instance)) {
            return $this->content;
        }
        list($context, $course, $cm) = get_context_info_array($PAGE->context->id);
        global $PAGE;
        $this->content = new stdClass();
        //$this->content->text = html_writer::tag('p', 'Este bloque requiere un archivo nombrado como "actividades.txt" que ha de subirse previamente.<br/>');
        if($id_archivo == 'no existe') {
            $this->content->text = html_writer::tag('label', '<strong>'.$archivo_actividades.'</strong></a><br/><strong>'.$fecha_creacion.'</strong><br/><br/>'); 
        } else {
            $this->content->text = html_writer::tag('label', 'Se ha encontrado el archivo: <a href="'.$ruta_pinta_archivo.'?='.$id_archivo.'?='.$ruta_archivo.'" target="_blank"><strong>'.$archivo_actividades.'</strong></a><br/>Creado el día: <strong>'.$fecha_creacion.'</strong><br/><br/>');
            $this->content->footer = '¿Este archivo pertenece a las tareas del curso <strong>'.$COURSE->fullname.'</strong>?<br/><ul style="list-style:none; font-weight:bold"><li><a href="'.$ruta_assign_task.'?='.$id_archivo.'?='.$ruta_archivo.'" target="_blank">Si</a></li><li><a href="'.$ruta_delete_file.'?='.$id_archivo.'?='.$ruta_archivo.'" target="_blank">No, borrar</a></li></ul>';
        }
        return $this->content;
    }
 }



     
     



        

        
        
        


    
    


    


    