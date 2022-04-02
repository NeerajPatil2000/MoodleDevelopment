<?php

class block_quizblock extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_quizblock');
    }


    function get_content() {
        global $DB;

        if($this->content !== NULL) {
            return $this->content;
        }
        $quizstring='';
        $quizes=$DB->get_records('quiz');

        foreach ($quizes as $quiz) {
            $quizstring .= $quiz->name . '<br>';
        }


        $this->content = new stdClass;
        $this->content->text=$quizstring;
        
        return $this->content;
    }
}
