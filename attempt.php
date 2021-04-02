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
 * This page displays an attempt of practice module.
 *
 * @package    block_qbpractice
 * @copyright  2021 Jan Kłudkiewicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 // Libraries
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(dirname(__FILE__) . '/attemptlib.php');
require_once(dirname(__FILE__) . '/locallib.php');

// Required parameters
$sessionid = required_param('id', PARAM_INT); // Session id

// Optional parameters
$slot = optional_param('slot', null, PARAM_INT);
$previous = optional_param('previous', null, PARAM_BOOL);
$next = optional_param('next', null, PARAM_BOOL);
$finish = optional_param('finish', null, PARAM_BOOL);
$finishreview = optional_param('finishreview', null, PARAM_BOOL);

// Qbpractice session, course and context variables
$session = $DB->get_record('qbpractice_session', array('id' => $sessionid));
$context = context_block::instance($session->instanceid);
$courseid = $context->get_parent_context()->instanceid;

// Page custiomization
$PAGE->set_url('/blocks/qbpractice/attempt.php', array('id' => $sessionid));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$title = get_string('practicesession', 'block_qbpractice');
$PAGE->set_heading($title);
$PAGE->requires->css('/blocks/qbpractice/style/attemptstyle.css?v='.rand());
$PAGE->requires->js('/blocks/qbpractice/attempt_script.js?v='.rand());

// Security functions
require_login($courseid);
require_capability('block/qbpractice:use', $context);

// Load question usage for session
$quba = question_engine::load_questions_usage_by_activity($session->questionusageid);

// Get slots numbers (current, previous, next)
if (!$slot) $slot = get_first_active_question($quba);
$previousslot = get_previous_question_slot($quba, $slot);
$nextslot = get_next_question_slot($quba, $slot);

// All URLs for navigation
$previousurl = new moodle_url('/blocks/qbpractice/attempt.php', array('id' => $sessionid, 'slot' => $previousslot));
$currenturl = new moodle_url('/blocks/qbpractice/attempt.php', array('id' => $sessionid, 'slot' => $slot));
$nexturl = new moodle_url('/blocks/qbpractice/attempt.php', array('id' => $sessionid, 'slot' => $nextslot));
$finishurl = new moodle_url('/blocks/qbpractice/summary.php', array('id' => $session->instanceid));

// Form processing
if (data_submitted()) {
	
	if ($previous) {
		
        redirect($previousurl);
		
	} else if ($next) {	
		
        redirect($nexturl);
		
    } else if ($finish) {
		
		qbpractice_session_finish();
        redirect($finishurl);
		
	} else if ($finishreview) {
		
		redirect($finishurl);
		
    } else {
		
		$quba->process_all_actions();
		$quba->finish_question($slot);
		question_engine::save_questions_usage_by_activity($quba);
		
	}
	
}

//Question display settings
$options = new question_display_options();
$options->flags = question_display_options::EDITABLE;
$options->correctness = question_display_options::VISIBLE;
$options->marks = question_display_options::HIDDEN;
if (has_capability('moodle/question:editall', $context)) $options->editquestionparams = array('courseid' => $courseid, 'returnurl' => $currenturl);

// Add navigation panel
$navbc = get_navigation_panel($session, $quba, $slot, $currenturl);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

// Important for flags to work
$headtags = '';
$headtags .= $quba->render_question_head_html($slot);
$headtags .= question_engine::initialise_js();

// Start the question form.
$html = html_writer::start_tag('form', array('method' => 'post', 'action' => $currenturl, 'enctype' => 'multipart/form-data', 'id' => 'responseform'));
$html = html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'questionid', 'value' => $quba->get_question_attempt($slot)->get_question_id()));

// Output the question.
$html .= $quba->render_question($slot, $options, $slot);

// Form action buttons
$html .= html_writer::start_tag('div', array('class' => 'navigation_buttons'));
if ($slot != 1) $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'previous', 'value' => get_string('previousquestion', 'block_qbpractice'), 'class' => 'navigation_button'));
if ($slot != end($quba->get_slots())) $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'next', 'value' => get_string('nextquestion', 'block_qbpractice'), 'class' => 'navigation_button'));
else if ($session->status == "finished") $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'finishreview', 'value' => get_string('finishreview', 'block_qbpractice')));
else $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'finish', 'value' => get_string('finishsession', 'block_qbpractice')));
$html .= html_writer::end_tag('div');
$html .= html_writer::end_tag('form');

$js = "document.querySelectorAll('input.questionflagvalue').addEventListener('change', flipFlag);
		function flipFlag() {
			var flag = document.querySelectorAll('input.questionflagvalue');
			var newstate = flag.value=='1'?true:false;
			var qid = document.getElementById('questionid').value;
			var toggleurl = 'toggleflag.php?qid='+qid+'&newstate='+newstate
			$ajax({url: toggleurl});
			}";
$html .= html_writer::script($js);

// Final output
echo $OUTPUT->header();

echo $html;

echo $OUTPUT->footer();

