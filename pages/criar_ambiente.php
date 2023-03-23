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
require_once('../src/turmas.php');

// forms
$forms = new redirecionamento_criacao_ambiente();
$info_forms = $forms->get_data();
$codofeatvceu = $info_forms->codofeatvceu;

// verifica se a turma enviada eh do usuario logado
if (! Turmas::usuario_docente_turma($USER->idnumber, $codofeatvceu) ) {
  \core\notification::error('A turma solicitada não está na sua lista de turmas!');
  $url = new moodle_url($CFG->wwwroot);
  redirect($url);
}

/**
 * aqui precisamos capturar as informacoes basicas do curso
 * 
 * podemos seguir a ideia do e-disciplinas e carregar o titulo,
 * nome curto e descricao num primeiro momento.
 * 
 * ainda nao tenho a query, entao vou puxar apenas o nome do 
 * curso, edicao e tal. 
 * 
 * eh importante tambem criar o formulario como um $mform, e 
 * nao na mao.
 */
$informacoes_turma = Turmas::info_turma_id_extensao($codofeatvceu);

// cabecalho
print $OUTPUT->header();

// template
print $OUTPUT->render_from_template('block_extensao/criar_ambiente', $informacoes_turma);

// rodape
print $OUTPUT->footer();