<?php

/**
 * Sincronizacao Provisoria
 * 
 * Pagina para fazer a sincronizacao com o Apolo enquanto ela nao eh
 * feita automaticamente de outra forma.
 */

// Configuracoes da pagina
require_once('../../../config.php');
global $PAGE, $OUTPUT;

$PAGE->set_heading(get_string('pluginname', 'block_extensao'));
$PAGE->set_pagelayout('admin');
require_login();

// cabecalho
print $OUTPUT->header();

// template
$info = array(
  'urlSincronizar' => new moodle_url('/blocks/extensao/src/sincronizar.php'),
  'urlCancelar' => new moodle_url($CFG->wwwroot)
);
print $OUTPUT->render_from_template('block_extensao/sincronizar', $info);

// rodape
print $OUTPUT->footer();