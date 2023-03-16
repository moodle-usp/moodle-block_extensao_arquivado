<?php

require_once('src/Service/Query.php');
require_once('vendor/autoload.php');

class block_extensao extends block_base {
    public function init() {
        $this->title = 'em dev';
    }

    public function get_content() {
        global $USER, $OUTPUT;
        $this->content =  new stdClass;

        // template
        $info = array('urlPaginaSincronizar' => new moodle_url('/blocks/extensao/pages/sincronizar.php'));
        $this->content->text = $OUTPUT->render_from_template('block_extensao/extensao_block', $info);
        
        return $this->content;
    }

    function has_config(){
        return true;
    }    

}