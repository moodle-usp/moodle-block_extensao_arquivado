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
     *
     * Nesse momento tambem, o que nos interessa eh quem tem o
     * tipo de atuacao como 1, 2 ou 5 somente (eh isso mesmo?)
     */
    $hoje = date("Y-m-d");
    $query = "
      SELECT 
            M.codofeatvceu AS 'Codoferecimentoatv'
            ,P.codpes AS 'NUSP'
            ,M.codatc AS 'CodAtuacao'
            ,O.codcurceu AS 'CodCurso'
            ,O.codatvceu AS 'CodAtividade'
            ,O.dtainiofeatv AS 'Inicio'
            ,O.dtafimofeatv AS 'Fim'
            ,C.nomcurceu AS 'NomeCurso'
            ,ED.codcurceu AS 'codCursoExtensao'
            ,ED.codedicurceu  AS 'EdCurso'
            FROM 
            MINISTRANTECEU M
        LEFT JOIN PESSOA P
            ON M.codpes = P.codpes
        RIGHT JOIN OFERECIMENTOATIVIDADECEU O 
           ON M.codofeatvceu = O.codofeatvceu
        RIGHT JOIN CURSOCEU C
          ON C.codcurceu = O.codcurceu
        RIGHT JOIN EDICAOCURSOOFECEU ED
            ON ED.codcurceu = C.codcurceu
        WHERE 
          O.dtafimofeatv > '$hoje'
          AND
          M.codatc = 1
    ";
    return USPDatabase::fetchAll($query);
  }
  
}