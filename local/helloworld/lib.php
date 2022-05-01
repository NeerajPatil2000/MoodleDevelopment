<?php
/**
 * Add link to index.php into navigation drawer.
 *      
 * @param global_navigation $nav Node representing the global navigation tree.
 */

defined('MOODLE_INTERNAL') || die();
function local_helloworld_extend_navigation_frontpage(navigation_node $nav,stdClass $course, context_course $context) {
    if(isloggedin() and !isguestuser())
    {
        $show=false;
        if(get_config('local_helloworld', 'showinflatnavigation'))
        {
            $show=true;
        }
        else{
            $show=false;
        }
        $node = $nav->add(get_string('pluginname','local_helloworld'),'/local/helloworld/', navigation_node::NODETYPE_LEAF,null,null, new pix_icon('t/message', 'HelloWorld'));
        $node->nodetype=1;
        $node->collapse=false;
        $node->forceopen=true;
        $node->isexpandable=false;
        $node->showinflatnavigation=$show;
    }
}

