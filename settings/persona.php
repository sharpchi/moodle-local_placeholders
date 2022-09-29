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
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/placeholders/locallib.php');

$page = new admin_settingpage('local_placeholders_persona', get_string('persona', 'local_placeholders'));

// Show warning for hidden resources/activities.
$name = 'local_placeholders/persona_enabled';
$title = get_string('enabled', 'local_placeholders');
$description = get_string('persona_enableddesc', 'local_placeholders');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$page->add($setting);

$name = 'local_placeholders/persona_showskype';
$title = get_string('persona_showskype', 'local_placeholders');
$description = get_string('persona_showskypedesc', 'local_placeholders');
$setting = new admin_setting_configcheckbox($name, $title, $description, true);
$page->add($setting);

$name = 'local_placeholders/persona_roles';
$title = new lang_string('persona_roles', 'local_placeholders');
$description = new lang_string('persona_rolesdesc', 'local_placeholders');
$options = \local_placeholders\get_course_roles_menu();
$setting = new admin_setting_configmultiselect($name, $title, $description, [], $options);
$page->add($setting);

$name = 'local_placeholders/persona_userfields';
$title = new lang_string('persona_userfields', 'local_placeholders');
$description = new lang_string('persona_userfieldsdesc', 'local_placeholders');
$options = [
    'phone1' => 'Landline',
    'phone2' => 'Mobile',
    'teamchat' => 'Teams chat',
    'teamcall' => 'Teams call',
    'skype' => 'Skype',
    'url' => 'Web page'
];
$setting = new admin_setting_configmultiselect($name, $title, $description, [], $options);
$page->add($setting);

$name = 'local_placeholders/persona_profilefields';
$title = new lang_string('persona_profilefields', 'local_placeholders');
$description = new lang_string('persona_profilefieldsdesc', 'local_placeholders');

// Gets available profile fields, grouped by category sort order and field sort order.
// The output will also reflect this.
$options = $DB->get_records_sql_menu('SELECT uif.shortname, uif.name
    FROM {user_info_field} uif
    JOIN {user_info_category} uic ON uic.id = uif.categoryid
    WHERE uif.visible = 2
    ORDER BY uic.sortorder ASC, uif.sortorder');
$setting = new admin_setting_configmultiselect($name, $title, $description, [], $options);
$page->add($setting);

$name = 'local_placeholders/persona_profilefieldiconmap';
$title = new lang_string('persona_profilefieldiconmap', 'local_placeholders');
$description = new lang_string('persona_profilefieldiconmapdesc', 'local_placeholders');
$setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_TEXT);
$page->add($setting);

$settings->add($page);
