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

namespace local_placeholders\reportbuilder\local\entities;

use context_system;
use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Class placeholder
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snippet extends base {

    /**
     * default tables
     *
     * @return array
     */
    protected function get_default_tables(): array {
        return [
            'local_placeholders_snippet',
        ];
    }

    /**
     * The default title for this entity in the list of columns/conditions/filters in the report builder
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('snippet', 'local_placeholders');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }
        return $this;
    }

    /**
     * Get all columns for the entity
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $phalias = $this->get_table_alias('local_placeholders_snippet');

        $columns[] = (new column(
            'id',
            new lang_string('id', 'local_placeholders'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$phalias}.id")
            ->set_is_sortable(true);

        $columns[] = (new column(
            'code',
            new lang_string('placeholdercode', 'local_placeholders'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$phalias}.id")
            ->add_field("{$phalias}.slug")
            ->add_callback(static function($id, $row): string {
                return "[snippet id=\"{$id}\" slug=\"{$row->slug}\"]";
            });

        $columns[] = (new column(
            'slug',
            new lang_string('slug', 'local_placeholders'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true)
            ->add_field("{$phalias}.slug");

        $columns[] = (new column(
            'title',
            new lang_string('title', 'local_placeholders'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true)
            ->add_field("{$phalias}.title");

        $columns[] = (new column(
            'content',
            new lang_string('content', 'local_placeholders'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$phalias}.content")
            ->add_fields("{$phalias}.contentformat, {$phalias}.id")
            ->add_callback(static function(?string $content, stdClass $snippet): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");
                if ($content == null) {
                    return '';
                }
                $context = context_system::instance();
                $content = file_rewrite_pluginfile_urls(
                    // The content of the text stored in the database.
                    $content,
                    // The pluginfile URL which will serve the request.
                    'pluginfile.php',
                    // The combination of contextid / component / filearea / itemid
                    // form the virtual bucket that file are stored in.
                    $context->id,
                    'local_placeholders',
                    'snippet',
                    $snippet->id
                );
                return format_text($content, $snippet->contentformat, ['context' => $context->id]);
            });

        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'local_placeholders'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$phalias}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'], get_string('strftimedatetimeshortaccurate', 'core_langconfig'));

            $columns[] = (new column(
                'timemodified',
                new lang_string('timemodified', 'local_placeholders'),
                $this->get_entity_name()
            ))
                ->add_joins($this->get_joins())
                ->set_type(column::TYPE_TIMESTAMP)
                ->add_field("{$phalias}.timecreated")
                ->set_is_sortable(true)
                ->add_callback([format::class, 'userdate'], get_string('strftimedatetimeshortaccurate', 'core_langconfig'));
        return $columns;
    }

    /**
     * All filters for the entity
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $phalias = $this->get_table_alias('local_placeholders_snippet');

        $filters[] = (new filter(
            text::class,
            'title',
            new lang_string('title', 'local_placeholders'),
            $this->get_entity_name(),
            "{$phalias}.title"
        ))
            ->add_joins($this->get_joins());

        $filters[] = (new filter(
            text::class,
            'content',
            new lang_string('content', 'local_placeholders'),
            $this->get_entity_name(),
            "{$phalias}.content"
        ))
            ->add_joins($this->get_joins());
        return $filters;
    }
}
