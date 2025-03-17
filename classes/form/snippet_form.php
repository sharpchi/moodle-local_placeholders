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

namespace local_placeholders\form;

use context_system;
use core\form\persistent;
use local_placeholders\entity\snippet;

/**
 * Class snippet
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snippet_form extends persistent {
    /**
     * Snippet class.
     *
     * @var string
     */
    protected static $persistentclass = snippet::class;

    /**
     * Form definition
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'title', get_string('title', 'local_placeholders'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'slug', get_string('slug', 'local_placeholders'));
        $mform->setType('slug', PARAM_TEXT);
        $mform->addRule('slug', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('slug', 'slug', 'local_placeholders');

        $mform->addElement('editor', 'content', get_string('content', 'local_placeholders'), null, $this->get_editor_options());
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('required'), 'required');

        $mform->addElement('course', 'courses', get_string('restricttocourses', 'local_placeholders'), [
            'multiple' => true,
            'showhidden' => true,
        ]);
        $mform->setType('courses', PARAM_SEQUENCE);

        $this->add_action_buttons();
    }

    /**
     * Prepare the content field for embedded images
     *
     * @param stdClass $data
     * @return void
     */
    public function set_data($data) {
        $id = $data->id ?? null;
        $data->contentformat = FORMAT_HTML;
        $data = file_prepare_standard_editor(
            $data,
            'content',
            $this->get_editor_options(),
            context_system::instance(),
            'local_placeholders',
            'snippet',
            $id
        );
        $data->content['text'] = $data->content_editor['text']['text'];
        parent::set_data($data);
    }

    /**
     * Options used by the text editor
     *
     * @return array
     */
    public function get_editor_options() {
        return ['maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => context_system::instance()];
    }
}
