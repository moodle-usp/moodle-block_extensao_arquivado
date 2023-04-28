<?php

/**
 * Aqui eh feita a sincronizacao de fato, constam as funcoes de
 * salvar dados, gerar objetos, buscar na Query, etc.
 * 
 * A classe Sincronizar eh chamada em:
 * - cli/sync.php
 */

require_once('Query.php');
use block_extensao\Service\Query;

class Sincronizar {

  /**
   * Apenas para nao inicializar com o metodo homonimo.
   */
  public function __construct() {}

  /**
   * Sincronizacao dos dados entre Apolo e Moodle
   * 
   * @param bool $apagar Para apagar os dados atuais antes de sincronizar
   */
  public function sincronizar ($parametros) {
    // se quiser substituir, precisa apagar os dados de agora
    if ($parametros['apagar']) $this->apagar();

    // sincronizando as turmas
    $turmas = $this->sincronizarTurmas();
    // se $turmas for false, eh que a base ja esta sincronizada
    if (!$turmas) return;
  
    if (!$parametros['pular_ministrantes']) {
      // sincronizando os ministrantes
      $this->sincronizarMinistrantes();
    }

    if (!$parametros['pular_alunos']) {
      // sincronizando os alunos
      $this->sincronizarAlunos($turmas);
    }

    // retorna a pagina de sincronizar
    cli_writeln(PHP_EOL . "Atualizado com sucesso!");
  }

  /**
   * Sincronizacao das turmas
   * 
   * @return array|bool
   */
  private function sincronizarTurmas () {
    cli_writeln(PHP_EOL . '[TURMAS]' . PHP_EOL . '# Capturando turmas...');

    // captura as turmas
    $turmas = Query::turmasAbertas();

    // monta o array que sera adicionado na mdl_extensao_turma
    $infos_turma = $this->filtrarInfosTurmas($turmas);

    // pega as turmas que nao estao na base
    $infos_turma = $this->turmasNaBase($infos_turma);

    // se estiver vazio nao tem por que continuar
    if (empty($infos_turma)) {
      cli_writeln('(X) A base já estava sincronizada!');
      return false;
    }

    try {
      cli_writeln('* Foram encontradas ' . count($infos_turma) . ' turmas!');
      // salva na mdl_extensao_turma
      cli_writeln('# Salvando turmas...');
      $this->salvarTurmasExtensao($infos_turma);
      cli_writeln('* Turmas sincronizadas!');
      return $infos_turma;
    } catch (Exception $e) {
      $this->mensagemErro('ERRO AO SINCRONIZAR AS TURMAS:', $e, true);
    }
  }

  /**
   * Sincronizacao dos ministrantes
   */
  private function sincronizarMinistrantes () {
    cli_writeln(PHP_EOL . '[MINISTRANTES]' . PHP_EOL . '# Capturando ministrantes...');
    // captura os ministrantes
    $ministrantes = Query::ministrantesTurmasAbertas();
    cli_writeln('* Foram encontrados ' . count($ministrantes) . ' ministrantes!');

    // monta o array que sera adicionado na mdl_extensao_ministrante
    cli_writeln('# Criando objetos...');
    $ministrantes = $this->objetoMinistrantes($ministrantes);
    
    // salva na mdl_extensao_ministrante
    try {
      cli_writeln('# Salvando ministrantes...');
      $this->salvarMinistrantesTurmas($ministrantes);
      cli_writeln('* Ministrantes sincronizados!');
      return true;
    } catch (Exception $e) {
      $this->mensagemErro('ERRO AO SINCRONIZAR OS MINISTRANTES:', $e, true);
    }
  }

  /**
   * Sincronizacao dos alunos
   * 
   * @param array $turmas Lista de turmas
   */
  private function sincronizarAlunos ($turmas) {
    // captura os alunos matriculados em cada turma
    $alunos = [];
    $qntdAlunos = 0;
    $t0 = time();
    cli_writeln(PHP_EOL . '[ALUNOS]' . PHP_EOL . '# Capturando alunos...');

    foreach ($turmas as $turma) {
      $aluno = Query::alunosMatriculados($turma->codofeatvceu);
      if (!empty($aluno)) { 
        $alunos[] = $aluno;
        $qntdAlunos += count($aluno);
      }
    }
    if (empty($alunos)) { 
      cli_writeln('Sem alunos para sincronizar...');
    } else {
      // calculos
      $qntdTurmas = count($turmas);
      $qntdTurmas_comAlunos = count($alunos);

      cli_writeln("* Foram encontrados {$qntdAlunos} alunos!");
      cli_writeln("* $qntdTurmas_comAlunos / $qntdTurmas das turmas tiveram alguma inscricao encontrada.");
      
      // agora captura as informacoes de cada aluno
      cli_writeln('# Capturando informações dos alunos');

      $lista_alunos = [];
      foreach ($alunos as $alunos_turma) {
        $lista = [];
        foreach ($alunos_turma as $aluno) {
          $info = Query::infoAlunosMatriculados($aluno['codpes']);
          $info_aluno = array_merge($aluno, $info);
          $lista[] = $info_aluno;
        }
        $lista_alunos[] = $lista;
        echo round(count($lista_alunos)/$qntdTurmas_comAlunos*100) . '%...';
      }

      cli_writeln(PHP_EOL . '# Criando objetos...');
      // monta o array que sera adicionado na mdl_extensao_aluno
      $alunos = $this->objetoAlunos($lista_alunos);
      
      try {
        // salva na mdl_extensao_aluno
        cli_writeln('# Salvando alunos...');
        foreach ($alunos as $alunos_turma) 
          $this->salvarAlunosTurmas($alunos_turma);
        cli_writeln('* Alunos sincronizados!');
      } catch (Exception $e) {
        $this->mensagemErro('ERRO AO SINCRONIZAR OS ALUNOS:', $e, true);
      }

    }
    // tempo de processamento
    cli_writeln('Tempo (alunos): ' . (time() - $t0) . 's');
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
  private function objetoAlunos ($alunos) {
    return array_map(function($alunos_turma) {
      // percorre os alunos de uma turma
      return array_map(function($aluno) {
        $obj = new stdClass;
        $obj->codofeatvceu = $aluno['codofeatvceu'];
        $obj->codpes = $aluno['codpes'];
        $obj->numcpf = $aluno['numcpf'];
        $obj->email = $aluno['codema'];
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

  /**
   * Para exibir mensagens de erro.
   * @param string $aviso Aviso que precede a mensagem de erro.
   * @param string $erro  Excecao de erro gerada pelo PHP.
   * @param bool   $parar Se quer que a mensagem seja um die() ou um nao.
   */
  private function mensagemErro ($aviso, $erro, $parar) {
    $msg = 'XXXXXXX' . PHP_EOL . $aviso . PHP_EOL . $erro . PHP_EOL . 'XXXXXXX' . PHP_EOL;
    if ($parar)
      die($msg);
    else
      echo $msg;
  }
}