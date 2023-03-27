<?php
/**
 * Forms
 * 
 * Para facilitar o trabalho com formularios, este arquivo os centraliza. Assim eh
 * mais facil de encontrar a origem dos formularios e fazer alteracoes quando
 * necessario.
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/formslib.php');

// formulario escondido para os docentes criarem um ambiente para um curso
class redirecionamento_criacao_ambiente extends moodleform {
  public function definition () {
    // input hidden com o id da turma no plugin Extensao
    $codofeatvceu = "";
    if (isset($this->_customdata['codofeatvceu']))
      $codofeatvceu = $this->_customdata['codofeatvceu'];  
    $this->_form->addElement('hidden', 'codofeatvceu', $codofeatvceu);
    $this->_form->setType('codofeatvceu', PARAM_TEXT);
    
    // botao de submit
    $this->_form->addElement('submit', 'redirecionar_criar_ambiente', 'Criar ambiente');
  }
}

// formulario para a criacao de ambientes no Moodle
class criar_ambiente_moodle extends moodleform {
  public function definition () {
    // input hidden com o id da turma no plugin Extensao
    $codofeatvceu = "";
    if (isset($this->_customdata['codofeatvceu']))
      $codofeatvceu = $this->_customdata['codofeatvceu'];
    $this->_form->addElement('hidden', 'codofeatvceu', $codofeatvceu);
    $this->_form->setType('codofeatvceu', PARAM_TEXT);

    // nome completo do curso
    $nomecompleto = "";
    if (isset($this->_customdata['fullname']))
      $nomecompleto = $this->_customdata['fullname'];
    $this->_form->addElement('text', 'fullname', 'Nome completo do curso', array('value'=>$nomecompleto));
    $this->_form->setType('fullname', PARAM_TEXT);

    // botao de submit
    $this->_form->addElement('submit', 'criar_ambiente_moodle', 'Criar ambiente');
  }
}