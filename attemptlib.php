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
	return null;
}

function get_navigation_panel($sessionid, $quba, $active) {
		$bc = new block_contents();
        $bc->attributes['id'] = 'block_qbpractice_navblock';
        $bc->attributes['role'] = 'navigation';
        $bc->attributes['aria-labelledby'] = 'block_qbpractice_navblock_title';
        $bc->title = html_writer::span(get_string('sessionnavigation', 'block_qbpractice'));
		$bc->content = '';
		
		$slots = $quba->get_slots();
		foreach ($slots as $slot) {
			$question_state = $quba->get_question_state($slot);
			
			if ($question_state->is_correct()) $slotclass = "correct_slot";
			else if ($question_state->is_incorrect()) $slotclass = "incorrect_slot";
			else $slotclass = "normal_slot";
			
			if ($slot == $active) $activeclass = "this_slot";
			else $activeclass = "other_slot";
			
			$actionurl = new moodle_url("/blocks/qbpractice/attempt.php", array('id' => $sessionid, 'slot' => $slot));
			$buttoncontent = $slot;
			$buttoncontent .= html_writer::tag('span', '', array('class' => "status_box ".$slotclass));
			$buttoncontent .= html_writer::tag('span', '', array('class' => "status_box ".$activeclass));
			$bc->content .= html_writer::link($actionurl, $buttoncontent, array('class' => 'slot_button'));
		} 
		
		return $bc;
}