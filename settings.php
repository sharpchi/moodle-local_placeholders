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
 * Placeholder settings
 *
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$parent = new admin_category('local_placeholderscat', new lang_string('pluginname', 'local_placeholders'));
if ($hassiteconfig) {
    $ADMIN->add('localplugins', $parent);

    $name = 'local_placeholders/managesnippets';
    $title = new lang_string('managesnippets', 'local_placeholders');
    $url = new moodle_url('/local/placeholders/snippets.php');
    $externalpage = new admin_externalpage($name, $title, $url);

    $ADMIN->add('local_placeholderscat', $externalpage);

    $settings = new theme_boost_admin_settingspage_tabs('local_placeholders', get_string('pluginname', 'local_placeholders'));
    include('settings/persona.php');

    $ADMIN->add('local_placeholderscat', $settings);
}
