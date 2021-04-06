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
 * This script deals with starting a new attempt for a qbpractice.
 *
 * It will end up redirecting to attempt.php.
 *
 * @package    block_qbpractice
 * @copyright  2021 Jan Kłudkiewicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/startattempt_form.php');
require_once($CFG->libdir . '/questionlib.php');

$id = required_param('id', PARAM_INT); // instance id.
$categoryid = required_param('categoryid', PARAM_INT); // Question category id

$context = context_block::instance($id);
$courseid = $context->get_parent_context()->instanceid;

$PAGE->set_url('/blocks/qbpractice/startattempt.php', array('id' => $id, 'categoryid' => $categoryid));
$PAGE->set_context($context);

$PAGE->requires->js('/blocks/qbpractice/startattempt_form_script.js?v='.rand());
$PAGE->requires->css('/blocks/qbpractice/style/startattemptstyle.css?v='.rand());

$PAGE->set_title(get_string('practicesession','block_qbpractice'));
$PAGE->set_heading(get_string('practicesession','block_qbpractice'));
$PAGE->set_pagelayout('standard');

require_login($courseid);

$data = array();
$data['category'] = $DB->get_record('question_categories', array('id' => $categoryid), 'id, name');
$data['subcategories'] = $DB->get_records_sql("SELECT categories.id, categories.name, 
										COUNT(DISTINCT question.id) AS allquestions,
                                        COUNT(DISTINCT (CASE WHEN EXISTS (SELECT a.id
                                                                          	FROM {question_attempts} AS a
                                                                          	JOIN {qbpractice_session} AS s ON s.questionusageid = a.questionusageid
                                                                          	WHERE a.questionid = question.id AND a.flagged = 1 AND s.userid = ?)
                                                        THEN question.id ELSE NULL END)) AS flagged,
										COUNT(DISTINCT (CASE WHEN NOT EXISTS (SELECT a.id
																				FROM {question_attempts} AS a
																				JOIN {qbpractice_session} AS s ON s.questionusageid = a.questionusageid
																				WHERE a.questionid = question.id AND a.responsesummary IS NOT NULL AND s.userid = ?)
														THEN question.id ELSE NULL END)) AS unseen,
										COUNT(DISTINCT (CASE WHEN EXISTS (SELECT a.id
                                                                          	FROM {question_attempts} AS a
                                                                          	JOIN {qbpractice_session} AS s ON s.questionusageid = a.questionusageid
                                                                          	WHERE  a.questionid = question.id AND a.rightanswer = a.responsesummary AND s.userid = ?)
														THEN question.id ELSE NULL END)) AS correct,
										COUNT(DISTINCT (CASE WHEN NOT EXISTS (SELECT a.id
																				FROM {question_attempts} AS a
																				JOIN {qbpractice_session} AS s ON s.questionusageid = a.questionusageid
																				WHERE a.questionid = question.id AND a.rightanswer = a.responsesummary AND s.userid = ?)
																	AND attempts.rightanswer != attempts.responsesummary AND session.userid = ?
														THEN question.id ELSE NULL END)) AS incorrect
										FROM {question_categories} AS categories
                                        JOIN {question} AS question ON categories.id = question.category
                                        LEFT JOIN {question_attempts} AS attempts ON attempts.questionid = question.id
										WHERE categories.parent = ? AND question.parent = 0
                                        GROUP BY categories.id
										ORDER BY categories.sortorder ASC", array($USER->id, $USER->id, $USER->id, $USER->id, $USER->id, $categoryid));

$mform = new block_qbpractice_startattempt_form(null, $data); //Starts new form (included in "startattempt_form.php")

if ($mform->is_cancelled()) {
    $returnurl = new moodle_url($context->get_url());
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {
    $sessionid = qbpractice_session_start($fromform, $context);
	$nexturl = new moodle_url('/blocks/qbpractice/attempt.php', array('id' => $sessionid));
	redirect($nexturl);
}

$mform->set_data(array('id' => $id));
$mform->set_data(array('categoryid' => $categoryid));

// Output starts here.
echo $OUTPUT->header();

$mform->display();

// Finish the page.
echo $OUTPUT->footer();
