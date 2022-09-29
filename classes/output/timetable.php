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
 * File for Timetables.
 * 
 * Not currently being used as we don't have any link to a Timetable.
 *
 * @package   local_placeholders
 * @author    Mark Sharp <m.sharp@chi.ac.uk>
 * @copyright 2021 University of Chichester {@link https://www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_placeholders\output;

use renderable;
use renderer_base;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Timetable renderer class.
 */
class timetable implements renderable, templatable {

    /**
     * The data for the timetable.
     *
     * @var object
     */
    protected $data;

    /**
     * Constructor
     *
     * @param object $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Export data for template.
     *
     * @param renderer_base $output
     * @return object
     */
    public function export_for_template(renderer_base $output) {
        $data = $this->data;

        return $data;
    }
}
