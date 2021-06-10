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
 * Collection of shortcodes
 *
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['contactdetails'] = '{$a}\'s contact details:';
$string['contactskype'] = 'Contact {$a} using Skype for Business. Note: Skype for Business needs to be installed.';
$string['coursenoend'] = 'No end date set';
$string['coursestartend'] = '{$a->start} - {$a->end}';

$string['email'] = 'Email';
$string['enabled'] = 'Enabled';

$string['persona'] = 'Persona';
$string['persona_enableddesc'] = 'Enable output of Contact details';
$string['persona_showskype'] = 'Show Skype';
$string['persona_showskypedesc'] = 'Show Skype for Business sip link (this may not work for all users).';
$string['phone'] = 'Phone';
$string['pluginname'] = 'Placeholders';

$string['room'] = 'Room';
$string['roomdescription'] = 'Find {$a} on the map';

$string['shortcode:coordinators'] = 'Prints a contact card of all the Module coordinators enrolled on this module.';
$string['shortcode:coordinators_help'] = 'Module coordinators are automatically enrolled via SITS.
Please contact Modular if these details are not correct.';
$string['shortcode:coursefield'] = 'Custom course fields';
$string['shortcode:coursefield_help'] = 'Output any custom course field using [customfield name="fieldname"]';
$string['shortcode:lecturers'] = 'Prints a contact card of all the Lecturers enrolled on this module.';
$string['shortcode:lecturers_help'] = 'Lecturers are manually enrolled by Module coordinators.
Please contact your Module coordinators if these details are not correct.';
$string['shortcode:librarians'] = 'Prints a contact card of all the librarians enrolled on this module.';
$string['shortcode:librarians_help'] = 'Librarians need to be enrolled on the modules for their contact card to appear.';

$string['shortcode:modulecode'] = 'The shortcode associated with this module.';
$string['shortcode:modulecode_help'] = 'The shortcode is made up of a number of parts:

* Module code: (ABC101)
* Semester pattern: (S1, S2 or AY)
* Occurrence: Usually A, but can be any other letter depending on how many classes there are for this module
* Year: The academic year of this module';
$string['shortcode:modulelevel'] = 'Qualification level associated with this module.';
$string['shortcode:modulename'] = 'The title of this module.';

$string['shortcode:startenddates'] = 'Displays start and end dates (if specified)';

$string['shortcode:timetable'] = 'This is not currently live. Please do not use.';

$string['social'] = 'Social';
$string['socialtitle'] = '{$a->service} account for {$a->name}';
$string['skypeforbusiness'] = 'Skype for Business';