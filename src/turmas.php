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
    $query = "SELECT id_turma_apolo FROM {extensao_usuario_turma} WHERE nusp_usuario = $nusp_docente AND papel_usuario IN (1,2,5)";
    $usuario_turma = $DB->get_records_sql($query, ['nusp_usuario' => $nusp_docente]);

    $cursos_usuario = array();
    // captura os nomes dos cursos relacionados
    foreach ($usuario_turma as $turma) {
      $busca = $DB->get_record('extensao_turma', ['id_turma_apolo' => $turma->id_turma_apolo]);

      $cursos_usuario[] = array(
        'id_turma_extensao' => $busca->id,
        'id_turma_apolo' => $turma->id_turma_apolo,
        'id_curso_apolo' => $busca->id_curso_apolo,
        'nome_curso_apolo' => $busca->nome_curso_apolo
      );
    }
    
    return $cursos_usuario;
  }

  // Captura as informacoes de uma turma passado o id no plugin Extensao
  public static function info_turma_id_extensao ($id_extensao) {
    global $DB;
    $infos = $DB->get_record('extensao_turma', ['id' => $id_extensao]);
    return $infos;
  }

  // Verifica se uma turma esta associada a um usuario
  public static function usuario_docente_turma ($nusp_usuario, $id_extensao) {
    global $DB;
    // captura o id apolo da turma na base do extensao
    $info_turma = $DB->get_record('extensao_turma', ['id' => $id_extensao]);
    // agora ve se esta associada ao usuario
    $query = "SELECT * FROM {extensao_usuario_turma} WHERE id_turma_apolo = $info_turma->id_turma_apolo AND nusp_usuario = $nusp_usuario";
    $turma_associada = $DB->get_record_sql($query);
    return !empty($turma_associada);
  }

}