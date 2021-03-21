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

defined('MOODLE_INTERNAL') || die();

function qbpractice_session_start($fromform, $context) {
    global $DB, $USER;
	
	qbpractice_session_finish();
	
    $session = new stdClass();
	
     /* $value = $fromform->optiontype;
     * type of practice (optiontype), is being set to 1 normal
     * as the other types (goalpercentage and time) have not been
     * implemented. it might be good to implement them in a later
     * release
     */
    $value = 1;

    if ($value == 1) {
        $session->time = null;
        $session->goalpercentage = null;
        $session->noofquestions = null;
    }

	$session->instanceid = $fromform->id;
    $session->timecreated = time();
    $session->typeofpractice = $value;
	$session->userid = $USER->id;
	$session->totalnoofquestions = $fromform->noofquestions;

	//Process selected subcategories from the form
	$arraycategoryids = array();
	foreach ($fromform->subcategories as $key => $subcategorychecked) if ($subcategorychecked==1) $arraycategoryids[$key] = $key;
	$session->categoryids = implode(',', $arraycategoryids);

	$quba = question_engine::make_questions_usage_by_activity('block_qbpractice', $context);
	$quba->set_preferred_behaviour($fromform->behaviour);
	
	$questionids = get_questions($arraycategoryids);
	
	for ($i=0; $i<$session->totalnoofquestions; $i++) {		
		$question = question_bank::load_question($questionids[$i]);
		$slot = $quba->add_question($question);
	}
	
	$quba->start_all_questions();

	question_engine::save_questions_usage_by_activity($quba);
	
    $session->questionusageid = $quba->get_id();
    $sessionid = $DB->insert_record('qbpractice_session', $session);
	
    return $sessionid;
}

function qbpractice_session_finish() {
	global $USER, $DB;
	
	$sessions = get_user_open_sessions();
	
	if ($sessions) {
		
		$transaction = $DB->start_delegated_transaction();
		
		foreach ($sessions as $session) {
			$quba = question_engine::load_questions_usage_by_activity($session->questionusageid);
	
			$slots = $quba->get_slots();
			$marksobtained = 0;
			$totalmarks = 0;
		
			foreach ($slots as $slot) {
				$fraction = $quba->get_question_fraction($slot);
				$maxmarks = $quba->get_question_max_mark($slot);
				$marksobtained += $fraction * $maxmarks;
				$totalmarks += $maxmarks;
			}
	
			$updatesql = "UPDATE {qbpractice_session} 
							SET status = 'finished', marksobtained = ?, totalmarks = ?, timefinished = ?
							WHERE id=?";
					
			$DB->execute($updatesql, array($marksobtained, $totalmarks, time(), $session->id));
		}
		
		$transaction->allow_commit();
	}
}

function get_questions($categoryids, $allowshuffle = true) {
	$available = question_bank::get_finder()->get_questions_from_categories($categoryids, null);
	
    if ($allowshuffle) shuffle($available);
	
	return $available;
}

/*
function choose_next_question($categoryids, $excludedquestions, $allowshuffle = true) {
	
    $available = question_bank::get_finder()->get_questions_from_categories($categoryids, null);
	
    if ($allowshuffle) shuffle($available); 

    foreach ($available as $questionid) {
        if (in_array($questionid, $excludedquestions)) continue;
		else {
			$question = question_bank::load_question($questionid, $allowshuffle);
			return $question;
		}
    }

    return null;
}*/

function get_question_categories($context) {
	global $DB;
	
	return $DB->get_records_sql("SELECT categories.id, categories.name
									FROM {question_categories} AS categories
									JOIN {question_categories} AS top ON top.id = categories.parent
									WHERE categories.contextid = ? AND top.parent = 0
									ORDER BY categories.sortorder ASC", array($context->id));
}

function get_user_open_sessions() {
	global $USER, $DB;
	
	$session = $DB->get_records("qbpractice_session", array('userid' => $USER->id, 'status' => 'inprogress'));
	
	return $session;
}