<?php

require_once('classes/Sybase.php');

use block_extensao\Sybase;

class block_extensao extends block_base {
    public function init() {
        $this->title = get_string('block_title', 'block_extensao');
    }

    public function get_content() {
        global $USER;
        
        $this->content =  new stdClass;

        $this->content->text = '';

        return $this->content;
    }

}