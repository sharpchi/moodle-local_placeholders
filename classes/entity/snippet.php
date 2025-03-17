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

namespace local_placeholders\entity;

use core\persistent;
use lang_string;

/**
 * Class snippet
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snippet extends persistent {
    /**
     * Table name for snippets.
     */
    const TABLE = 'local_placeholders_snippet';

    /**
     * Additional properties for the snippet entity.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'slug' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'title' => [
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'content' => [
                'type' => PARAM_RAW,
                'null' => NULL_NOT_ALLOWED,
            ],
            'contentformat' => [
                'choices' => [FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN],
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
            ],
            'courses' => [
                'type' => PARAM_SEQUENCE,
                'default' => '',
            ],
            'categories' => [
                'type' => PARAM_SEQUENCE,
                'default' => '',
            ],
        ];
    }

    /**
     * Make sure the slug is sluggish.
     *
     * @param string $slug
     * @return bool|lang_string
     */
    protected function validate_slug(string $slug) {
        // Do not allow url type chars ?&%=# or spaces).
        if (preg_match('/[\?&%=# ]/', $slug, $matches) !== 0) {
            return new lang_string('invalidcharsinslug', 'local_placeholders');
        }

        $currentid = self::get('id');
        // If this is a new record, and the slug exists, then reject.
        if ($currentid == 0 && static::record_exists_select('slug = ?', [$slug])) {
            return new lang_string('duplicate_slug', 'local_placeholders');
        }

        // If this is an existing record, and the id is not the same as this one, then reject it.
        $records = static::get_records(['slug' => $slug]);
        foreach ($records as $record) {
            if ($record->get('id') != $currentid) {
                return new lang_string('duplicate_slug', 'local_placeholders');
            }
        }
        return true;
    }
}
