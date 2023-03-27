<?php

/**
 * Ambientes Moodle
 * 
 * Neste arquivo ficam as questoes relativas a criacao de ambientes
 * Moodle (cursos). Isso eh usado pelos docentes quando ha turmas
 * abertas em seu nome, por exemplo.
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/course/lib.php'); // biblioteca de cursos

class Ambiente {

  /**
   * Para criar um curso usando a api do Moodle, passado um objeto de 
   * curso
   */
  public static function criar_ambiente ($curso) {
    // verifica se o curso ja esta na base
    if (Ambiente::buscar_curso_moodle($curso->shortname)) {
      return false;
    }

    // cria o curso
    $moodle_curso = \create_course($curso);
    return $moodle_curso;
  }

  /**
   * Buscar um curso na base Moodle
   */
  public static function buscar_curso_moodle ($nome_curto) {
    global $DB;

    $curso = $DB->get_record('course', ['shortname' => $nome_curto]);
    return $curso;
  }

  /**
   * Cria um objeto de curso
   */
  public static function criar_objeto_curso ($info_forms) {
    $curso = new stdClass;
    
    $curso->shortname = 'mac032a1b';
    $curso->fullname = $info_forms->fullname;
    $curso->idnumber = '';
    $curso->visible = 1;
    
    $curso->format = '';
    $curso->numsections = '';

    $curso->summary = '';
    $curso->summaryfomart = FORMAT_HTML;

    $curso->startdate = '';
    $curso->enddate = '';
    $curso->timemodified = time();

    $curso->category = 1;

    return $curso;
  }

}