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
	
	$studypreference = $fromform->studypreference;

	//Process selected subcategories from the form
	$arraycategoryids = array();
	
	foreach ($fromform->subcategories as $key => $subcategorychecked) if (($subcategorychecked==1) && !strpos($key, "_")) $arraycategoryids[$key] = $key;
	$session->categoryids = implode(',', $arraycategoryids);
	var_dump($arraycategoryids);

	$quba = question_engine::make_questions_usage_by_activity('block_qbpractice', $context);
	$quba->set_preferred_behaviour($fromform->behaviour);
	
	$questionids = get_questions($arraycategoryids, $studypreference);
	
	for ($i=0; $i<$session->totalnoofquestions; $i++) {
		$question = question_bank::load_question($questionids[$i]);
		$slot = $quba->add_question($question);
	}
	
	$quba->start_all_questions();
	
	// Sets existing flags
	$quba = set_flags($quba);

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
			$quba->finish_all_questions();
			question_engine::save_questions_usage_by_activity($quba);
	
			$slots = $quba->get_slots();
			$marksobtained = 0;
			$totalmarks = 0;
		
			foreach ($slots as $slot) {
				$fraction = $quba->get_question_fraction($slot);
				$maxmarks = $quba->get_question_max_mark($slot);
				$marksobtained += $fraction * $maxmarks;
				$totalmarks += $maxmarks;
			}
					
			$DB->execute("UPDATE {qbpractice_session} 
							SET status = 'finished', marksobtained = ?, totalmarks = ?, timefinished = ?
							WHERE id=?", array($marksobtained, $totalmarks, time(), $session->id));
		}
		
		$transaction->allow_commit();
	}
}

function clear_user_history() {
	global $USER, $DB;
	
	$DB->execute("DELETE attempts, attempt_steps, usages, session
					FROM mdl_question_attempts AS attempts, mdl_question_attempt_steps AS attempt_steps, mdl_question_usages AS usages, mdl_qbpractice_session AS session
					WHERE attempts.questionusageid = session.questionusageid AND attempts.questionusageid = usages.id AND attempts.id = attempt_steps.questionattemptid AND session.userid = ?", array($USER->id));
}

function get_questions($categoryids, $studypreference, $allowshuffle = true) {
	switch($studypreference) {
		case 0: // All questions
			$available = get_all_questions($categoryids);
			break;
		case 1: // Flagged only
			$available = get_flagged_questions($categoryids);
			break;
		case 2: // Unseen before
			$available = get_unseen_questions($categoryids);
			break;
		case 3: // Answered incorrectly
			$available = get_incorrect_questions($categoryids);
			break;
	}
	
    if ($allowshuffle) shuffle($available);
	
	return $available;
}

function get_all_questions($categoryids) {
	return question_bank::get_finder()->get_questions_from_categories($categoryids, null);
}

function get_flagged_questions($categoryids) {
	global $DB, $USER;
	$DB->set_debug(true);
	$results = $DB->get_fieldset_sql("SELECT DISTINCT question.id
										FROM {question} AS question
										JOIN {question_attempts} AS attempt ON attempt.questionid = question.id
										JOIN {qbpractice_session} AS session ON session.questionusageid = attempt.questionusageid
										WHERE question.parent = 0 AND attempt.flagged = 1 AND question.category IN (?) AND session.userid = ?", array(implode(",", $categoryids), $USER->id));
										
	$DB->set_debug(false);
	var_dump($results);
	$return = array();
	foreach ($results as $result) $return[$result->id] = $result->id;
	return $return;
}

function get_unseen_questions($categoryids) {
	global $DB, $USER;
	$results = $DB->get_records_sql("SELECT DISTINCT question.id
										FROM {question} AS question
										LEFT JOIN {question_attempts} AS attempt ON attempt.questionid = question.id
										LEFT JOIN {qbpractice_session} AS session ON session.questionusageid = attempt.questionusageid
										WHERE question.parent = 0 AND question.category IN (?)
										AND NOT EXISTS (SELECT * FROM {question_attempts} AS a 
														JOIN {qbpractice_session} AS s ON s.questionusageid = a.questionusageid
														WHERE a.responsesummary IS NOT NULL AND a.questionid = question.id AND s.userid = ?)", array(implode(",", $categoryids), $USER->id));
	
	$return = array();
	foreach ($results as $result) $return[$result->id] = $result->id;
	return $return;
}

function get_incorrect_questions($categoryids) {
	global $DB, $USER;
	$results = $DB->get_records_sql("SELECT DISTINCT question.id, question.name
										FROM {question} AS question
										JOIN {question_attempts} AS attempt ON attempt.questionid = question.id
										JOIN {qbpractice_session} AS session ON session.questionusageid = attempt.questionusageid
										WHERE question.parent = 0 AND question.category IN (?) AND attempt.rightanswer != attempt.responsesummary AND attempt.responsesummary IS NOT NULL AND session.userid = ?
										AND NOT EXISTS (SELECT a.id
														FROM {question_attempts} AS a
														JOIN {qbpractice_session} AS s ON s.questionusageid = a.questionusageid
														WHERE a.questionid = question.id AND s.userid = session.userid AND a.rightanswer = a.responsesummary)", array(implode(",", $categoryids), $USER->id));
	
	$return = array();
	foreach ($results as $result) $return[$result->id] = $result->id;
	return $return;
}

function set_flags($quba) {
	global $DB, $USER;
	
	$slots = $quba->get_slots();
	foreach ($slots as $slot) {
		$question_attempt = $quba->get_question_attempt($slot);
		
		$flag_exists = $DB->record_exists_sql("SELECT *
										FROM {question} AS question
										JOIN {question_attempts} AS attempt ON attempt.questionid = question.id
										JOIN {qbpractice_session} AS session ON session.questionusageid = attempt.questionusageid
										WHERE question.parent = 0 AND question.id = ? AND attempt.flagged = 1 AND session.userid = ?", array($question_attempt->get_question_id(), $USER->id));
	
		if ($flag_exists) {
			$question_attempt->set_flagged(true);
			$quba->replace_loaded_question_attempt_info($slot, $question_attempt);
		}
	}
	
	return $quba;
}

function get_question_categories($context) {
	global $DB, $USER;
	
	return $DB->get_records_sql("SELECT categories.id, categories.name, COUNT(DISTINCT questions.id) AS allquestions, COUNT(DISTINCT CASE WHEN attempts.responsesummary IS NOT NULL AND sessions.userid = ? THEN questions.id ELSE NULL END) AS seenbefore
									FROM {question_categories} AS categories
									JOIN {question_categories} AS top ON top.id = categories.parent
                                    JOIN {question_categories} AS sub ON sub.parent = categories.id
                                    LEFT JOIN {question} AS questions ON questions.category = sub.id
                                    LEFT JOIN {question_attempts} AS attempts ON attempts.questionid = questions.id
                                    LEFT JOIN {qbpractice_session} AS sessions ON sessions.questionusageid = attempts.questionusageid
									WHERE categories.contextid = ? AND top.parent = 0 AND questions.parent = 0
                                    GROUP BY categories.id
									ORDER BY categories.sortorder ASC", array($USER->id, $context->id));
}

function get_user_open_sessions() {
	global $USER, $DB;
	
	$session = $DB->get_records("qbpractice_session", array('userid' => $USER->id, 'status' => 'inprogress'));
	
	return $session;
}