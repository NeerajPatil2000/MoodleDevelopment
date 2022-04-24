<?php

// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see .

/**
* This is a one-line short description of the file.
*
* You can have a rather longer description of the file as well,
* if you like, and it can span multiple lines.
*
* @package local_helloworld
* @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require(__DIR__. '/../../config.php');
require_once("$CFG->libdir/formslib.php");
$PAGE->set_url(new moodle_url('/local/helloworld/index.php'));
require_login();
if(isguestuser())
{
  print_error('noguest');
}
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('hellouser', 'local_helloworld', "World"));
$PAGE->navbar->add(get_string('sayhello','local_helloworld'), new moodle_url('/local/helloworld/index.php'));
$id = optional_param('id', -1, PARAM_INT);
if($id != -1)
{
  $sesskey = required_param('id', -1, PARAM_INT);
  require_sesskey();
  $DB->delete_records("local_helloworld_msgs", ['id' => $id]);
}

echo $OUTPUT->header();
class simplehtml_form extends moodleform {
    public function definition() {
        global $CFG;
       
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('textarea','message',null, 'wrap="left" rows="10" cols="200"'); // Add elements to your form
        $mform->setType('message', PARAM_TEXT );                   //Set type of element
        $mform->setDefault('message', 'Type your message');        //Default value
        $this->add_action_buttons($cancel=false,$submitlabel=get_string('submit'));
    }

}

//Instantiate simplehtml_form 
$mform = new simplehtml_form($target=new moodle_url($CFG->httpswwwroot.'/local/helloworld/index.php'));
$toform=[];
global $USER;
$systemcontext = context_system::instance();
$deleteurl = new moodle_url('/local/helloworld/index.php');

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.
    
    $mform->set_data($toform);
    $mform->display();
    
    global $DB;
    
    require_capability('local/helloworld:postmessages',$systemcontext ,$USER->id);
    
      // print_r("Hello");
      if($ins=$DB->get_record('local_helloworld_msgs', ['userid'=>$USER->id]))
      {
        $ins->message = $fromform->message;
        $ins->timecreated =time();
        $DB->update_record('local_helloworld_msgs', $ins,$bulk=true);
      }
      else
      {
        $ins = new stdClass();
        $ins->message = $fromform->message;
        $ins->timecreated =time();
        $ins->userid = $USER->id;
        $ins->id=$DB->insert_record("local_helloworld_msgs", $ins, $returnid=true, $bulk=false);
      }
    
    if(has_capability('local/helloworld:viewmessages',$systemcontext,$USER->id))
    {
      $data=$DB->get_records_sql("SELECT u.username,m.message,m.timecreated,m.userid,m.id FROM {local_helloworld_msgs} m INNER JOIN {user} u ON m.userid = u.id");   
      $out = '';
      $out.=html_writer::start_tag('div',['class'=>"card-columns"]);
      foreach ($data as $entry) 
      { 
        $deleteurlstr = $deleteurl->out(true,
            array('id' => $entry->id,'sesskey' => sesskey()));
        $message = $entry->message;
        $info='- '.$entry->username.','.userdate($entry->timecreated);
        $out.=html_writer::start_tag('div',['class'=>"card"]);
        $out.=html_writer::start_tag('div',['class'=>"card-body"]);
        $out.=html_writer::tag('p',$message,['class'=>"card-text"]);
        $result=html_writer::tag('small',$info,['class'=>"text-muted"]);
        $out.=html_writer::tag('p',$result,['class'=>"card-text"]);
        if(has_capability('local/helloworld:deleteanymessage',$systemcontext,$USER->id))
        {
          $out.= '<a title="' . get_string('delete') . '" href="' . $deleteurlstr . '">' .
          $OUTPUT->pix_icon('t/delete', get_string('delete')) . '</a> ';
        }
        $out.=html_writer::end_tag('div');
        $out.=html_writer::end_tag('div');
      }
      $out.=html_writer::end_tag('div');
      echo $out;
    }
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.

  //Set default data (if any)
  if(has_capability('local/helloworld:viewmessages',$systemcontext ,$USER->id))
  {
    $mform->set_data($toform);
    

    $mform->display();
    global $DB;
  
    $data=$DB->get_records_sql("SELECT u.username,m.message,m.timecreated,m.userid,m.id FROM {local_helloworld_msgs} m INNER JOIN {user} u ON m.userid = u.id");
    $out = '';
    $out.=html_writer::start_tag('div',['class'=>"card-columns"]);
    foreach ($data as $entry) 
    {
      $deleteurlstr = $deleteurl->out(true,
            array('id' => $entry->id)); 
      $message = $entry->message;
      $info='- '.$entry->username.','.userdate($entry->timecreated);
      
      $out.=html_writer::start_tag('div',['class'=>"card"]);
      $out.=html_writer::start_tag('div',['class'=>"card-body"]);
      $out.=html_writer::tag('p',$message,['class'=>"card-text"]);
      $result=html_writer::tag('small',$info,['class'=>"text-muted"]);
      $out.=html_writer::tag('p',$result,['class'=>"card-text"]);
      if(has_capability('local/helloworld:deleteanymessage',$systemcontext,$USER->id))
      {
        $out.= '<a title="' . get_string('delete') . '" href="' . $deleteurlstr . '">' .
        $OUTPUT->pix_icon('t/delete', get_string('delete')) . '</a> ';
      }
      $out.=html_writer::end_tag('div');
      $out.=html_writer::end_tag('div');
    }
    $out.=html_writer::end_tag('div');
    echo $out;
  }

}
echo $OUTPUT->footer();
?>



