<?php

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');

require_once(__DIR__ . '/../src/Service/Query.php');
use block_extensao\Service\Query;
 
class Sincronizar {

  public function __construct() {
    // ...
  }

  // Captura as turmas atuais no Apolo e registra na base Moodle
  public function sincronizar ($substituir=false) {
    
    /**
     * Capturar turmas
     * 
     * A ideia eh que essa funcao retorne um array das turmas, trazendo as
     * informacoes relativas aos cursos.
     */
    
    // se quiser substituir, precisa apagar os dados de agora
    if ($substituir) $this->apagar();

    $turmas = Query::turmasAbertas();

    // monta o array que sera adicionado na mdl_extensao_turma
    $infos_turma = $this->filtrarInfosTurmas($turmas);

    // pega as turmas que nao estao na base
    $infos_turma = $this->turmasNaBase($infos_turma);
    
    // se estiver vazio nao tem por que continuar
    if (empty($infos_turma)) {
      echo "A base já estava sincronizada!" . PHP_EOL;
      return;
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
    // captura os docentes
    $docentes = Query::docentesTurmasAbertas();

    // monta o array que sera adicionado na mdl_extensao_usuario_curso
    $docentes = $this->objetoDocentes($docentes);
    
    // salva na mdl_extensao_usuario_curso
    $this->salvarDocentesTurmas($docentes);

    // retorna a pagina de sincronizar
    echo "Atualizado com sucesso!" . PHP_EOL;
  }

  // Filtra as infos das turmas, condensando somente algumas em outro array
  private function filtrarInfosTurmas ($turmas) {
    return array_map(function($turma) {
      $obj = new stdClass;
      $obj->codofeatvceu = $turma['codofeatvceu'];
      $obj->nome_curso_apolo = $turma['nomcurceu'];
      return $obj;
    }, $turmas);
  }

  // Cria objetos para os arrays
  private function objetoDocentes ($docentes) {
    return array_map(function($docente) {
      $obj = new stdClass;
      $obj->codofeatvceu = $docente['codofeatvceu'];
      $obj->codpes = $docente['codpes'];
      $obj->papel_usuario = $docente['codatc'];
      return $obj;
    }, $docentes);
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
      $resultado_busca = $DB->record_exists('extensao_turma', array('codofeatvceu' => $turma->codofeatvceu));

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
  private function salvarDocentesTurmas ($docentes) {
    global $DB;
    $DB->insert_records('extensao_ministrante', $docentes);
  }

  // Apagar informacoes salvas atualmente
  private function apagar () {
    global $DB;

    $DB->delete_records('extensao_turma', array('id_moodle' => NULL));
    $DB->delete_records('extensao_ministrante');
  }
}

$opcoes = getopt("", ["substituir"]);
$sinc = new Sincronizar();

// caso passe a opcao de susbstituir os dados da base atual
if (isset($opcoes["substituir"])) {
  // apaga os dados atuais
  $sinc->sincronizar(true);
} else {
  $sinc->sincronizar();
}

echo 'Sincronização encerrada.' . PHP_EOL;