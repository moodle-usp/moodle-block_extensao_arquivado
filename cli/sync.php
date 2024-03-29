<?php
/**
 * Sincronizacao
 * 
 * Para sincronizar a base do Apolo com o Moodle de Cursos de Extensao,
 * utiliza-se este CLI, bastando rodar na pasta raiz:
 * 
 * > php cli/sync.php
 * 
 * No caso de desejar apagar os dados que ja estiverem na base (utilizado
 * geralmente em testes locais), basta passar a opcao --apagar:
 * 
 * > php cli/sync.php --apagar
 * 
 * Os dados ficam armazenados nas tabelas:
 * 1. mdl_extensao_turma:       Dados de turmas;
 * 2. mdl_extensao_ministrante: Dados de ministrantes e suas turmas;
 * 3. mdl_extensao_aluno:       Dados de alunos e suas matriculas.
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');

require_once(__DIR__ . '/../src/Service/Query.php');
use block_extensao\Service\Query;

class Sincronizar {

  public function __construct() {
    // ...
  }

  /**
   * Sincronizacao dos dados entre Apolo e Moodle
   * 
   * @param bool $apagar Para apagar os dados atuais antes de sincronizar
   */
  public function sincronizar ($apagar=false) {
    
    // se quiser substituir, precisa apagar os dados de agora
    if ($apagar) $this->apagar();

    // sincronizando as turmas
    $turmas = $this->sincronizarTurmas();
    // se $turmas for false, eh que a base ja esta sincronizada
    if (!$turmas) return;
  
    // sincronizando os ministrantes
    $this->sincronizarMinistrantes();

    // sincronizando os alunos
    $this->sincronizarAlunos($turmas);

    // retorna a pagina de sincronizar
    echo PHP_EOL . "Atualizado com sucesso!" . PHP_EOL;
  }

  /**
   * Sincronizacao das turmas
   * 
   * @return array|bool
   */
  private function sincronizarTurmas () {
    // captura as turmas
    $turmas = Query::turmasAbertas();

    // monta o array que sera adicionado na mdl_extensao_turma
    $infos_turma = $this->filtrarInfosTurmas($turmas);

    // pega as turmas que nao estao na base
    $infos_turma = $this->turmasNaBase($infos_turma);

    // se estiver vazio nao tem por que continuar
    if (empty($infos_turma)) {
      echo 'A base já estava sincronizada!' . PHP_EOL;
      return false;
    }

    // salva na mdl_extensao_turma
    $this->salvarTurmasExtensao($infos_turma);
    echo 'Turmas sincronizadas...' . PHP_EOL;
    
    return $infos_turma;
  }

  /**
   * Sincronizacao dos ministrantes
   */
  private function sincronizarMinistrantes () {
    // captura os ministrantes
    $ministrantes = Query::ministrantesTurmasAbertas();

    // monta o array que sera adicionado na mdl_extensao_ministrante
    $ministrantes = $this->objetoMinistrantes($ministrantes);

    // salva na mdl_extensao_ministrante
    $this->salvarMinistrantesTurmas($ministrantes);
    echo 'Ministrantes sincronizados...' . PHP_EOL;
  }

  /**
   * Sincronizacao dos alunos
   * 
   * @param array $turmas Lista de turmas
   */
  private function sincronizarAlunos ($turmas) {
    // captura os alunos matriculados em cada turma
    $alunos = [];
    foreach ($turmas as $turma) {
      $aluno = Query::alunosMatriculados($turma->codofeatvceu);
      if (!empty($aluno)) $alunos[] = $aluno;
    }
    if (empty($alunos)) {
      echo 'Sem alunos para sincronizar...' . PHP_EOL;
    } else {
      // monta o array que sera adicionado na mdl_extensao_aluno
      $alunos = $this->objetoAlunos($alunos);

      // salva na mdl_extensao_aluno
      foreach ($alunos as $alunos_turma) 
        $this->salvarAlunosTurmas($alunos_turma);
      echo 'Alunos sincronizados...' . PHP_EOL;
    }
  }

  /**
   * Filtra as infos das turmas, condensando somente algumas em 
   * outro array
   * 
   * @param array $turmas Lista de turmas
   * 
   * @return array
   */
  private function filtrarInfosTurmas ($turmas) {
    return array_map(function($turma) {
      $obj = new stdClass;
      $obj->codofeatvceu = $turma['codofeatvceu'];
      $obj->nome_curso_apolo = $turma['nomcurceu'];
      return $obj;
    }, $turmas);
  }

  /**
   * Cria objetos para os arrays
   * 
   * @param array $ministrantes Lista de ministrantes
   * 
   * @return array
   */ 
  private function objetoMinistrantes ($ministrantes) {
    return array_map(function($ministrante) {
      $obj = new stdClass;
      $obj->codofeatvceu = $ministrante['codofeatvceu'];
      $obj->codpes = $ministrante['codpes'];
      $obj->papel_usuario = $ministrante['codatc'];
      return $obj;
    }, $ministrantes);
  }

  /**
   * Cria objetos para os alunos
   * 
   * @param array $alunos Lista de alunos
   * 
   * @return array
   */
  // Cria objetos para os alunos
  private function objetoAlunos ($alunos) {
    return array_map(function($alunos_turma) {
      // percorre os alunos de uma turma
      return array_map(function($aluno) {
        $obj = new stdClass;
        $obj->codofeatvceu = $aluno['codofeatvceu'];
        $obj->codpes = $aluno['codpes'];
        $obj->email = "";
        $obj->nome = $aluno['nompes'];
        return $obj;
      }, $alunos_turma);
    }, $alunos);
  }

  /**
   * Procura as turmas na base para ver se ja constam
   * O que fazemos no caso de a turma ja constar?
   * Ignorar ou substituir? por enquanto esta sendo 
   * apenas ignorado
   * 
   * @param array $turmas Lista de turmas
   * 
   * @return array
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

  /**
   * Para salvar as turmas de extensao.
   * 
   * @param array $cursos_turmas Turmas dos cursos.
   */
  private function salvarTurmasExtensao ($cursos_turmas) {
    global $DB;
    $DB->insert_records('extensao_turma', $cursos_turmas);
  }    
  
  /**
   * Para salvar as relacoes entre ministrante e turma
   * 
   * @param array $ministrantes Lista de ministrantes
   */
  private function salvarMinistrantesTurmas ($ministrantes) {
    global $DB;
    $DB->insert_records('extensao_ministrante', $ministrantes);
  }

  /**
   * Para salvar as relacoes entre aluno e turma.
   * 
   * @param array $alunos Lista de alunos.
   */
  private function salvarAlunosTurmas ($alunos) {
    global $DB;
    $DB->insert_records('extensao_aluno', $alunos);
  }

  /**
   * Para apgar as informacoes existentes na base do
   * Moodle.
   */
  private function apagar () {
    global $DB;

    $DB->delete_records('extensao_turma', array('id_moodle' => NULL));
    $DB->delete_records('extensao_ministrante');
    $DB->delete_records('extensao_aluno');
  }
}

// opcoes
$opcoes = getopt("", ["apagar"]);
$sinc = new Sincronizar();

// caso passe a opcao de susbstituir os dados da base atual
if (isset($opcoes["apagar"])) {
  // apaga os dados atuais
  $sinc->sincronizar(true);
} else {
  $sinc->sincronizar();
}

echo 'Sincronização encerrada.' . PHP_EOL;