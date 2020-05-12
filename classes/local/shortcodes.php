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
 * Shortcodes implementations.
 * 
 * @package    local_chi
 * @copyright  2019 University of Chichester {@link http://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_placeholders\local;

require_once($CFG->dirroot . '/local/placeholders/locallib.php');

use context_course;
use html_writer;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class shortcodes {

    public static function modulecode($shortcode, $args, $content, $env, $next) {
        global $COURSE;

        if (!$COURSE) {
            return '';
        }

        if (empty($COURSE->idnumber)) {
            return '';
        }

        return $COURSE->idnumber;
    }

    public static function modulename($shortcode, $args, $content, $env, $next) {
        global $COURSE;

        if (!$COURSE) {
            return '';
        }

        if (empty($COURSE->fullname)) {
            return '';
        }

        return $COURSE->fullname;
    }

    public static function modulelevel($shortcode, $args, $content, $env, $next) {
        global $COURSE, $DB;

        if (!$COURSE) {
            return '';
        }
        $level = $DB->get_field('local_course_metadata', 'value', ['courseid' => $COURSE->id, 'name' => 'level']);
        if (!$level) {
            return '';
        }
        return $level;
    }

    public static function coordinators($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        $users = \local_placeholders\get_users_in_course_by_role('coordinator');
        $mcs = new \local_placeholders\output\persona($users);
        return $OUTPUT->render($mcs);
    }

    public static function librarians($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        $users = \local_placeholders\get_users_in_course_by_role('sl');
        $mcs = new \local_placeholders\output\persona($users);
        return $OUTPUT->render($mcs);
    }

    public static function lecturers($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        $users = \local_placeholders\get_users_in_course_by_role('lecturer');
        $mcs = new \local_placeholders\output\persona($users);
        return $OUTPUT->render($mcs);
    }

    public static function timetable($shortcode, $args, $content, $env, $next) {
        global $COURSE, $DB, $OUTPUT;

        if (!$COURSE) {
            return '';
        }
        $records = $DB->get_records('local_course_metadata', ['courseid' => $COURSE->id]);
        if (!$records) {
            return 'Nada';
        }
        $meta = new stdClass();
        foreach ($records as $record) {
            $meta->{$record->name} = $record->value;
        }
        $classes = \local_placeholders\timetable($meta->modulecode, $meta->semester, $meta->occurrence, $meta->academicyear, time(), strtotime("+14 days", time()));
        $classes->stuff = "Stuff";
        $timetable = new \local_placeholders\output\timetable($classes);
        return $OUTPUT->render($timetable);
    }

    public static function startenddates($shortcode, $args, $content, $env, $next) {
        global $COURSE, $USER;

        if (!$COURSE) {
            return '';
        }

        $context = context_course::instance($COURSE->id);

        $params = new stdClass();
        $startdate = $COURSE->startdate;
        $enddate = $COURSE->enddate;
        if ($startdate == 0) {
            $startdate = time();
        }
        $params->start = date('d/m/Y', $startdate);
        if ($enddate == 0) {
            $params->end = get_string('coursenoend', 'local_placeholders');
        } else {
            $params->end = date('d/m/Y', $enddate);
        }
        $return = get_string('coursestartend', 'local_placeholders', $params);
        if (!has_capability('moodle/course:update', $context, $USER)) {
            return $return;
        }
        $link = new moodle_url('/course/edit.php', ['id' => $COURSE->id]);
        return $return . ' ' . html_writer::link($link, get_string('editsettings'));
    }
}