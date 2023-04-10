<?php

namespace block_extensao\Service;

use stdClass;

require_once('USPDatabase.php');

class Query 
{
  public static function coursesFromThispYear(){
    $year = date('Y');
    $query = "SELECT codcurceu,nomcurceu,dtainc FROM CURSOCEU WHERE dtainc LIKE '%{$year}%'";
    return USPDatabase::fetchAll($query);
  }

  public static function turmasAbertas () {
    /**
     * Captura as turmas abertas.
     * Sao consideradas como turmas abertas somente as turmas com
     * data de encerramento posterior a data de hoje.
     */
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

  public static function ministrantesTurmasAbertas () {
    /**
     * Captura os ministrantes das turmas abertas.
     * Sao consideradas como turmas abertas somente as turmas com
     * data de encerramento posterior a data de hoje.
     */
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

  public static function informacoesTurma ($codofeatvceu) {
    /**
     * A partir do codofeatvceu, captura as informacoes de uma
     * turma, como a data de inicio e tal.
     * 
     * [ a query sera posta aqui posteriormente ]
     */
    $info_curso = new stdClass;
    $info_curso->codofeatvceu = $codofeatvceu;
    $info_curso->startdate = strtotime("now");
    $info_curso->enddate = strtotime("+1 year");
    return $info_curso;
  }
  
   // Obtem o objetivo do curso explicitado 
  public static function objetivo_extensao($codofeatvceu) {
    $obj = "
    SELECT c.objcur FROM OFERECIMENTOATIVIDADECEU o LEFT JOIN CURSOCEU c ON c.codcurceu = o.codcurceu 
    WHERE codofeatvceu = $codofeatvceu";
    return USPDatabase::fetch($obj);
  }

  public static function alunosMatriculados ($codofeatvceu) {
    /**
     * Captura os alunos matriculados nas turmas abertas.
     * Sao consideradas como tumras abertas somente as turmas com
     * data de encerramento posterior a data de hoje.
     */
    $hoje = date("Y-m-d");
    $query = "
      SELECT 
        ma.codofeatvceu,
        mc.codpes,
        p.nompes
      FROM dbo.MATRICULAATIVIDADECEU ma
      INNER JOIN	
        dbo.MATRICULACURSOCEU mc
        ON mc.codmtrcurceu = ma.codmtrcurceu
      INNER JOIN
        dbo.PESSOA p 
        ON mc.codpes = p.codpes
      WHERE ma.codofeatvceu = $codofeatvceu
    ";
    return USPDatabase::fetchAll($query);
  }
}