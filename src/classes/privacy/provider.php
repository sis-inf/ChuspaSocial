<?php
namespace local_ChuspaSocial\privacy; 

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;

class provider implements 
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_user_data_provider {

    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        $sql_course = "SELECT ctx.id 
                       FROM {context} ctx
                       JOIN {local_ChuspaSocial_data} d ON d.courseid = ctx.instanceid
                       WHERE ctx.contextlevel = :contextlevel 
                       AND d.userid = :userid";
                       
        $params_course = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid
        ];
        
        $contextlist->add_from_sql($sql_course, $params_course);

        $sql_system = "SELECT ctx.id 
                       FROM {context} ctx
                       JOIN {local_ChuspaSocial_preferences} p ON p.userid = ctx.instanceid
                       WHERE ctx.contextlevel = :contextlevel 
                       AND p.userid = :userid";
                       
        $params_system = [
            'contextlevel' => CONTEXT_SYSTEM,
            'userid' => $userid
        ];
        
        $contextlist->add_from_sql($sql_system, $params_system);
        return $contextlist;
    }

}