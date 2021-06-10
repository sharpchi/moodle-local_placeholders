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
 * Placeholders upgrade file.
 * @package   local_placeholders
 * @author    Mark Sharp <m.sharp@chi.ac.uk>
 * @copyright 2020 University of Chichester {@link https://www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/local/placeholders/locallib.php');

/**
 * Upgrade function called by Moodle.
 *
 * @param int $oldversion Old version of the plugin.
 * @return bool
 */
function xmldb_local_placeholders_upgrade($oldversion) {
    if ($oldversion < 2020051302) {
        $fields = ['linkedin'];
        foreach ($fields as $field) {
            \local_placeholders\set_userinfofield($field);
        }
        upgrade_plugin_savepoint(true, '2020051302', 'local', 'placeholders');
    }

    return true;
}