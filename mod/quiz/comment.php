<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This page allows the teacher to enter a manual grade for a particular question.
 * This page is expected to only be used in a popup window.
 *
 * @package   mod_quiz
 * @copyright gustav delius 2006
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('locallib.php');

$attemptid = required_param('attempt', PARAM_INT);
$slot = required_param('slot', PARAM_INT); // The question number in the attempt.
$cmid = optional_param('cmid', null, PARAM_INT);

$PAGE->set_url('/mod/quiz/comment.php', array('attempt' => $attemptid, 'slot' => $slot));

$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
$attemptobj->preload_all_attempt_step_users();
$student = $DB->get_record('user', array('id' => $attemptobj->get_userid()));

// Can only grade finished attempts.
if (!$attemptobj->is_finished()) {
    print_error('attemptclosed', 'quiz');
}

// Check login and permissions.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
$attemptobj->require_capability('mod/quiz:grade');

// Print the page header.
$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string('manualgradequestion', 'quiz', array(
        'question' => format_string($attemptobj->get_question_name($slot)),
        'quiz' => format_string($attemptobj->get_quiz_name()), 'user' => fullname($student))));
$PAGE->set_heading($attemptobj->get_course()->fullname);
$output = $PAGE->get_renderer('mod_quiz');
echo $output->header();

// Prepare summary information about this question attempt.
$summarydata = array();

// Student name.
$userpicture = new user_picture($student);
$userpicture->courseid = $attemptobj->get_courseid();
$summarydata['user'] = array(
    'title'   => $userpicture,
    'content' => new action_link(new moodle_url('/user/view.php', array(
            'id' => $student->id, 'course' => $attemptobj->get_courseid())),
            fullname($student, true)),
);

// Quiz name.
$summarydata['quizname'] = array(
    'title'   => get_string('modulename', 'quiz'),
    'content' => format_string($attemptobj->get_quiz_name()),
);

// Question name.
$summarydata['questionname'] = array(
    'title'   => get_string('question', 'quiz'),
    // 'content' => "BTP",
    'content' => $attemptobj->get_question_name($slot),
);

// Process any data that was submitted.
if (data_submitted() && confirm_sesskey()) {
    
    if (optional_param('submit', false, PARAM_BOOL) && question_engine::is_manual_grade_in_range($attemptobj->get_uniqueid(), $slot)) {
        $transaction = $DB->start_delegated_transaction();
        $attemptobj->process_submitted_actions(time());
        $transaction->allow_commit();

        // Log this action.
        $params = array(
            'objectid' => $attemptobj->get_question_attempt($slot)->get_question_id(),
            'courseid' => $attemptobj->get_courseid(),
            'context' => context_module::instance($attemptobj->get_cmid()),
            'other' => array(
                'quizid' => $attemptobj->get_quizid(),
                'attemptid' => $attemptobj->get_attemptid(),
                'slot' => $slot
            )
        );
        $event = \mod_quiz\event\question_manually_graded::create($params);
        $event->trigger();

        echo $output->notification(get_string('changessaved'), 'notifysuccess');
        close_window(2, true);
        die;
    }
}

// Print quiz information.
echo $output->review_summary_table($summarydata, 0);

// Print the comment form.
echo '<form method="post" class="mform" id="manualgradingform" action="' .
                                $CFG->wwwroot . '/mod/quiz/comment.php">';
// echo '<input id="id_submitbutton" type="submit" name="submit" class="btn btn-primary" value="annotate"/>';
echo $attemptobj->render_question_for_commenting($slot);
$qa=$attemptobj->get_question_attempt($slot);
$options = $attemptobj->get_display_options(true);
$files = $qa->get_last_qt_files('attachments', $options->context->id);
// var_dump($files);
$fileurl = "";
foreach ($files as $file) {
    $out = $qa->get_response_file_url($file);
    $url = (explode("?", $out))[0];
    $fileurl = $url;
    // var_dump($out);
}



$filename = $fileurl;


// foreach ($files as $file) {
//     $temp = $qa->get_response_file_url($file);
//     // var_dump($temp);
//     $st = explode("?",$temp);
//     var_dump($st);
// }

// file API
// $fs = get_file_storage();

// // // Prepare file record object
// $fileinfo = array(
//     'contextid' => $options->context->id, // ID of context
//     'component' => 'mod_mymodule',     // usually = table name
//     'filearea' => 'myarea',     // usually = table name
//     'itemid' => 9,               // usually = ID of row in table
//     'filepath' => '/',           // any path beginning and ending in /
//     'mimetype' => 'application/pdf',
//     'filename' => 'myfile.pdf'); // any filename

// // // Create file containing text 'hello world'
// $val="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf";
// $fs->create_file_from_url($fileinfo, $val);

// $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
//                       $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);


// $fs->add_file_to_pool('/');


// Read contents
// if ($file) {
//     $contents = $file->get_content();
//     echo "<h1>" . $contents . "</h1>";
//     $file->delete();
// } else {
//     echo "<h1>FILE DOESN'T EXISTS</h1>";
// }

// $out = $qa->get_response_file_url($file);
// $url = (explode("?", $out))[0];
// $var_dump($out);
// $fileurl = $url;
// $filename = $file->get_content();

include "./myindex.html";
?>
<script type="text/javascript">var filename = "<?= $filename ?>";</script>
<script type="text/javascript" src="myscript.js"></script>
<div>
    <input type="hidden" name="attempt" value="<?php echo $attemptobj->get_attemptid(); ?>" />
    <input type="hidden" name="slot" value="<?php echo $slot; ?>" />
    <input type="hidden" name="slots" value="<?php echo $slot; ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
</div>
<!-- <input id="id_submitbutton" type="submit" name="submit" class="btn btn-primary" value="annotate"/> -->
<fieldset class="hidden">
    <div>
        <div class="fitem fitem_actionbuttons fitem_fsubmit">
            <fieldset class="felement fsubmit">
                <input id="id_submitbutton" type="submit" name="submit" class="btn btn-primary" value="<?php
                        print_string('save', 'quiz'); ?>"/>
            </fieldset>
        </div>
    </div>
</fieldset>
<?php
echo '</form>';
$PAGE->requires->js_init_call('M.mod_quiz.init_comment_popup', null, false, quiz_get_js_module());

// End of the page.
echo $output->footer();