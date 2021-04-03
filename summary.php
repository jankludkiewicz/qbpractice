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
 * Views overall summary of your session.
 * @package    block_qbpractice
 * @copyright  2021 Jan Kłudkiewicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/locallib.php');

$id = required_param('id', PARAM_INT); // Instance id.

$context = context_block::instance($id);
$courseid = $context->get_parent_context()->instanceid;

$PAGE->set_url('/block/qbpractice/summary.php', array('id' => $id));
$PAGE->set_context($context);

require_login($courseid);

$PAGE->set_title(get_string('pluginname', 'block_qbpractice'));
$PAGE->set_heading(get_string('summary', 'block_qbpractice'));
$PAGE->set_pagelayout('standard');

$table = new html_table();
$table->attributes['class'] = 'generaltable boxaligncenter';
$table->caption = get_string('pastsessions', 'block_qbpractice');
$table->head = array(get_string('order', 'block_qbpractice'), get_string('subjectname', 'block_qbpractice'), get_string('totalnoofquestions', 'block_qbpractice'), get_string('result', 'block_qbpractice'), get_string('date', 'block_qbpractice'));
$table->align = array('left', 'left');
$table->size = array('', '');
$table->data = array();

$sessions = $DB->get_records('qbpractice_session', array('userid' => $USER->id));

$i = 1;
foreach ($sessions as $session) {
	$category_ids = explode(',', $session->categoryids);
	$subjectname = $DB->get_field_sql("SELECT top.name
										FROM {question_categories} AS top
										JOIN {question_categories} AS sub ON sub.parent = top.id
										WHERE sub.id = ?", array($category_ids[0]));
	$actionurl = new moodle_url('/blocks/qbpractice/attempt.php', array('id' => $session->id));
	$label = html_writer::tag('span', $subjectname);
	$sessionlink = html_writer::link($actionurl, $label, array(null));
	
	if ($session->status == "finished") {
		$score = round($session->marksobtained/$session->totalmarks*100,2);
		$table->data[] = array($i, $sessionlink, $session->totalnoofquestions, $score.'%', date("d-m-Y H:i", $session->timefinished));
	}
	if ($session->status == "inprogress") {
		$row = new html_table_row();
		$row->cells = array($i, $sessionlink, $session->totalnoofquestions);
		$status_cell = new html_table_cell();
		$status_cell->colspan = 2;
		$status_cell->text = get_string('inprogress', 'block_qbpractice');
		$row->cells[] = $status_cell;
		$table->data[] = $row;
	}
	
	$i++;
}

if (empty($sessions)) {
	$row = new html_table_row();
	$no_sessions_cell = new html_table_cell();
	$no_sessions_cell->colspan = 5;
	$no_sessions_cell->text = get_string('nosessions', 'block_qbpractice');
	$row->cells[] = $no_sessions_cell;
	$table->data[] = $row;
}

$html = html_writer::table($table);

if (!empty($sessions)) {
	$actionurl = new moodle_url('/blocks/qbpractice/clear_practice_history.php', array('id' => $id));
	$label = html_writer::tag('span', get_string('clearhistory', 'block_qbpractice'));
	$html .= html_writer::link($actionurl, $label, array(null));
}

$html .= html_writer::empty_tag('br');

$actionurl = new moodle_url($context->get_url());
$label = html_writer::tag('span', get_string('backtocourse', 'block_qbpractice'));
$html .= html_writer::link($actionurl, $label, array(null));

echo $OUTPUT->header();

echo $html;

echo $OUTPUT->footer();
