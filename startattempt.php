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

$PAGE->set_url('/blocks/qbpractice/startattempt.php', array('id' => $id, 'categoryid' => $categoryid));
$PAGE->requires->js('/blocks/qbpractice/startattempt_form_script.js');
$PAGE->set_context($context);
$DB->set_field('qbpractice_session', 'status', 'finished', array(null));

var_dump($USER);

require_login();

$data = array();
$data['category'] = $DB->get_record('question_categories', array('id' => $categoryid), 'id, name');
$data['subcategories'] = $DB->get_records_sql("SELECT categories.id, categories.name, COUNT(*) as noofquestions
										FROM {question_categories} AS categories
                                        JOIN {question} AS question ON categories.id = question.category
										WHERE categories.parent = ? AND question.parent = 0
                                        GROUP BY categories.id
										ORDER BY categories.sortorder ASC", array($categoryid));

$mform = new block_qbpractice_startattempt_form(null, $data); //Starts new form (included in "startattempt_form.php")

if ($mform->is_cancelled()) {
    $returnurl = new moodle_url('/block/qbpractice/view.php', array('id' => $id));
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {
    $sessionid = qbpractice_session_create($fromform, $context);
	$nexturl = new moodle_url('/blocks/qbpractice/attempt.php', array('id' => $sessionid));
	redirect($nexturl);
}

$mform->set_data(array('id' => $id));
$mform->set_data(array('categoryid' => $categoryid));

// Print the page header.
$PAGE->set_title(format_string($data['category']->name));
$PAGE->set_heading(format_string($data['category']->name));

// Output starts here.
echo $OUTPUT->header();

$mform->display();

// Finish the page.
echo $OUTPUT->footer();
