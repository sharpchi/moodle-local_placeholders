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
 * Local functions.
 * 
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link http://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_placeholders;

use context_course;
use stdClass;

defined('MOODLE_INTERNAL') || die();

function set_userinfofield($shortname) {
    global $DB;
    $specific = [
        'twitter' => [
            'name' => 'Twitter handle',
            'description' => 'Your Twitter username e.g. chiuni',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "15",
            'param4' => 'https://twitter.com/$$',
            'param5' => '_blank'
        ],
        'instagram' => [
            'name' => 'Instagram handle',
            'description' => 'Your Instagram username e.g. chiuni',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "15",
            'param4' => 'https://instagram.com/$$',
            'param5' => '_blank'
        ],
        'room' => [
            'name' => 'Room',
            'description' => 'If you have an office, what\'s your postcode e.g. C18-0-06',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "10",
            'param4' => 'https://maps.chi.ac.uk/#room=$$',
            'param5' => '_blank'
        ],
    ];

    if (!array_key_exists($shortname, $specific)) {
        return;
    }

    if ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
        // error_log("field already exists");
        return;
    }

    if (!$cat = $DB->get_record('user_info_category', ['name' => get_string('profiledefaultcategory', 'admin')])) {
        $sortorder = $DB->get_field_sql('SELECT MAX(`sortorder`) FROM {user_info_category}');
        if (is_numeric($sortorder)) {
            $sortorder++;
        } else {
            $sortorder = 1;
        }
        $cat = new stdClass();
        $cat->name = get_string('profiledefaultcategory', 'admin');
        $cat->sortorder = $sortorder;
        $cat->id = $DB->insert_record('user_info_category', $cat);
    }
    $sortorder = $DB->get_field_sql("SELECT MAX(`sortorder`) FROM {user_info_field} WHERE `categoryid` = :categoryid", ['categoryid' => $cat->id]);
    if (is_numeric($sortorder)) {
        $sortorder++;
    } else {
        $sortorder = 1;
    }
    $inst = $specific[$shortname];
    $datatype = $inst['datatype'] ?? 'text';
    switch ($datatype) {
        default:
            $inst['param1'] = $inst['param1'] ?? 30;
            $inst['param2'] = $inst['param2'] ?? 15;
            $inst['param3'] = $inst['param3'] ?? '';
            $inst['param4'] = $inst['param4'] ?? '';
            $inst['param5'] = $inst['param5'] ?? '';
            break;
    }
    
    $r = new stdClass();
    $r->shortname = $shortname;
    $r->name = $inst['name'];
    $r->datatype = $datatype;
    $r->description = $inst['description'] ?? '';
    $r->descriptionformat = FORMAT_HTML;
    $r->categoryid = $cat->id;
    $r->sortorder = $sortorder;
    $r->required = $inst['required'] ?? 0;
    $r->locked = $inst['locked'] ?? 0;
    $r->visible = $inst['visible'] ?? 1;
    $r->forceunique = $inst['forceunique'] ?? 0;
    $r->signup = 0;
    $r->defaultdata = $inst['defaultdata'] ?? '';
    $r->defaultdataformat = 0;
    $r->param1 = $inst['param1']; // textbox length
    $r->param2 = $inst['param2']; // max length for field
    $r->param3 = $inst['param3']; // password?
    $r->param4 = $inst['param4']; // link Url
    $r->param5 = $inst['param5']; // link target
    
    $DB->insert_record('user_info_field', $r);
    
}

/**
 * Given a role shortname, this will fetch users in the current course with that role.
 *
 * @param string $roleshortname
 * @return array List of userIDs
 */
function get_users_in_course_by_role($roleshortname) {
    global $DB, $COURSE;
    $context = context_course::instance($COURSE->id);
    $roleid = $DB->get_field('role', 'id', ['shortname' => $roleshortname]);
    $roleassignments = $DB->get_records('role_assignments', ['contextid' => $context->id, 'roleid' => $roleid]);
    $users = [];
    foreach ($roleassignments as $ra) {
        $users[$ra->userid] = $ra->userid;
    }
    return $users;
}

function timetable($code, $semester, $occurrence, $year, $start, $end) {
    global $DB;
    $classes = new stdClass();
    $classes->classes = [];
    // This is where to do some API things.
    
    return $classes;
}

/**
 * Gets all the metadate for a given courseid
 * @param int $courseid Course ID
 * @return array Array of metadata values indexed by the field's shortname.
 */
function get_course_metadata($courseid) {
    $handler = \core_customfield\handler::get_handler('core_course', 'course');
    // This is equivalent to the line above.
    // $handler = \core_course\customfield\course_handler::create();
    $datas = $handler->get_instance_data($courseid);
    $metadata = [];
    foreach ($datas as $data) {
        if (empty($data->get_value())) {
            continue;
        }
        // $cat = $data->get_field()->get_category()->get('name');
        $metadata[$data->get_field()->get('shortname')] = $data->get_value();
    }
    return $metadata;
}