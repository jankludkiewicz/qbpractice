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
 * The form for starting a new session.
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    block_qbpractice
 * @copyright  2021 Jan Kłudkiewicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class block_qbpractice_startattempt_form extends moodleform {

    public function definition() {

        $mform = $this->_form;
		
		$mform->addElement('static', 'categoryname', $this->_customdata['category']->name);
		
		$mform->addElement('html', '<div class="studypreference-wrapper">');
		$mform->addElement('html', '<label class="studypreference-label" id="allquestions"><input type="radio" name="studypreference" class="studypreference-radio" value="0">');
		$mform->addElement('html', 'All questions</label>');
		$mform->addElement('html', '<label class="studypreference-label" id="flagged"><input type="radio" name="studypreference" class="studypreference-radio" value="1">');
		$mform->addElement('html', 'Flagged only</label>');
		$mform->addElement('html', '<label class="studypreference-label" id="unseen"><input type="radio" name="studypreference" class="studypreference-radio" value="2">');
		$mform->addElement('html', 'Unseen before</label>');
		$mform->addElement('html', '<label class="studypreference-label" id="incorrect"><input type="radio" name="studypreference" class="studypreference-radio" value="3">');
		$mform->addElement('html', 'Answered incorrectly</label>');
		$mform->addElement('html', '<label class="studypreference-label" id="exam"><input type="radio" name="studypreference" class="studypreference-radio" value="4">');
		$mform->addElement('html', 'Exam</label>');
		$mform->setDefault('studypreference', 0);
		$mform->addElement('html', '</div>');
		
		foreach ($this->_customdata['subcategories'] as $subcategory) {
			$mform->addElement('advcheckbox', 'subcategories['.$subcategory->id.']', $subcategory->name.' ('.$subcategory->allquestions.')', null, array('group' => 1), array(0, 1));
			
			$mform->addElement('hidden', 'subcategories['.$subcategory->id.']_allquestions', $subcategory->allquestions);
			$mform->setType('subcategories['.$subcategory->id.']_allquestions', PARAM_INT);
			
			$mform->addElement('hidden', 'subcategories['.$subcategory->id.']_flagged', $subcategory->flagged);
			$mform->setType('subcategories['.$subcategory->id.']_flagged', PARAM_INT);
			
			$mform->addElement('hidden', 'subcategories['.$subcategory->id.']_unseen', $subcategory->unseen);
			$mform->setType('subcategories['.$subcategory->id.']_unseen', PARAM_INT);
			
			$mform->addElement('hidden', 'subcategories['.$subcategory->id.']_incorrect', $subcategory->incorrect);
			$mform->setType('subcategories['.$subcategory->id.']_incorrect', PARAM_INT);
		}
		
		$mform->addElement('static', 'questions', 'Number of questions');
		$mform->addElement('html','<div class="form-group row"><div class="col-md-3"></div><div class="col-md-9"><input type="range" min="1" max="1" step="1" value="1" name="noofquestions" id="questionsno" onmouseup="updateRange()"> <label id="questionsnodisplay"></label></div></div>');

        $this->add_action_buttons(true, get_string('startpractice', 'qpractice'));

        $mform->addElement('hidden', 'id', 0);
		$mform->addElement('hidden', 'categoryid', 0);
		$mform->addElement('hidden', 'noofquestions', 0);
		$mform->addElement('hidden', 'behaviour', 'immediatefeedback');
		
		$mform->setType('id', PARAM_INT);
		$mform->setType('categoryid', PARAM_INT);
		$mform->setType('noofquestions', PARAM_INT);
		$mform->setType('behaviour', PARAM_ALPHA);
    }

}
