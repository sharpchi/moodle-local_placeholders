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

namespace local_placeholders\reportbuilder\datasource;

use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use local_placeholders\reportbuilder\local\entities\snippet;

/**
 * Class snippets
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snippets extends datasource {

    /**
     * Initialise the datasource
     *
     * @return void
     */
    protected function initialise(): void {
        $snippetentity = new snippet();
        $snippetentityalias = $snippetentity->get_table_alias('local_placeholders_snippet');
        $snippetentityname = $snippetentity->get_entity_name();
        $this->add_entity($snippetentity);
        $this->set_main_table('local_placeholders_snippet', $snippetentityalias);

        $userentity = new user();
        $this->add_entity($userentity);
        $userentityalias = $userentity->get_table_alias('user');
        $this->add_join("LEFT JOIN {user} {$userentityalias} ON {$userentityalias}.id = {$snippetentityalias}.usermodified");

        $this->add_columns_from_entity($snippetentityname);
        $this->add_filters_from_entity($snippetentityname);
        $this->add_conditions_from_entity($snippetentityname);
        $this->set_downloadable(false);
    }

    /**
     * Get datasource name
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('snippets', 'local_placeholders');
    }

    /**
     * Return the columns that will be added to the report once it's created
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'snippet:id',
            'snippet:code',
            'snippet:title',
            'snippet:content',
            'snippet:timemodified',
        ];
    }

    /**
     * Return the fitlers that will be added to the report.
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'snippet:title',
            'snippet:content',
            'snippet:timecreated',
        ];
    }

    /**
     * Return conditions
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [];
    }

    /**
     * Default sorting
     *
     * @return array
     */
    public function get_default_column_sorting(): array {
        return [
            'snippet:title' => 'ASC',
        ];
    }
}
