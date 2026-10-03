<?php
namespace local_tuplugin\privacy; 

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\provider as metadata_provider;

class provider implements metadata_provider {

    public static function get_metadata(collection $collection) : collection {
        
        $collection->add_database_table(
            'post',
            [
                'userid' => 'privacy:metadata:post:userid',
                'content' => 'privacy:metadata:post:content',
                'created' => 'privacy:metadata:post:created',
            ],
            'privacy:metadata:post'
        );

        $collection->add_database_table(
            'comment',
            [
                'userid' => 'privacy:metadata:comment:userid',
                'postid' => 'privacy:metadata:comment:postid',
                'content' => 'privacy:metadata:comment:content',

            ],
            'privacy:metadata:comment'
        );

        $collection->add_database_table(
            'reaction',
            [
                'userid' => 'privacy:metadata:reaction:userid',
                'postid' => 'privacy:metadata:reaction:postid',
                'reactiontype' => 'privacy:metadata:reaction:reactiontype',
            ],
            'privacy:metadata:reaction'
        );

        return $collection;
    }
}