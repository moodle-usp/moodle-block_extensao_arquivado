<?php

require_once('src/Service/Query.php');
require_once('src/turmas.php');
require_once('vendor/autoload.php');
require_once('utils/forms.php');

class block_extensao extends block_base {
    public function init() {
        $this->title = 'USP Extensão';
    }

    public function get_content() {
        global $USER, $OUTPUT;
        $this->content =  new stdClass;
        // caso nao esteja logado
        if (!isset($USER->idnumber)) return;

        // precisamos capturar na base Moodle os cursos nos quais o usuario eh docente
        $cursos_usuario = Turmas::docente_turmas($USER->idnumber);

        // para cada curso, cria um formulario
        for ($i = 0; $i < count($cursos_usuario); $i++) {
            // captura o id da turma no plugin de extensao
            $id_extensao = $cursos_usuario[$i]['id_turma_extensao'];

            // cria um formulario
            $form = new redirecionamento_criacao_ambiente('/blocks/extensao/pages/criar_ambiente.php', array('id_turma_extensao' => $id_extensao));

            $cursos_usuario[$i]['formulario'] = $form->render();
        }

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