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

require_once("locallib.php");

class block_qbpractice extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_qbpractice');
    }

	public function get_content() {
		$coursecontext = $this->context->get_course_context();
		$coursecatcontext = $coursecontext->get_parent_context();
		
		if ($this->content !== null) {
			return $this->content;
		}
 
		$this->content =  new stdClass;
		
		$sessions = get_user_open_sessions();
		
		if ($sessions) {
			// Continue previous session
			$session = reset($sessions);
			
			$continueurl = new moodle_url("/blocks/qbpractice/attempt.php", array('id' => $session->id));
			$label = html_writer::tag('span', get_string('continuesession', 'block_qbpractice'));
			$this->content->text = html_writer::link($continueurl, $label, array(null));
			
			$this->content->text .= html_writer::empty_tag('br');
			
			$finishurl = new moodle_url("/blocks/qbpractice/summary.php", array('id' => $this->context->instanceid));
			$label = html_writer::tag('span', get_string('finishsession', 'block_qbpractice'));
			$this->content->text .= html_writer::link($finishurl, $label, array(null));
		} else {
			// Select category to start session
			$this->content->text = html_writer::tag('span', get_string('selectcategory', 'block_qbpractice'));
		
			$questioncategories = get_question_categories($coursecatcontext);
		
			$this->content->text .= html_writer::start_tag('ul', array(null));
		
			foreach ($questioncategories as $questioncategory) {
				$this->content->text .= html_writer::start_tag('li', array(null));
				$actionurl = new moodle_url("/blocks/qbpractice/startattempt.php", array('id' => $this->context->instanceid, 'categoryid' => $questioncategory->id));
				$label = html_writer::tag('span', $questioncategory->name);
				$this->content->text .= html_writer::link($actionurl, $label, array(null));
				$this->content->text .= html_writer::end_tag('li');
			}
		
			$this->content->text .= html_writer::end_tag('ul');
			
			$actionurl = new moodle_url('/blocks/qbpractice/summary.php', array('id' => $this->context->instanceid));
			$label = html_writer::tag('span', get_string('previoussessionssummary', 'block_qbpractice'));
			$this->content->footer = html_writer::link($actionurl, $label, array(null));
		}

		return $this->content;
	}
	
    public function applicable_formats() {
        return array('course' => true);
    }

}
