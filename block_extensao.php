<?php

require_once('src/Service/Query.php');
require_once('src/turmas.php');
require_once('vendor/autoload.php');

class block_extensao extends block_base {
    public function init() {
        $this->title = 'USP Extensão';
    }

    public function get_content() {
        global $USER, $OUTPUT;
        $this->content =  new stdClass;

        // precisamos capturar na base Moodle os cursos nos quais o usuario eh docente
        $cursos_usuario = Turmas::docente_turmas($USER->idnumber);

        // array da template
        $info = array(
            'urlPaginaSincronizar' => new moodle_url('/blocks/extensao/pages/sincronizar.php'),
            'sem_cursos' => empty($cursos_usuario),
            'cursos_docente' => $cursos_usuario
        );
        // template
        $this->content->text = $OUTPUT->render_from_template('block_extensao/extensao_block', $info);
        
        return $this->content;
    }

    function has_config(){
        return true;
    }    

}