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

use context_system;
use core\context;
use core\output\html_writer;
use core\url;
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
        global $DB, $COURSE;
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }

        if (empty($course->idnumber)) {
            return '';
        }

        return $course->idnumber;
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
        global $DB, $COURSE;
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }

        if (empty($course->fullname)) {
            return '';
        }

        return $course->fullname;
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
        global $DB, $COURSE;
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }
        $metadata = \local_placeholders\get_course_metadata($course->id);
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
        $exclude = isset($args['exclude']) ? explode(',', $args['exclude']) : [];
        $mcs = new \local_placeholders\output\persona($users, $title, $exclude);
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
        global $DB, $COURSE, $USER;
        $courseid = $args['courseid'] ?? null;
        if ($courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
        } else {
            $course = $COURSE;
        }
        if (!$course) {
            return '';
        }

        $context = context\course::instance($course->id);

        $params = new stdClass();
        $startdate = $course->startdate;
        $enddate = $course->enddate;
        if ($startdate == 0) {
            $startdate = time();
        }
        $params->start = date('d/m/Y', $startdate);
        $params->startmachine = date('Y-m-d', $startdate);
        if ($enddate == 0) {
            $params->end = get_string('coursenoend', 'local_placeholders');
            $params->endmachine = '';
        } else {
            $params->end = date('d/m/Y', $enddate);
            $params->endmachine = date('Y-m-d', $enddate);
        }
        $return = get_string('coursestartend', 'local_placeholders', $params);
        if (!has_capability('moodle/course:update', $context, $USER)) {
            return $return;
        }
        if (!get_config('local_placeholders', 'includecourseeditlink')) {
            return $return;
        }
        $link = new url('/course/edit.php', ['id' => $course->id]);
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

    /**
     * Snippet shortcode
     *
     * @param string $shortcode The shortcode.
     * @param object $args The arguments of the code.
     * @param string|null $content The content, if the shortcode wraps content.
     * @param object $env The filter environment (contains context, noclean and originalformat).
     * @param Closure $next The function to pass the content through to process sub shortcodes.
     * @return string The new content.
     */
    public static function snippet($shortcode, $args, $content, $env, $next): string {
        global $DB, $PAGE;

        $id = $args['id'] ?? null;
        $slug = $args['slug'] ?? null;
        if (!$id && !$slug) {
            return '';
        }

        // Use either the id or the slug, prefer the id.
        $snippet = $DB->get_record('local_placeholders_snippet', ['id' => $id]);
        if (!$snippet) {
            $snippet = $DB->get_record('local_placeholders_snippet', ['slug' => $slug]);
            if (!$snippet) {
                return '';
            }
        }

        // SOL TODO: Add context checks on course and category ids, and maybe some kind of key.

        $content = file_rewrite_pluginfile_urls(
            // The content of the text stored in the database.
            $snippet->content,
            // The pluginfile URL which will serve the request.
            'pluginfile.php',
            // The combination of contextid / component / filearea / itemid
            // form the virtual bucket that file are stored in.
            context_system::instance()->id,
            'local_placeholders',
            'snippet',
            $snippet->id
        );

        return $next($content);
    }
}
