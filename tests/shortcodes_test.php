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
 * Tests for shortcodes
 *
 * @package   local_placeholders
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_placeholders;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use advanced_testcase;

class shortcodes_test extends advanced_testcase {
    public function setUp(): void {
       $this->resetAfterTest();
    }

    public function test_modulecode() {
        global $PAGE;
        $course = $this->getDataGenerator()->create_course([
            'idnumber' => 'ABC101_S1_A_20'
        ]);
        $PAGE->set_course($course);
        $formattedtext = \local_placeholders\local\shortcodes::modulecode(null, null, null, null, null);
        $this->assertSame('ABC101_S1_A_20', $formattedtext);

        // Course with no idnumber returns an empty string;
        $course = $this->getDataGenerator()->create_course();
        $PAGE->set_course($course);
        $formattedtext = \local_placeholders\local\shortcodes::modulecode(null, null, null, null, null);
        $this->assertSame('', $formattedtext);
    }

    public function test_modulename() {
        global $PAGE;
        $course = $this->getDataGenerator()->create_course([
            'fullname' => 'My test course'
        ]);
        $PAGE->set_course($course);
        $formattedtext = \local_placeholders\local\shortcodes::modulename(null, null, null, null, null);
        $this->assertSame('My test course', $formattedtext);
    }

    public function test_contactcard() {
        global $DB, $PAGE;
        $course = $this->getDataGenerator()->create_course();
        $PAGE->set_course($course);
        $roles = $DB->get_records('role', [], '', 'shortname, id');
        set_config('persona_roles', $roles['editingteacher']->id . ',' . $roles['teacher']->id, 'local_placeholders');
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'twitter',
            'name' => 'Twitter',
            'description' => 'Your Twitter username e.g. academic1000',
            'datatype' => 'text',
            'defaultdata' => '',
            'param1' => "30",
            'param2' => "15",
            'param4' => 'https://twitter.com/$$',
            'param5' => '_blank'
        ]);
        $editingteacher = $this->getDataGenerator()->create_user([
            'profile_field_twitter' => 'testtwitacc'
        ]);
        $teacher = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($editingteacher->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'teacher');
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');

        $regexpattern = "/.*placeholders_persona.*FULLNAME.*id=USERID&amp;course=COURSEID.*fa-envelope.*" .
            "<a href=\"mailto:USERNAME.*/is";

        // No arguments passed in, so nothing to print.
        $formattedtext = \local_placeholders\local\shortcodes::contactcard(null, null, null, null, null);
        $this->assertSame('', $formattedtext);

        // Card for editing teacher.
        $formattedtext = \local_placeholders\local\shortcodes::contactcard(null, ['role' => 'editingteacher'], null, null, null);
        $filteredtextpattern = str_replace('USERNAME', $editingteacher->username, $regexpattern);
        $filteredtextpattern = str_replace('FULLNAME', fullname($editingteacher), $filteredtextpattern);
        $filteredtextpattern = str_replace('USERID', $editingteacher->id, $filteredtextpattern);
        $filteredtextpattern = str_replace('COURSEID', $course->id, $filteredtextpattern);
        $this->assertMatchesRegularExpression($filteredtextpattern, $formattedtext);

        // Card for teacher.
        $formattedtext = \local_placeholders\local\shortcodes::contactcard(null, ['role' => 'teacher'], null, null, null);
        $filteredtextpattern = str_replace('USERNAME', $teacher->username, $regexpattern);
        $filteredtextpattern = str_replace('FULLNAME', fullname($teacher), $filteredtextpattern);
        $filteredtextpattern = str_replace('USERID', $teacher->id, $filteredtextpattern);
        $filteredtextpattern = str_replace('COURSEID', $course->id, $filteredtextpattern);
        $this->assertMatchesRegularExpression($filteredtextpattern, $formattedtext);

        // Card for student i.e. there shouldn't be one because the student role hasn't been added in the settings.
        $formattedtext = \local_placeholders\local\shortcodes::contactcard(null, ['role' => 'student'], null, null, null);
        $this->assertSame('', $formattedtext);

        // Add the Twitter field as an option
        set_config('persona_profilefields', 'twitter', 'local_placeholders');
        $regexpattern = "/.*placeholders_persona.*FULLNAME.*id=USERID&amp;course=COURSEID.*fa-envelope.*" .
            "<a href=\"mailto:USERNAME.*TWITTER/is";
        // Card for editing teacher with Twitter handle.
        $formattedtext = \local_placeholders\local\shortcodes::contactcard(null, ['role' => 'editingteacher'], null, null, null);
        $filteredtextpattern = str_replace('USERNAME', $editingteacher->username, $regexpattern);
        $filteredtextpattern = str_replace('FULLNAME', fullname($editingteacher), $filteredtextpattern);
        $filteredtextpattern = str_replace('USERID', $editingteacher->id, $filteredtextpattern);
        $filteredtextpattern = str_replace('COURSEID', $course->id, $filteredtextpattern);
        $filteredtextpattern = str_replace('TWITTER', 'twitter.com\/testtwitacc', $filteredtextpattern);
        $this->assertMatchesRegularExpression($filteredtextpattern, $formattedtext);

        // Card for teacher without Twitter handle.
        $formattedtext = \local_placeholders\local\shortcodes::contactcard(null, ['role' => 'teacher'], null, null, null);
        $filteredtextpattern = str_replace('USERNAME', $teacher->username, $regexpattern);
        $filteredtextpattern = str_replace('FULLNAME', fullname($teacher), $filteredtextpattern);
        $filteredtextpattern = str_replace('USERID', $teacher->id, $filteredtextpattern);
        $filteredtextpattern = str_replace('COURSEID', $course->id, $filteredtextpattern);
        $filteredtextpattern = str_replace('TWITTER', '', $filteredtextpattern);
        $this->assertMatchesRegularExpression($filteredtextpattern, $formattedtext);
    }
}