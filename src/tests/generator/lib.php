
<?php
// This file is part of Moodle - http://moodle.org/
//
// Generator for ChuspaSocial tests.

defined('MOODLE_INTERNAL') || die();

/**
 * Generator for the local_chuspasocial component.
 *
 * @package    local_chuspasocial
 * @category   test
 */
class local_chuspasocial_generator extends component_generator_base {

    /**
     * Counter used to generate unique follow records.
     *
     * @var int
     */
    protected $followcount = 0;

    /**
     * Create a follow record for testing.
     *
     * @param array $record Data for the follow record.
     * @return \local_chuspasocial\local\persistent\follow
     */
    public function create_follow(array $record = []) {
        $this->followcount++;

        $defaults = [
            'userid' => 2,
            'itemtype' => 'user',
            'itemid' => 1000 + $this->followcount,
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);

        return \local_chuspasocial\local\persistent\follow::create($record);
    }
}
