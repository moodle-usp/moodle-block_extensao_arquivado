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
require_once(__DIR__ . '/turmas.php');

class Ambiente {

  /**
   * Para criar um curso usando a api do Moodle, passado um objeto de 
   * curso.
   */
  public static function criar_ambiente ($codofeatvceu, $curso) {
    // verifica se o curso ja esta na base
    if (Turmas::ambiente_criado_turma($codofeatvceu)) {
      return false;
    }

    // cria o curso
    $moodle_curso = \create_course($curso);
    return $moodle_curso;
  }

  /**
   * Cria um objeto de curso
   */
  public static function criar_objeto_curso ($info_forms, $info_curso_apolo) {
    $curso = new stdClass;
    
    $curso->shortname = $info_forms->shortname;
    $curso->fullname = $info_forms->fullname;
    $curso->idnumber = ''; //?
    $curso->visible = 1;
    
    $curso->format = 'topics'; //?
    $curso->numsections = ''; //?

    $curso->summary = $info_forms->summary; 
    $curso->summaryfomart = FORMAT_HTML;

    $curso->startdate = $info_curso_apolo->startdate;
    $curso->enddate = $info_curso_apolo->enddate;
    $curso->timemodified = time();

    $curso->category = 1; //?

    return $curso;
  }

}