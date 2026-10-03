<?php
namespace local_tuplugin\privacy; 

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;

class provider implements 
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_user_data_provider {

    /**
     * Devuelve los contextos donde el usuario tiene datos.
     *
     * @param int $userid El ID del usuario.
     * @return contextlist La lista de contextos.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        
        global $DB;
        
        $sql = "SELECT ctx.id 
                FROM {context} ctx
                JOIN {tu_tabla_de_datos} d ON d.courseid = ctx.instanceid
                WHERE ctx.contextlevel = ? AND d.userid = ?";
                
        $params = [CONTEXT_COURSE, $userid];
        
        $contextlist->add_from_sql($sql, $params);


        return $contextlist;
    }

}