<?php
namespace local_placeholders\output;

use renderer_base;
use renderable;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

class timetable implements renderable, templatable {

    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function export_for_template(renderer_base $output) {
        $data = $this->data;

        return $data;
    }
}
