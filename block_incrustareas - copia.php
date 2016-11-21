<?php


//defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/comment/lib.php');
require_once($CFG->dirroot . '/files/externallib.php');

//require_once($CFG->dirroot . '/blocks/incrustareas/dropzone.js');

class block_incrustareas extends block_base {
    
    // Inicializo
    public function init() {
        global $PAGE;
        $this->title = get_string('incrustareas', 'block_incrustareas');
        //$PAGE->requires->js('/blocks/incrustareas/js/dropzone.js');
    }
    
    // Añado el título al bloque
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_incrustareas');            
            } else {
                $this->title = $this->config->title;
            }
            /*if (empty($this->config->text)) {
                $this->config->text = get_string('defaulttext', 'block_incrustareas');
            }*/
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
        global $CFG, $PAGE, $DB;
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
        $this->content->text = html_writer::tag('label', 'kepaza'.$draftitemid.'');
        //$this->content->text = $comment->output(true);
        $this->content->footer = '';
        return $this->content;
    }
    

}

function get_files_by_itemid($itemid) { 
        global $CFG, $PAGE, $DB;
        if(($itemid != 0)) {
            $punetero_numero = $itemid;
            $sql="SELECT * FROM mdl_files order by id desc limit 1";
            echo 'estoy funcionando, punetero numero vale = '. $punetero_numero . '<br>';
            siguo_intentandolo($punetero_numero);
            $punetero_numero = $itemid;
        } else if(($itemid == '') || ($itemid == 'undefined') || ($itemid == null) || ($itemid == false)) {
            echo 'la variable draft vale ' . $itemid . 'y no ha funcionado<br>';
            $punetero_numero = '<br>estoy en el else';
        } else {
            $punetero_numero = '<br>a por cojones<br>';
            echo $punetero_numero;
        }
        if (isset($itemid) && $itemid!=0) {
            $sql_resultado = $DB->get_record_sql($sql);
            echo '<br>aqui tambien<br>';
            print_r($sql_resultado); 
            //siguo_intentandolo($sql_resultado);
        }
    }

// Obtengo el contenido del archivo que se ha subido
        $context = context_system::instance();
        if (empty($entry->id)) {
            $entry = new stdClass;
            $entry->id = null;
            $entry->definition = '';
            $entry->format = FORMAT_HTML;
        }
    
        echo 'eeeh<br>'.$draftitemid.'<br>';

        if ($draftitemid = file_get_submitted_draft_itemid('archivo_tareas')) {
           // echo $draftitemid.' de primeras<br>';
            $currenttext = file_prepare_draft_area($draftitemid, $context->id, 'mod_glossary', 'archivo_tareas',
                                       $entry->id, array('subdirs'=>true), $entry->definition);
            $entry->entry = array('text'=>$currenttext, 'format'=>$entry->format, 'itemid'=>$draftitemid);
            print_r($entry);
            echo $draftitemid.' de primeras<br>';
            get_files_by_itemid($draftitemid);
        } else {
            echo $draftitemid.' de segundas en el else<br>';
            get_files_by_itemid($draftitemid);
        }

        //file_prepare_draft_area($draftitemid, $context->id, 'course', 'archivo_tareas', $entry->id,array('subdirs' => 0, 'maxbytes' => 1024, 'maxfiles' => 50));
        global $punetero_numero;
        //get_files_by_itemid($draftitemid);

        function siguo_intentandolo($traeitemid) {
            $otro = $traeitemid;
            //echo 'siii = ' . $otro .'<br>';
        }

        echo $draftitemid.' este se pinta siempre<br>y punetero numero vale = ' . $otro . '<br>';
        //global $sql_resultado;
        //print_r($sql_resultado); 

        $sql_2="SELECT * FROM mdl_files order by id desc limit 1";
        global $CFG, $PAGE, $DB;
        $sql_resultado_2 = $DB->get_record_sql($sql_2);
        print_r($sql_resultado_2); 
            //print_r($sql_resultado); 
            //siguo_intentandolo($sql_resultado);
        

        
        
        


    
    


    


    