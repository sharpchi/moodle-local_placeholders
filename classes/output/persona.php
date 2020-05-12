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
 * Output class for a persona table.    
 * 
 * @package    local_placeholders
 * @copyright  2019 University of Chichester {@link http://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_placeholders\output;

use html_writer;
use moodle_url;
use renderer_base;
use renderable;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

class persona implements renderable, templatable {

    protected $people;

    /**
     * Array of userids to display persona tables.
     *
     * @param array $people Userids of module coordinators or lecturers or librarians.
     */
    public function __construct($people) {
        $this->people = $people;
    }

    public function export_for_template(renderer_base $output) {
        global $DB, $OUTPUT;
        $config = get_config('local_placeholders');
        if (!$config->persona_enabled) {
            return false;
        }
        $personas = new stdClass();
        $personas->people = [];
        if (empty($this->people)) {
            return false;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($this->people);
        $users = $DB->get_records_sql("SELECT * FROM {user} WHERE id $insql", $inparams);
        
        list($insql, $inparams) = $DB->get_in_or_equal(['twitter', 'instagram', 'room']);
        $profilefields = $DB->get_records_sql("SELECT * FROM {user_info_field} WHERE shortname $insql", $inparams);

        $postcodere = '/[CB][0-9]+\-[0-9]\-[0-9]+/';
        
        foreach ($users as $user) {
            $persona = new stdClass();
            $persona->name = fullname($user);
            $persona->email = $user->email;
            $persona->photo = $OUTPUT->user_picture($user);
            $persona->phone = $user->phone1;
            if (isset($config->persona_showskype) && $config->persona_showskype) {
                $persona->skype = 1;
            }
            $social = [];
            foreach ($profilefields as $profilefield) {
                $fieldvalue = $DB->get_field('user_info_data', 'data', ['userid' => $user->id, 'fieldid' => $profilefield->id]);
                if (!$fieldvalue) {
                    continue;
                }
                
                switch ($profilefield->shortname) {
                    case 'twitter':
                    case 'instagram':
                        $item = new stdClass();
                        $item->handle = str_replace('@', '', $fieldvalue);
                        $item->faicon = $profilefield->shortname;
                        $item->service = ucfirst($profilefield->shortname);
                        $item->baseurl = 'https://' . $profilefield->shortname . '.com/';
                        $item->person = $persona->name;
                        $social[] = $item;
                        break;
                    case 'phone':
                        $persona->phone = $fieldvalue;
                        break;
                    case 'room':
                        $room = $fieldvalue;
                        if (preg_match($postcodere, $room) === 1) {
                            $roomurl = new moodle_url('https://maps.chi.ac.uk/', [], 'room=' . $room);
                            $room = html_writer::link($roomurl, $room, ['title' => get_string('roomdescription', 'local_placeholders', $room)]);
                        }
                        $persona->room = $room; // Make into map link.
                        break;
                }
            }
            if (count($social)>0) {
                // Add a last property to the last item in the social array. This allows for a comma separated list in the template.
                // It's ugly.
                $social[count($social) - 1]->last = 1;
                $persona->social = new stdClass();
                $persona->social->accounts = $social;
            }

            $personas->people[] = $persona;
        }


        return $personas;
    }
}