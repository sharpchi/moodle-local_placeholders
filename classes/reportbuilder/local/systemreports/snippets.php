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

namespace local_placeholders\reportbuilder\local\systemreports;

use context_system;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\action;
use core_reportbuilder\system_report;
use lang_string;
use local_placeholders\reportbuilder\local\entities\snippet;
use moodle_url;
use pix_icon;

/**
 * Class placeholder
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snippets extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Placeholder for the report builder.
        $entitymain = new snippet();
        $entitymainalias = $entitymain->get_table_alias('local_placeholders_snippet');

        $this->set_main_table('local_placeholders_snippet', $entitymainalias);
        $this->add_entity($entitymain);
        $this->add_base_fields("{$entitymainalias}.id");

        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser->add_join(
            "LEFT JOIN {user} {$entityuseralias}
                    ON {$entityuseralias}.id = {$entitymainalias}.usermodified"
        ));

        $this->add_columns();
        $this->add_filters();
        $this->add_actions();
        $this->set_downloadable(false);
    }

    /**
     * Can user view this report
     *
     * @return boolean
     */
    public function can_view(): bool {
        return has_capability('local/placeholders:managesnippets', context_system::instance());
    }

    /**
     * The columns visible in this report
     *
     * @return void
     */
    public function add_columns(): void {
        $this->add_columns_from_entities([
            'snippet:id',
            'snippet:code',
            'snippet:title',
            'snippet:content',
            'user:fullname',
            'snippet:timecreated',
            'snippet:timemodified',
        ]);

        $this->get_column('user:fullname')
            ->set_title(new lang_string('modifiedby', 'local_placeholders'));
    }

    /**
     * Filters available
     *
     * @return void
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'snippet:title',
            'snippet:content',
        ]);
    }

    /**
     * Possible actions for this snippet.
     *
     * @return void
     */
    protected function add_actions(): void {
        $this->add_action((new action(
            new moodle_url('/local/placeholders/snippet.php', [
                'id' => ':id',
                'action' => 'edit',
            ]),
            new pix_icon('t/edit', ''),
            [],
            false,
            new lang_string('edit'),
        )));

        $this->add_action((new action(
            new moodle_url('/local/placeholders/snippet.php', [
                'id' => ':id',
                'action' => 'delete',
            ]),
            new pix_icon('t/delete', ''),
            [
                'class' => 'text-danger',
            ],
            false,
            new lang_string('delete'),
            new lang_string('confirmdeletesnippet', 'local_placeholders')
        )));
    }
}
