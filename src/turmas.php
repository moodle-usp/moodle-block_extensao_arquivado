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
    global $DB;
    
    // captura as turmas relacionadas ao usuario
    $query = "SELECT codofeatvceu FROM {extensao_ministrante} WHERE codpes = $nusp_docente AND papel_usuario IN (1,2,5)";
    $usuario_turma = $DB->get_records_sql($query, ['codpes' => $nusp_docente]);

    $cursos_usuario = array();
    // captura os nomes dos cursos relacionados
    foreach ($usuario_turma as $turma) {
      $busca = $DB->get_record('extensao_turma', ['codofeatvceu' => $turma->codofeatvceu]);

      if ($busca->id_moodle) continue;

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

  /**
   * Verifica se uma turma no extensao_turma ja teve um ambiente criado
   */
  public static function ambiente_criado_turma ($codofeatvceu) {
    global $DB;
    $query = $DB->get_record('extensao_turma', ['codofeatvceu' => $codofeatvceu]);
    return $query->id_moodle;
  }

  // Atualiza o codigo da turma no Moodle
  /**
   * OBS: parece que do jeito que a gente fez, sem o id, nao funciona a 
   * api do moodle! 
   */
  public static function atualizar_id_moodle_turma ($codofeatvceu, $id_moodle) {
    global $DB;
    $query = "UPDATE {extensao_turma} SET id_moodle = $id_moodle WHERE codofeatvceu = $codofeatvceu";
    $query = $DB->execute($query);
    return $query;
  }
}