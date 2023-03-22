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
    $this->_form->addElement('hidden', 'id_turma_extensao', $this->_customdata['id_turma_extensao']);

    // botao de submit
    $this->_form->addElement('submit', 'redirecionar_criar_ambiente', 'Criar ambiente');
  }
}