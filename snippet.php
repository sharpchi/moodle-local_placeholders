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
 * TODO describe file snippet
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_placeholders\entity\snippet;
use local_placeholders\form\snippet_form;

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login(null, false);

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', 'new', PARAM_ALPHA);
$confirmdelete = optional_param('confirmdelete', null, PARAM_BOOL);

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

if (!in_array($action, ['edit', 'delete', 'new'])) {
    $action = 'new';
}
$pageparams = [
    'action' => $action,
    'id' => $id,
];

admin_externalpage_setup('local_placeholders/managesnippets', '', $pageparams, '/local/placeholders/snippet.php');
require_capability('local/placeholders:managesnippets', $context);

$editoroptions = ['maxfiles' => -1, 'noclean' => true, 'context' => $context];
$snippet = new snippet($id);
$customdata = [
    'persistent' => $snippet,
    'user' => $USER->id,
];

if ($confirmdelete && confirm_sesskey()) {
    $slug = $snippet->get('slug');
    $snippet->delete();
    redirect(new moodle_url('/local/placeholders/snippets.php'), get_string('snippetdeleted', 'local_placeholders', $slug),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

$url = new moodle_url('/local/placeholders/snippet.php', $pageparams);
$PAGE->set_url($url);
$PAGE->navbar->add(get_string('localplugins'), new moodle_url('/admin/category.php?category=localplugins'));
$PAGE->navbar->add(
    get_string('pluginname', 'local_placeholders'),
    new moodle_url('/admin/category.php?category=local_placeholderscat')
);
$PAGE->navbar->add(
    get_string('managesnippets', 'local_placeholders'),
    new moodle_url('/local/placeholders/snippets.php')
);

$form = new snippet_form($PAGE->url->out(false), $customdata);
if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/placeholders/snippets.php'));
} else if ($formdata = $form->get_data()) {
    if (empty($formdata->id)) {
        $snippet = new snippet(0, $formdata);
        $snippet->create();
        $data = new stdClass();
        $data->id = $snippet->get('id');
        $data->contentformat = FORMAT_HTML;
        $data->content_editor = [
            'text' => $snippet->get('content'),
            'format' => FORMAT_HTML,
            'itemid' => $snippet->get('id'),
        ];
        $data = file_postupdate_standard_editor(
            $data,
            'content',
            $editoroptions,
            context_system::instance(),
            'local_placeholders',
            'snippet',
            $snippet->get('id')
        );
        $snippet->set('content', $data->content);
        $snippet->update();
        redirect(new moodle_url('/local/placeholders/snippets.php'),
            get_string('snippetcreated', 'local_placeholders', $formdata->slug),
            null,
            \core\output\notification::NOTIFY_INFO
        );
    } else {
        $snippet = new snippet($formdata->id);
        if ($action == 'edit') {
            $snippet->from_record($formdata);
            $data = new stdClass();
            $data->id = $snippet->get('id');
            $data->content = $snippet->get('content');
            $data->contentformat = FORMAT_HTML;
            $data->content_editor = [
                'text' => $snippet->get('content'),
                'format' => FORMAT_HTML,
                'itemid' => $snippet->get('id'),
            ];
            $data = file_postupdate_standard_editor(
                $data,
                'content',
                $editoroptions,
                context_system::instance(),
                'local_placeholders',
                'snippet',
                $snippet->get('id')
            );
            $snippet->set('content', $data->content);
            $snippet->update();
            redirect(new moodle_url('/local/placeholders/snippets.php'),
                get_string('snippetupdated', 'local_placeholders', $formdata->slug),
                null,
                \core\output\notification::NOTIFY_INFO
            );
        }
    }
}

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

if ($action == 'delete') {
    $heading = new lang_string('confirmdeletesnippet', 'local_placeholders');
    echo html_writer::tag('h3', $heading);
    $deleteurl = new moodle_url('/local/placeholders/snippet.php', [
        'id' => $snippet->get('id'),
        'confirmdelete' => true,
        'sesskey' => sesskey(),
    ]);
    $deletebutton = new single_button($deleteurl, get_string('delete'), 'post');
    echo $OUTPUT->confirm(get_string('confirmdeletesnippet', 'local_placeholders', $snippet->get('slug')), $deletebutton,
        new moodle_url('/local/placeholders/snippets.php')
    );
} else {
    $heading = new lang_string('newsnippet', 'local_placeholders');
    if ($id > 0) {
        $heading = new lang_string('editsnippet', 'local_placeholders');
    }
    echo html_writer::tag('h3', $heading);
    $form->display();
}

echo $OUTPUT->footer();
