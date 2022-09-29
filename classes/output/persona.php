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
 * @copyright  2019 University of Chichester {@link https://www.chi.ac.uk}
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

/**
 * Personal contact details card
 */
class persona implements renderable, templatable {

    /**
     * List of userids to print contact details of
     *
     * @var array
     */
    protected $people;

    /**
     * Array of userids to display persona tables.
     *
     * @param array $people Userids of module coordinators or lecturers or librarians.
     * @param string $title Optional title
     */
    public function __construct($people, $title = '') {
        $this->people = $people;
        $this->title = $title;
    }

    /**
     * Export data for the template.
     *
     * @param renderer_base $output
     * @return object|bool
     */
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
        if (!empty($this->title)) {
            $personas->title = $this->title;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($this->people);
        $users = $DB->get_records_sql("SELECT * FROM {user} WHERE id $insql", $inparams);
        // Get selected info fields. Detect if they are urls, if they are make into a link and pass that in.

        $selectedprofilefields = explode(',', $config->persona_profilefields);
        list($insql, $inparams) = $DB->get_in_or_equal($selectedprofilefields);
        // Only public fields can be displayed.
        $profilefields = $DB->get_records_sql("SELECT uif.*
            FROM {user_info_field} uif
            JOIN {user_info_category} uic ON uic.id = uif.categoryid
            WHERE uif.visible = 2 AND uif.shortname $insql
            ORDER BY uic.sortorder ASC, uif.sortorder", $inparams);

        $selectediconsrows = explode("\n", $config->persona_profilefieldiconmap);
        $selectedicons = [];
        foreach ($selectediconsrows as $row) {
            if (empty(trim($row))) {
                continue;
            }
            if (strpos($row, '=') === false) {
                continue;
            }
            list($key, $icon) = explode('=', $row);
            $selectedicons[$key] = $icon;
        }
        $selecteduserfields = explode(',', $config->persona_userfields);
        foreach ($users as $user) {
            $persona = new stdClass();
            $persona->name = fullname($user);
            $persona->email = $user->email;
            $persona->photo = $OUTPUT->user_picture($user, ['size' => 96]);
            foreach ($selecteduserfields as $selecteduserfield) {
                switch ($selecteduserfield) {
                    case 'teamchat':
                        $item = new stdClass();
                        $item->url = 'https://teams.microsoft.com/l/chat/0/0?users=' . $persona->email;
                        $item->icon = 'fa-comments';
                        $item->content = get_string('chatwithonteams', 'local_placeholders', $persona->name);
                        $persona->chat[] = $item;
                        $persona->haschat = true;
                        break;
                    case 'teamcall':
                        $item = new stdClass();
                        $item->url = 'https://teams.microsoft.com/l/call/0/0?users=' . $persona->email;
                        $item->icon = 'fa-headphones';
                        $item->content = get_string('callonteams', 'local_placeholders', $persona->name);
                        $persona->ipphone[] = $item;
                        $persona->hasipphone = true;
                        break;
                    case 'phone1':
                        if (empty(trim($user->phone1))) {
                            continue 2;
                        }
                        $item = new stdClass();
                        $item->url = 'call:' . $user->phone1;
                        $item->icon = 'fa-phone';
                        $item->content = get_string('calllandline', 'local_placeholders', $persona->name);
                        $persona->phone[] = $item;
                        $persona->hasphone = true;
                        break;
                    case 'phone2':
                        if (empty($user->{$selecteduserfield})) {
                            continue 2;
                        }
                        $item = new stdClass();
                        $item->url = 'call:' . $user->phone2;
                        $item->icon = 'fa-mobile';
                        $item->content = get_string('callmobile', 'local_placeholders', $persona->name);
                        $persona->phone[] = $item;
                        $persona->hasphone = true;
                        break;
                    case 'skype':
                        if (empty($user->{$selecteduserfield})) {
                            continue 2;
                        }
                        $item = new stdClass();
                        $item->url = 'sip:' . $user->skype;
                        $item->icon = 'fa-skype';
                        $item->content = get_string('callskype', 'local_placeholders', $persona->name);
                        $persona->ipphone[] = $item;
                        $persona->hasipphone = true;
                        break;
                    case 'url':
                        if (empty($user->url) || !filter_var($user->url, FILTER_VALIDATE_URL)) {
                            continue 2;
                        }
                        $item = new stdClass();
                        // Strip http(s) and readd it. Ensures using https, and adds it if not present.
                        $url = preg_replace('/http(s)?:\/\//', '', $user->url);
                        $item->url = 'https://' . $url;
                        $item->icon = 'fa-globe';
                        $item->content = $url;
                        $persona->link = $item;
                        $persona->hasurl = true;
                        break;
                }
            }

            $persona->profilefields = [];

            foreach ($profilefields as $profilefield) {
                $field = $DB->get_record('user_info_data', ['userid' => $user->id, 'fieldid' => $profilefield->id]);
                if (!$field || empty(trim($field->data))) {
                    continue;
                }
                $entry = new stdClass();
                $entry->label = $profilefield->name;
                // Urls have param4 set.
                if (!empty($profilefield->param4)) {
                    $entry->url = str_replace('$$', $field->data, $profilefield->param4);
                }
                if (isset($selectedicons[$profilefield->shortname])) {
                    $entry->icon = $selectedicons[$profilefield->shortname];
                }
                $entry->content = $field->data;
                $persona->profilefields[] = $entry;
            }
            $personas->people[] = $persona;
        }
        return $personas;
    }
}
