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
 * Back-end code for handling data about quizzes and the current user's attempt.
 *
 * There are classes for loading all the information about a quiz and attempts,
 * and for displaying the navigation panel.
 *
 * @package   block_qbpractice
 * @copyright 2021 Jan Kłudkiewicz
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
function get_previous_question_slot($quba, $slot) {
	$slots = $quba->get_slots();
	if ($slot == 1) return 1;
	else return $slot-1;
}

function get_next_question_slot($quba, $slot) {
	$slots = $quba->get_slots();
	if (isset($slots[$slot])) return $slot+1;
	else return null;
}

function get_first_active_question($quba) {
	$slots = $quba->get_slots();
	foreach ($slots as $slot) {
		if ($quba->get_question_state($slot)->is_active()) return $slot;
	}
	return 1;
}

function get_navigation_panel($session, $quba, $active, $returnurl) {
		$bc = new block_contents();
        $bc->attributes['id'] = 'block_qbpractice_navblock';
        $bc->attributes['role'] = 'navigation';
        $bc->attributes['aria-labelledby'] = 'block_qbpractice_navblock_title';
        $bc->title = html_writer::span(get_string('sessionnavigation', 'block_qbpractice'));
		
		$html = html_writer::start_tag('div', array('class' => 'qnbuttons_wrapper'));
		
		$slots = $quba->get_slots();
		foreach ($slots as $slot) {
			$question_state = $quba->get_question_state($slot);
			
			if ($question_state->is_correct()) $correctness = "correct";
			else if ($question_state->is_incorrect()) $correctness = "incorrect";
			else $correctness = "";
			
			if ($slot == $active) $thispage = "thispage";
			else $thispage = "";
			
			if ($quba->get_question_attempt($slot)->is_flagged()) $flagged = "flagged";
			else $flagged = "";
			
			$actionurl = new moodle_url("/blocks/qbpractice/attempt.php", array('id' => $session->id, 'slot' => $slot));
			$buttoncontent = $slot;
			$buttoncontent .= html_writer::tag('span', '', array('class' => "thispageholder"));
			$buttoncontent .= html_writer::tag('span', '', array('class' => "trafficlight"));
			$html .= html_writer::link($actionurl, $buttoncontent, array('class' => 'qnbutton '.$thispage.' '.$correctness.' '.$flagged, 'id' => 'qbpracticenavbutton'.$slot));
		}
		$html .= html_writer::end_tag('div');
		$html .= html_writer::start_tag('div', array('class' => 'other_nav'));
		
		// Other navigation buttons including: finish button, previous sessions summary link and go back to course link
		
		// Add finish button
		$html .= html_writer::start_tag('form', array('method' => 'post', 'action' => $returnurl, 'enctype' => 'multipart/form-data', 'id' => 'navigationform'));
		if ($session->status == "finished") $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'finishreview', 'value' => get_string('finishreview', 'block_qbpractice'), 'class' => 'finish_button'));
		else $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'finish', 'value' => get_string('finishsession', 'block_qbpractice'), 'class' => 'finish_button'));
		$html .= html_writer::end_tag('form');
		
		// Add previus sessions summary link
		$actionurl = new moodle_url("/blocks/qbpractice/summary.php", array('id' => $session->instanceid));
		$html .= html_writer::start_tag('p', array('class' => 'ordinary_link'));
		$html .= html_writer::link($actionurl, get_string('previoussessionssummary', 'block_qbpractice'), array(null));
		$html .= html_writer::end_tag('p');
		
		// Add go back to course link
		$actionurl = new moodle_url(context_block::instance($session->instanceid)->get_url());
		$html .= html_writer::start_tag('p', array('class' => 'ordinary_link'));
		$html .= html_writer::link($actionurl, get_string('backtocourse', 'block_qbpractice'), array(null));
		$html .= html_writer::end_tag('p');
		
		$html .= html_writer::end_tag('div');
		
		$bc->content = $html;
		
		return $bc;
}