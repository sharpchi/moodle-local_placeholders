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

$string['calllandline'] = 'Call {$a} on the phone';
$string['callmobile'] = 'Call {$a} on their mobile';
$string['callonteams'] = 'Call {$a} on Teams';
$string['callskype'] = 'Call {$a} on Skype';
$string['chatwithonteams'] = 'Chat with {$a} on Teams';
$string['contact'] = 'Contact';
$string['contactdetails'] = '{$a}\'s contact details:';
$string['contactemail'] = 'Email {$a}';
$string['contactskype'] = 'Contact {$a} using Skype for Business. Note: Skype for Business needs to be installed.';
$string['coursenoend'] = 'No end date set';
$string['coursestartend'] = '<time datetime="{$a->startmachine}">{$a->start}</time> - <time datetime="{$a->endmachine}">{$a->end}</time>';

$string['email'] = 'Email';
$string['emailperson'] = 'Email {$a}';
$string['enabled'] = 'Enabled';

$string['ipphone'] = 'IP Phone';

$string['persona'] = 'Persona';
$string['persona_enableddesc'] = 'Enable output of Contact details';
$string['persona_profilefieldiconmap'] = 'Font awesome icons for profile fields';
$string['persona_profilefieldiconmapdesc'] = 'One entry per line, the font-awesome icon to be used next to a profile field entry. Format: fieldshortname=fa-envelope';
$string['persona_profilefields'] = 'Available profile fields';
$string['persona_profilefieldsdesc'] = 'The profile fields that can be displayed in a persona contact card';
$string['persona_roles'] = 'Valid persona roles';
$string['persona_rolesdesc'] = 'Only these selected roles will be allowed for presenting a persona contact card. All others will not - for privacy reasons.';
$string['persona_showskype'] = 'Show Skype';
$string['persona_showskypedesc'] = 'Show Skype for Business sip link (this may not work for all users).';
$string['persona_userfields'] = 'Display userfields';
$string['persona_userfieldsdesc'] = 'Only display these user fields';
$string['phone'] = 'Phone';
$string['pluginname'] = 'Placeholders';


$string['room'] = 'Room';
$string['roomdescription'] = 'Find {$a} on the map';

$string['shortcode:contactcard'] = 'Prints a contact card for the given role.';
$string['shortcode:contactcard_help'] = 'Use the following format: [contactcard role="editingteacher"].
Note: role shortnames are used. If you are using an invalid name, nothing will be output.';
$string['shortcode:coordinators'] = 'Prints a contact card of all the Module coordinators enrolled on this module.';
$string['shortcode:coordinators_help'] = 'Module coordinators are automatically enrolled via SITS.
Please contact Modular if these details are not correct.';
$string['shortcode:coursefield'] = 'Custom course fields';
$string['shortcode:coursefield_help'] = 'Output any custom course field using [coursefield name="fieldname"]';
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
$string['skypeforbusiness'] = 'Skype {$a}';

$string['website'] = 'Website';
