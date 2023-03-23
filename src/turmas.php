<?php

/**
 * Turmas
 * 
 * A ideia desse arquivo eh mexer com turmas dentro da base do Moodle. Tambem nao sei
 * bem onde deixa-lo, entao por hora vai ficar aqui. Ele vai vasculhar na base de dados
 * do Moodle e trazer informacoes relevantes, a depender da funcao.
 */

// require_once('../../../config.php');

class Turmas {
  
  // Captura as turmas relativas a um docente na base do Moodle
  public static function docente_turmas ($nusp_docente) {

    /**
     * Docente Turmas
     * 
     * A ideia eh que essa funcao retorne um array dasd turmas, trazendo
     * as informacoes relativas aos cursos.
     */

    global $DB;
    
    // captura as turmas relacionadas ao usuario
    $query = "SELECT codofeatvceu FROM {extensao_ministrante} WHERE codpes = $nusp_docente AND papel_usuario IN (1,2,5)";
    $usuario_turma = $DB->get_records_sql($query, ['codpes' => $nusp_docente]);

    $cursos_usuario = array();
    // captura os nomes dos cursos relacionados
    foreach ($usuario_turma as $turma) {
      $busca = $DB->get_record('extensao_turma', ['codofeatvceu' => $turma->codofeatvceu]);

      $cursos_usuario[] = array(
        'codofeatvceu' => $turma->codofeatvceu,
        'nome_curso_apolo' => $busca->nome_curso_apolo
      );
    }
    
    return $cursos_usuario;
  }

  // Captura as informacoes de uma turma passado o id no plugin Extensao
  public static function info_turma_id_extensao ($codofeatvceu) {
    global $DB;
    $infos = $DB->get_record('extensao_turma', ['codofeatvceu' => $codofeatvceu]);
    return $infos;
  }

  // Verifica se uma turma esta associada a um usuario
  public static function usuario_docente_turma ($nusp_usuario, $codofeatvceu) {
    global $DB;
    // captura o id apolo da turma na base do extensao
    $info_turma = $DB->get_record('extensao_turma', ['codofeatvceu' => $codofeatvceu]);
    // agora ve se esta associada ao usuario
    $query = "SELECT * FROM {extensao_ministrante} WHERE codofeatvceu = $info_turma->codofeatvceu AND codpes = $nusp_usuario";
    $turma_associada = $DB->get_record_sql($query);
    return !empty($turma_associada);
  }

}