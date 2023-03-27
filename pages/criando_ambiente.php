<?php
/**
 * Aqui eh necessario capturar as informacoes que vieram do forms atraves
 * do campo hidden 'id_turma_extensao' e mostrar ao usuario/docente as 
 * informacoes basicas do ambiente que esta criando.
 */

require_once(dirname(__FILE__) . '/../../../config.php');
global $USER, $PAGE, $OUTPUT;

$PAGE->set_pagelayout('admin');
$PAGE->set_url("/block/extensao/criar_ambiente");
$PAGE->set_context(context_system::instance());
$PAGE->set_heading(get_string('pluginname', 'block_extensao'));
require_login();

// requerimentos
require_once('../utils/forms.php');
require_once('../src/ambiente.php');

// captura os dados vindos do formulario (configurar ainda)
$forms = new criar_ambiente_moodle();
$info_forms = $forms->get_data();

// transforma o enviado em um objeto de curso
$curso = Ambiente::criar_objeto_curso($info_forms);

// tenta criar o curso
$curso_criado = Ambiente::criar_ambiente($curso);

// exibe informacoes caso de errado, exibe o curso com uma mensagem de sucesso caso de certo
if (!$curso_criado) {
  \core\notification::error('Um curso com o mesmo nome curto já consta na base Moodle!');
  $_SESSION['codofeatvceu'] = $info_forms->codofeatvceu; // salva para poder usar novamente
  redirect(new moodle_url('criar_ambiente.php'));
} else {
  \core\notification::success('Ambiente criado com sucesso!');
  redirect(new moodle_url($CFG->wwwroot) . "/course/view.php?id={$curso_criado->id}");
}