<?php

namespace block_extensao\Service;

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

  public static function docentesTurmasAbertas () {
    /**
     * Captura os docentes das turmas abertas.
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
  
}