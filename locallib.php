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
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_placeholders;

use context_course;
use stdClass;

/**
 * Called by the installer to set up user profile fields for Moodle.
 *
 * @param string $shortname
 * @return void
 */
function set_userinfofield($shortname) {
    global $DB;
    $specific = [
        'instagram' => [
            'name' => 'Instagram',
            'description' => 'Your Instagram username e.g. academic1000',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "15",
            'param4' => 'https://instagram.com/$$',
            'param5' => '_blank'
        ],
        'linkedin' => [
            'name' => 'Linkedin',
            'description' => 'Your Linkedin profile page',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "15",
            'param4' => 'https://linkedin.com/in/$$',
            'param5' => '_blank'
        ],
        'twitter' => [
            'name' => 'Twitter',
            'description' => 'Your Twitter username e.g. academic1000',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "15",
            'param4' => 'https://twitter.com/$$',
            'param5' => '_blank'
        ],
    ];

    if (!array_key_exists($shortname, $specific)) {
        return;
    }

    if ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
        return;
    }

    if (!$cat = $DB->get_record('user_info_category', ['name' => get_string('profiledefaultcategory', 'admin')])) {
        $sortorder = $DB->get_field_sql('SELECT MAX(sortorder) FROM {user_info_category}');
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
    $sortorder = $DB->get_field_sql("SELECT MAX(sortorder) FROM {user_info_field} WHERE categoryid = :categoryid",
        ['categoryid' => $cat->id]);
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
    $r->param1 = $inst['param1']; // Textbox length.
    $r->param2 = $inst['param2']; // Max length for field.
    $r->param3 = $inst['param3']; // Password.
    $r->param4 = $inst['param4']; // Link Url.
    $r->param5 = $inst['param5']; // Link target.

    $DB->insert_record('user_info_field', $r);
}

/**
 * Given a role shortname, this will fetch users in the current course with that role.
 *
 * @param string $roleshortname
 * @return array List of userIDs indexed by userid.
 */
function get_users_in_course_by_role($roleshortname) {
    global $DB, $COURSE;
    $context = context_course::instance($COURSE->id);
    $roleid = $DB->get_field('role', 'id', ['shortname' => $roleshortname]);
    // Check enrolment or user isn't suspended or inactive.
    // ue.status = ENROL_USER_ACTIVE.
    $roleassignments = $DB->get_records('role_assignments', ['contextid' => $context->id, 'roleid' => $roleid]);
    $users = [];
    // A user can have multiple assignments of the same role. We only need one.
    foreach ($roleassignments as $ra) {
        $users[$ra->userid] = $ra->userid;
    }
    return $users;
}

/**
 * Returns the default course role names.
 *
 * @return array Key/Value pair for a menu.
 */
function get_course_roles_menu() {
    global $DB;
    $sql = "SELECT r.id, r.name, r.shortname
              FROM {role} r
         LEFT JOIN {role_context_levels} rcl ON (rcl.roleid = r.id AND rcl.contextlevel = :contextlevel)
             WHERE rcl.id IS NOT NULL
          ORDER BY sortorder DESC";
    $params = [
        'contextlevel' => CONTEXT_COURSE
    ];
    $roles = $DB->get_records_sql($sql, $params);

    return role_fix_names($roles, null, ROLENAME_ORIGINAL, true);
}

/**
 * Given roleids, return the shortnames
 *
 * @param array $roleids
 * @return array
 */
function get_rolenames_for_ids($roleids) {
    global $DB;
    list($insql, $inparams) = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
    $sql = "SELECT r.shortname
        FROM {role} r
        WHERE r.id $insql";
    return $DB->get_records_sql($sql, $inparams);
}

/**
 * Print a timetable for the module.
 * Not currently used. Here as an idea.
 *
 * @param string $code Module code
 * @param string $semester Semester of the module (S1,S2)
 * @param string $occurrence A,B,C etc
 * @param string $year Academic year the module runs
 * @param int $start Start timestamp the sessions to display - now?
 * @param int $end End timestamp of the sessions to display now+2weeks.
 * @return void
 * @todo Not implemented.
 */
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
    $datas = $handler->get_instance_data($courseid);
    $metadata = [];
    foreach ($datas as $data) {
        if (empty($data->get_value())) {
            continue;
        }
        $metadata[$data->get_field()->get('shortname')] = $data->get_value();
    }
    return $metadata;
}
