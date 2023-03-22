<?php

/**
 * Sincronizar
 * 
 * A ideia desse arquivo eh fazer o servico de sincronizacao. Nao sei bem onde
 * deixa-lo, entao por hora vou deixar aqui. Ele vai chamar a classe de queries 
 * e buscar os dados no Apolo, e em seguida vai salvar as informacoes obtidas
 * dentro da base interna do Moodle.
 * 
 * Depois de tudo isso, seria bom ele voltar pra tela da view com uma mensagem
 * qualquer de 'sucesso' (se der certo).
 * 
 * Seria bom a gente estabelecer um fator para limitar a busca e tal. Buscar as
 * turmas de um ano so pode dar problemas se for generalizado, entao eh bom 
 * fazermos algo bem pensado.
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once('Service/Query.php');
use block_extensao\Service\Query;
 
class Sincronizar {

  private $URL_RETORNO;

  public function __construct() {
    $this->URL_RETORNO = '/blocks/extensao/pages/sincronizar.php';   
    $this->sincronizar();
  }

  // Captura as turmas atuais no Apolo e registra na base Moodle
  public function sincronizar () {
    
    /**
     * Capturar turmas
     * 
     * A ideia eh que essa funcao retorne um array das turmas, trazendo as
     * informacoes relativas aos cursos.
     */
    $turmas = Query::turmasAbertas();
    // monta o array que sera adicionado na mdl_extensao_turma
    $infos_turma = $this->filtrarInfosTurmas($turmas);

    // pega as turmas que nao estao na base
    $infos_turma = $this->turmasNaBase($infos_turma);
    
    // se estiver vazio nao tem por que continuar
    if (empty($infos_turma)) {
      \core\notification::success('A base já estava sincronizada!');
      $url = new moodle_url($this->URL_RETORNO);
      redirect($url);
      // return false;
    }

    // salva na mdl_extensao_turma
    $this->salvarTurmasExtensao($infos_turma);
    
    /**
     * Relacionar usuarios com turmas
     * 
     * eh preciso relacionar o NUSP dos usuarios com seus respectivos
     * cursos, um array tipo:
     * array(
     *  'nusp' => ...,
     *  'id_turma' => ...,
     *  'role' => ... # nesse caso nessa importacao todos vao ser docentes
     * )
     * e ai vamos tambem salvar em uma tabela o seguinte array:
     * array(
     *  'id_turma' => ...,
     *  'id_curso_apolo' => ...,
     *  'nome_curso' => ... 
     * )
     * ai vamos poder relacionar as duas usando 'id_turma'.
     */
    
    // monta o array que sera adicionado na mdl_extensao_usuario_curso
    $infos_docentes_turmas = $this->filtrarInfosDocentesTurmas($turmas);
    
    // salva na mdl_extensao_usuario_curso
    $this->salvarDocentesTurmas($infos_docentes_turmas);

    // retorna a pagina de sincronizar
    \core\notification::success('Atualizado com sucesso!');
    $url = new moodle_url($this->URL_RETORNO);
    redirect($url);
  }

  // Filtra as infos das turmas, condensando somente algumas em outro array
  private function filtrarInfosTurmas ($turmas) {
    return array_map(function($turma) {
      $obj = new stdClass;
      $obj->id_turma_apolo = $turma['EdCurso']; // DUVIDA: eh essa info que queremos mesmo?
      $obj->id_curso_apolo = $turma['codCursoExtensao'];
      $obj->nome_curso_apolo = $turma['NomeCurso'];
      $obj->curso_moodle_criado = 0;
      return $obj;
    }, $turmas);
  }

  // Fila as infos dos docentes e turmas, condensando somente algumas em outro array
  private function filtrarInfosDocentesTurmas ($turmas) {
    return array_map(function($turma) {
      $obj = new stdClass;
      $obj->id_turma_apolo = $turma['EdCurso']; // DUVIDA: eh essa info que queremos mesmo?
      $obj->nusp_usuario = $turma['NUSP'];
      $obj->papel_usuario = $turma['CodAtuacao'];
      return $obj;
    }, $turmas);
  }

  /**
   * Procura as turmas na base para ver se ja constam
   * 
   * O que fazemos no caso de a turma ja constar?
   * Ignorar ou substituir?
   * 
   * por enquanto esta sendo apenas ignorado
   */
  private function turmasNaBase ($turmas) {
    global $DB;

    $turmas_fora_base = array();

    // percorre as turmas e vai procurando na base
    foreach($turmas as $turma) {
      // procura pela turma na base
      $resultado_busca = $DB->record_exists('extensao_turma', array('id_turma_apolo' => $turma->id_turma_apolo));

      // se existir, vamos apenas remover do $turmas...
      if (!$resultado_busca)
        $turmas_fora_base[] = $turma;
    }
    
    return $turmas_fora_base;
  }

  // Salvar as turmas de extensao
  private function salvarTurmasExtensao ($cursos_turmas) {
    global $DB;
    $DB->insert_records('extensao_turma', $cursos_turmas);
  }    
  
  // Salvar as relacoes docente-turma
  private function salvarDocentesTurmas ($docentes_turmas) {
    global $DB;
    $DB->insert_records('extensao_usuario_turma', $docentes_turmas);
  }

}

$sinc = new Sincronizar();