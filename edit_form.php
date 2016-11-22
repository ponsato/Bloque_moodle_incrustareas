<?php

class block_incrustareas_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
        global $CFG, $PAGE, $DB;
        $mform = $this->_form;
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
        $mform->addElement('select', 'resource', 'Tipo de curso', array('Capacitación', 'Certificado'));
        $mform->setDefault('type', 'Capacitación');
        $mform->addElement('text', 'id_curso', 'Id del curso<br/><small>Indicado en la url tras ?id=</small>');
        $mform->setDefault('id_curso', ' ');
        $mform->setType('id_curso', PARAM_INT);
        
        
    }
    
    /*function config_save($data) {
    // Comportamiento por defecto: guarda todas las variables como propiedades $CFG
    // No necesitas sobrescribirlo si estás satisfecho con lo anterior
    foreach ($data as $name => $value) {
        set_config($name, $value);
    }
    return true;*/
}

}


