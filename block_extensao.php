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
        global $OUTPUT;

        $this->content =  new stdClass;
        
        // captura o contexto do usuario
        $instancia_contexto = $this->contexto();
        
        // caso nao seja um curso (-1) ou seja o painel (1), lista os cursos
        if ($instancia_contexto == -1 || $instancia_contexto == 1) {
            // lista das turmas nas quais o usuario eh ministrante, se for o caso
            $cursos_usuario = $this->lista_turmas_ministrante();
            
            // array da template
            $info = array(
                'inicio' => true,
                'cursos_docente' => $cursos_usuario 
            );

            // template
            $this->content->text = $OUTPUT->render_from_template('block_extensao/extensao_block', $info);
        } 
        // caso contrario, estara dentro de um curso, entao precisa listar os inscritos
        else {
            // captura o codofeatvceu
            $codofeatvceu = Turmas::codofeatvceu($instancia_contexto)->codofeatvceu;
            // captura os inscritos na turma
            $inscritos_turma = Turmas::inscritos_turma($codofeatvceu);
            $info = array(
                'inscritos' => $inscritos_turma
            );
            
            $this->content->text = $OUTPUT->render_from_template('block_extensao/bloco_curso', $info);
        }
        
        
        return $this->content;
    }

    function has_config(){
        return true;
    }    

    /**
     * Captura o contexto no qual o bloco esta inserido.
     * 
     * @return integer
     */
    private function contexto () {
        global $PAGE;

        if ($PAGE->context->contextlevel != CONTEXT_COURSE)
            return -1;
        else
            return $PAGE->context->get_course_context()->instanceid;
    }

    /**
     * Lista as turmas nas quais o usuario logado eh ministrante e
     * cujos ambientes Moodle ainda nao tiverem sido criados.
     */
    private function lista_turmas_ministrante () {
        global $USER;

        // verifica se o usuario logado tem numero USP
        if (!isset($USER->idnumber) or empty($USER->idnumber))
            return false;

        // eh preciso capturar na base Moodle os cursos nos quais o usuario eh docente e
        // cujo ambiente ainda nao foi criado
        $cursos_usuario = Turmas::docente_turmas($USER->idnumber);

        // para cada curso, cria um formulario
        for ($i = 0; $i < count($cursos_usuario); $i++) {
            // captura o id da turma no plugin de extensao
            $codofeatvceu = $cursos_usuario[$i]['codofeatvceu'];

            // cria um formulario
            $form = new redirecionamento_criacao_ambiente('/blocks/extensao/pages/criar_ambiente.php', array('codofeatvceu' => $codofeatvceu));

            $cursos_usuario[$i]['formulario'] = $form->render();
        }

        return $cursos_usuario;
    }
}