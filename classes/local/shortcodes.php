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
        global $COURSE;

        if (!$COURSE) {
            return '';
        }
        $metadata = \local_placeholders\get_course_metadata($COURSE->id);
        return $metadata['level'] ?? '';
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

    /**
     * Prints out a timetable for the given course.
     *
     * @param string $shortcode
     * @param array $args
     * @param string $content
     * @param \context $env
     * @param string $next
     * @return string Timetable
     * @todo Needs implementing.
     */
    public static function timetable($shortcode, $args, $content, $env, $next) {
        global $COURSE, $DB, $OUTPUT;
        return '';
        if (!$COURSE) {
            return '';
        }
        $metadata = \local_placeholders\get_course_metadata($COURSE->id);
        
        $classes = \local_placeholders\timetable($metadata['modulecode'], $metadata['semester'], $metadata['occurrence'], $metadata['academicyear'], time(), strtotime("+14 days", time()));
        if (empty($classes->classes)) {
            return '';
        }
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

    public static function coursefield($shortcode, $args, $content, $env, $next) {
        global $COURSE;
        if (!$COURSE) {
            return '';
        }
        if (!isset($args['name'])) {
            return;
        }
        $metadata = \local_placeholders\get_course_metadata($COURSE->id);
        if (!isset($metadata[$args['name']])) {
            return '';
        }
        $value = $metadata[$args['name']];
        if (strpos($value, 'http') === 0) {
            $value = html_writer::link($value, ucwords($args['name']));
        }
        return $value;

    }
}