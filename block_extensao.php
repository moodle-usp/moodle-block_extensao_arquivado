<?php

require_once('classes/Sybase.php');

use block_extensao\Sybase;

class block_extensao extends block_base {
    public function init() {
        $this->title = get_string('block_title', 'block_extensao');
    }

    public function get_content() {
        global $USER;
        
        $this->content->text = 
		"Acesse a área de administração";

		$url = new moodle_url('/blocks/extensao/view.php');
		$this->content->text .= html_writer::link($url, 'Upload stuffs');

		return $this->content;
    }

}