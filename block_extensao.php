<?php

require_once('src/Service/Query.php');
require_once('vendor/autoload.php');

use block_extensao\Service\Query;

use Carbon\Carbon;

class block_extensao extends block_base {
    public function init() {
        $this->title = 'em dev';
    }

    public function get_content() {
        global $USER;
        
        $this->content =  new stdClass;

        $cursos = Query::coursesFromThispYear();
        $this->content->text = "$USER->idnumber ";
        foreach($cursos as $curso) {
            $date = Carbon::parse($curso['dtainc'])->format('d/m/Y');
            $this->content->text .= "<li>{$curso['nomcurceu']} {$date} </li>";
        }    

        return $this->content;
    }

    function has_config(){
        return true;
    }    

}