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
 * Settings for the persona template
 * 
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link http://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage('local_placeholders_persona', get_string('persona', 'local_placeholders'));

// Show warning for hidden resources/activities.
$name = 'local_placeholders/persona_enabled';
$title = get_string('enabled', 'local_placeholders');
$description = get_string('persona_enableddesc', 'local_placeholders');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
// $setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'local_placeholders/persona_showskype';
$title = get_string('persona_showskype', 'local_placeholders');
$description = get_string('persona_showskypedesc', 'local_placeholders');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
// $setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


$settings->add($page);