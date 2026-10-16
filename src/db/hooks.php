<?php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => '\core\hook\navigation\primary_extend',
        'callback' => '\local_chuspasocial\local\hook_callbacks::extend_primary_navigation',
    ],
];
