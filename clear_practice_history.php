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
 * This page displays an attempt of practice module.
 *
 * @package    block_qbpractice
 * @copyright  2021 Jan Kłudkiewicz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
 require_once(dirname(__FILE__) . '/locallib.php');
 
$id = required_param('id', PARAM_INT); // Instance id.

$context = context_block::instance($id);
$courseid = $context->get_parent_context()->instanceid;

$PAGE->set_url('/block/qbpractice/clear_practice_history.php', array('id' => $id));
$PAGE->set_context($context);

require_login($courseid);

clear_user_history();

redirect($context->get_url());