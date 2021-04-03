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
			
			if ($question_state->is_correct()) $slotclass = "correct_slot";
			else if ($question_state->is_incorrect()) $slotclass = "incorrect_slot";
			else $slotclass = "normal_slot";
			
			if ($slot == $active) $thispage = "thispage";
			else $thispage = "";
			
			$actionurl = new moodle_url("/blocks/qbpractice/attempt.php", array('id' => $session->id, 'slot' => $slot));
			$buttoncontent = $slot;
			$buttoncontent .= html_writer::tag('span', '', array('class' => "thispageholder"));
			$buttoncontent .= html_writer::tag('span', '', array('class' => "trafficlight"));
			$html .= html_writer::link($actionurl, $buttoncontent, array('class' => 'qnbutton '.$thispage, 'id' => 'qbpracticenavbutton'.$slot));
		}
		$html .= html_writer::end_tag('div');
		$html .= html_writer::start_tag('div', array('class' => 'other_nav'));
		$html .= html_writer::start_tag('form', array('method' => 'post', 'action' => $returnurl, 'enctype' => 'multipart/form-data', 'id' => 'navigationform'));
		if ($session->status == "finished") $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'finishreview', 'value' => get_string('finishreview', 'block_qbpractice')));
		else $html .= html_writer::empty_tag('input', array('type' => 'submit', 'name' => 'finish', 'value' => get_string('finishsession', 'block_qbpractice')));
		$html .= html_writer::end_tag('form');
		$html .= html_writer::end_tag('div');
		
		$bc->content = $html;
		
		return $bc;
}