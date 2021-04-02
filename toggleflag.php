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
 * Used by ajax calls to toggle the flagged state of a question in an attempt.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/question/engine/lib.php');

// Parameters
$questionid = required_param('qid', PARAM_INT);
$newstate = required_param('newstate', PARAM_INT);

// Check that the requested session really exists
$attempts = $DB->get_records_sql("SELECT *
								FROM {question_attempts} AS attempt
								JOIN {qbpractice_session} AS session ON session.questionusageid = attempt.questionusageid
								WHERE session.userid = ? AND attempt.questionid = ?", array($USER->id, $questionid));
					var_dump($attempts);			
foreach ($attempts as $attempt) {
	$attempt->flagged = $newstate;
	$DB->update_record("{question_attempts}", $attempt);
}

echo 'OK';
