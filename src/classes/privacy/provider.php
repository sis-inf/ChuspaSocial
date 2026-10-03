<?php
namespace local_tuplugin\privacy; 

use core_privacy\local\metadata\collection;

class provider implements \core_privacy\local\metadata\provider {

    public static function get_metadata(collection $collection) : collection {
        
        $collection->add_database_table(
            'follow',
            [
                'userid' => 'privacy:metadata:follow:userid',
                'targetid' => 'privacy:metadata:follow:targetid',
            ],
            'privacy:metadata:follow'
        );

        $collection->add_database_table(
            'report',
            [
                'userid' => 'privacy:metadata:report:userid',
                'reporttext' => 'privacy:metadata:report:reporttext',
            ],
            'privacy:metadata:report'
        );

        $collection->add_database_table(
            'listing',
            [
                'userid' => 'privacy:metadata:listing:userid',
            ],
            'privacy:metadata:listing'
        );

        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:core_files');

        $collection->add_subsystem_link('core_tag', [], 'privacy:metadata:core_tag');

        return $collection;
    }
}