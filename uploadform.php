<?php

require_once("$CFG->libdir/formslib.php");

class uploadform extends moodleform {
    public function definition() {
        $this->_form->addElement('text', 'description', 'identificação do arquivo:');
        $this->_form->addElement('select', 'type', 'Tipo', ['users', 'courses']);
        $this->_form->addElement('file', 'file','Arquivo csv');
        $this->_form->addElement('submit', 'buttob', 'Enviar');
        
        $this->_form->addRule('description',null,'required');
        $this->_form->addRule('type',null,'required');
        $this->_form->addRule('file',null,'required');
    }
}