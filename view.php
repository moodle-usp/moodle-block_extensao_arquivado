<?php

require_once('../../config.php');
require_once('uploadform.php');

$uploadform = new uploadform();

global $DB;
global $PAGE, $OUTPUT;

$url = new moodle_url("/blocks/extensao/view.php");
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('admin');
require_login();

$PAGE->set_pagelayout('standard');

$data= [
	'title' => 'Plugin de importação de cursos do replicado para o moodle',
	'uploadform' => $uploadform->render(),
	'records' => $records,
];
	

print $OUTPUT->header();
print $OUTPUT->render_from_template('block_extensao/view', $data);
print $OUTPUT->footer();