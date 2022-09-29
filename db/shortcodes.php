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
 * Lists Chi defined shortcodes, depends on filter_shortcodes being present.
 *
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// See filter_shortcodes for full documentation.
// The key in this array is the name of the shortcode e.g. [modulecode].
$shortcodes = [
    'modulecode' => [
        'callback' => 'local_placeholders\local\shortcodes::modulecode',
        'description' => 'shortcode:modulecode'
    ],
    'modulename' => [
        'callback' => 'local_placeholders\local\shortcodes::modulename',
        'description' => 'shortcode:modulename'
    ],
    'modulelevel' => [
        'callback' => 'local_placeholders\local\shortcodes::modulelevel',
        'description' => 'shortcode:modulelevel'
    ],
    'coordinators' => [
        'callback' => 'local_placeholders\local\shortcodes::coordinators',
        'description' => 'shortcode:coordinators'
    ],
    'librarians' => [
        'callback' => 'local_placeholders\local\shortcodes::librarians',
        'description' => 'shortcode:librarians'
    ],
    'lecturers' => [
        'callback' => 'local_placeholders\local\shortcodes::lecturers',
        'description' => 'shortcode:lecturers'
    ],
    'timetable' => [
        'callback' => 'local_placeholders\local\shortcodes::timetable',
        'description' => 'shortcode:timetable'
    ],
    'startenddates' => [
        'callback' => 'local_placeholders\local\shortcodes::startenddates',
        'description' => 'shortcode:startenddates'
    ],
    'coursefield' => [
        'callback' => 'local_placeholders\local\shortcodes::coursefield',
        'description' => 'shortcode:coursefield'
    ],
    'contactcard' => [
        'callback' => 'local_placeholders\local\shortcodes::contactcard',
        'description' => 'shortcode:contactcard'
    ]
];
