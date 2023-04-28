<?php
/**
 * Aqui ficam as queries que interagem com o Apolo. Assim,
 * toda busca de dados na base do Apolo devera ser uma funcao
 * estatica da classe Query.
 */

namespace block_extensao\Service;

use stdClass;

require_once('USPDatabase.php');

class Query 
{
  /**
   * Captura as turmas abertas.
   * Sao consideradas como turmas abertas somente as turmas com
   * data de encerramento posterior a data de hoje.
   * 
   * @return array
   */
  public static function turmasAbertas () {
    $hoje = date("Y-m-d");
    $query = "
      SELECT
        o.codofeatvceu
        ,c.nomcurceu
        ,o.dtainiofeatv
        ,o.dtafimofeatv
      FROM OFERECIMENTOATIVIDADECEU o
          LEFT JOIN CURSOCEU c
            ON c.codcurceu = o.codcurceu
          LEFT JOIN EDICAOCURSOOFECEU e
            ON o.codcurceu = e.codcurceu AND o.codedicurceu = e.codedicurceu
      WHERE e.dtainiofeedi >= '$hoje'
      ORDER BY codofeatvceu 
    ";

    return USPDatabase::fetchAll($query);
  }

  /**
   * Captura os ministrantes das turmas abertas.
   * Sao consideradas como turmas abertas somente as turmas com
   * data de encerramento posterior a data de hoje.
   * 
   * @return array
   */
  public static function ministrantesTurmasAbertas () {
    $hoje = date("Y-m-d");
    $query = "
      SELECT
        m.codofeatvceu
        ,m.codpes
        ,m.codatc
      FROM dbo.MINISTRANTECEU m
      WHERE codpes IS NOT NULL
        AND m.dtainimisatv >= '$hoje'
      ORDER BY codofeatvceu
    ";

    return USPDatabase::fetchAll($query);
  }

  /**
   * A partir do codofeatvceu, captura as informacoes de uma
   * turma, como a data de inicio e tal.
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return object
   */
  public static function informacoesTurma ($codofeatvceu) {
    // faz a busca no Apolo
    $query = "
      SELECT
        dtainiofeatv,
        dtafimofeatv
      FROM OFERECIMENTOATIVIDADECEU
      WHERE codofeatvceu = $codofeatvceu
    ";
    $info = USPDatabase::fetch($query);

    $info_curso = new stdClass;
    $info_curso->codofeatvceu = $codofeatvceu;
    $info_curso->startdate = strtotime($info['dtainiofeatv']);
    $info_curso->enddate = strtotime($info['dtafimofeatv']);
    return $info_curso;
  }
  
  /**
   * Obtem o objetivo do curso informado.
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return array
   */
   // Obtem o objetivo do curso explicitado 
  public static function objetivo_extensao($codofeatvceu) {
    $obj = "
    SELECT c.objcur FROM OFERECIMENTOATIVIDADECEU o LEFT JOIN CURSOCEU c ON c.codcurceu = o.codcurceu 
    WHERE codofeatvceu = $codofeatvceu";
    return USPDatabase::fetch($obj);
  }

  /**
   * Captura os alunos matriculados em uma determinada turma a
   * partir de seu codofeatvceu. Retorna uma lista de objetos
   * na forma:
   * - codofeatvceu;
   * - codpes;
   * - nompes;
   * - codema.
   * 
   * @param string|integer $codofeatvceu Codigo de oferecimento da atividade.
   * 
   * @return array
   */
  public static function alunosMatriculados ($codofeatvceu) {
    $query = "
      SELECT 
        ma.codofeatvceu,
        mc.codpes
      FROM dbo.MATRICULAATIVIDADECEU ma
      INNER JOIN
        dbo.MATRICULACURSOCEU mc
        ON mc.codmtrcurceu = ma.codmtrcurceu
      WHERE
        ma.codofeatvceu = $codofeatvceu
    ";
    return USPDatabase::fetchAll($query);
  }

  public static function infoAlunosMatriculados ($codpes) {
    $query = "
      SELECT
        p.codpes,
        p.nompes,
        p.numcpf,
        em.codema
      FROM dbo.PESSOA p
      INNER JOIN
        dbo.EMAILPESSOA em
        ON em.codpes = p.codpes
      WHERE
        p.codpes = $codpes
    ";
    return USPDatabase::fetch($query);   
  }
}