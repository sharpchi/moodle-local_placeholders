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
 * TODO describe file snippets
 *
 * @package    local_placeholders
 * @copyright  2025 Southampton Solent University {@link https://www.solent.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_reportbuilder\permission;
use core_reportbuilder\system_report_factory;
use local_placeholders\reportbuilder\local\systemreports\snippets;

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_placeholders/managesnippets', '', null, '/local/placeholders/snippets.php');
$context = context_system::instance();
require_capability('local/placeholders:managesnippets', $context);

$url = new moodle_url('/local/placeholders/snippets.php', []);
$PAGE->set_url($url);
$PAGE->set_context($context);

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();


$new = new action_link(new moodle_url('/local/placeholders/snippet.php', ['action' => 'new']),
    get_string('addnewsnippet', 'local_placeholders'),
    null,
    ['class' => 'btn btn-primary'],
    new pix_icon('e/cut', get_string('addnewsnippet', 'local_placeholders'))
);
echo $OUTPUT->render($new);

$report = system_report_factory::create(snippets::class, $context);
echo $report->output();

echo $OUTPUT->footer();
