<?php

class block_incrustareas_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
        global $CFG, $PAGE, $DB;
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
 
        // A sample string variable with a default value.
        //$mform->addElement('text', 'config_text', get_string('blockstring', 'block_incrustareas'));
        //$mform->setDefault('config_text', ' ');
        //$mform->setType('config_text', PARAM_RAW);       
        
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_incrustareas'));
        $mform->setDefault('config_title', ' ');
        $mform->setType('config_title', PARAM_TEXT);
        $maxbytes = 1024; 
        $mform->addElement('filemanager', 'archivo_tareas', 'Subir archivos aquí', null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 50, 'accepted_types' => '*')); 
        
    }

}


