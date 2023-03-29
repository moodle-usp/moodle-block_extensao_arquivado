<?php

/**
 * Apolo
 * 
 * A ideia desse arquivo eh servir de ponte entre o Service/Query e o restante do plugin.
 * Ele contem algumas funcoes voltadas para a busca de dados dentro da base do Apolo,
 * sem a necessidade de chamar diretamente a classe de queries dentro de uma view, por
 * exemplo.
 */

require_once(__DIR__ . '/Service/Query.php');
use block_extensao\Service\Query;

class Apolo {

  public static function informacoesTurma ($codofeatvceu) {
    /**
     * A partir do codofeatvceu, captura as informacoes de uma
     * turma, como a data de inicio e tal.
     */
    return Query::informacoesTurma($codofeatvceu);
  }

}