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
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_placeholders\local;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/placeholders/locallib.php');

use context_course;
use html_writer;
use moodle_url;
use stdClass;

/**
 * Class called by the filter_shortcodes plugin.
 */
class shortcodes {

    /**
     * Returns course idnumber if set.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
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

    /**
     * Returns course fullname if is a course.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
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

    /**
     * Returns course qualification level if set (e.g. L4,L5).
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function modulelevel($shortcode, $args, $content, $env, $next) {
        global $COURSE;

        if (!$COURSE) {
            return '';
        }
        $metadata = \local_placeholders\get_course_metadata($COURSE->id);
        return $metadata['level'] ?? '';
    }

    /**
     * Returns persona cards for each module coordinator on course, if any.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. 'role' and 'title' accepted.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function coordinators($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        $users = \local_placeholders\get_users_in_course_by_role('coordinator');
        $title = $args['title'] ?? '';
        $mcs = new \local_placeholders\output\persona($users, $title);
        return $OUTPUT->render($mcs);
    }

    /**
     * Returns persona cards for each module coordinator on course, if any.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. 'role' and 'title' accepted.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function librarians($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        $users = \local_placeholders\get_users_in_course_by_role('sl');
        $title = $args['title'] ?? '';
        $mcs = new \local_placeholders\output\persona($users, $title);
        return $OUTPUT->render($mcs);
    }

    /**
     * Returns persona cards for each lecturer on course, if any.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. 'role' and 'title' accepted.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function lecturers($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        $users = \local_placeholders\get_users_in_course_by_role('lecturer');
        $title = $args['title'] ?? '';
        $mcs = new \local_placeholders\output\persona($users, $title);
        return $OUTPUT->render($mcs);
    }

    /**
     * Returns persona cards for each given rolename on course, if any.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code. 'role' and 'title' accepted.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function contactcard($shortcode, $args, $content, $env, $next) {
        global $OUTPUT;
        if (!isset($args['role'])) {
            return '';
        }
        $permittedroleids = explode(',', get_config('local_placeholders', 'persona_roles'));
        $rolenames = \local_placeholders\get_rolenames_for_ids($permittedroleids);
        if (!array_key_exists($args['role'], $rolenames)) {
            return '';
        }
        $title = $args['title'] ?? '';
        $users = \local_placeholders\get_users_in_course_by_role($args['role']);
        $mcs = new \local_placeholders\output\persona($users, $title);
        return $OUTPUT->render($mcs);
    }

    /**
     * Prints out a timetable for the given course.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     * @todo Needs implementing.
     */
    public static function timetable($shortcode, $args, $content, $env, $next) {
        global $COURSE, $OUTPUT;
        return '';
    }

    /**
     * Prints out the start and end dates of a course, with an edit link if permitted.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
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

    /**
     * Prints out any course custom field.
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * * name - Shortname of the course custom field. e.g. semester.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
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
        if ($args['name'] == 'expiration') {
            $value = "Expiration date: " . userdate($value);
        }
        return $value;
    }
}
