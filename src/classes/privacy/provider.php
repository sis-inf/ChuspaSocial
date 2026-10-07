<?php
namespace local_chuspasocial\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\plugin\provider as plugin_provider;
use core_privacy\local\metadata\provider as metadata_provider;
use core_privacy\local\request\writer;

class provider implements metadata_provider, plugin_provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_chuspasocial_posts', [
            'userid' => 'privacy:metadata:local_chuspasocial_posts:userid',
            'content' => 'privacy:metadata:local_chuspasocial_posts:content',
            'timecreated' => 'privacy:metadata:local_chuspasocial_posts:timecreated',
        ], 'privacy:metadata:local_chuspasocial_posts');

        $collection->add_database_table('local_chuspasocial_comments', [
            'userid' => 'privacy:metadata:local_chuspasocial_comments:userid',
            'comment' => 'privacy:metadata:local_chuspasocial_comments:comment',
        ], 'privacy:metadata:local_chuspasocial_comments');


        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {local_chuspasocial_posts} p ON p.userid = ctx.instanceid
                 WHERE ctx.contextlevel = :contextlevel
                   AND p.userid = :userid";
        
        $params = [
            'contextlevel' => CONTEXT_USER, 
            'userid' => $userid
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        foreach ($contextlist->get_contexts() as $context) {
            
            // 1. Exportar Publicaciones
            $posts = $DB->get_records('local_chuspasocial_posts', ['userid' => $userid]);
            if (!empty($posts)) {
                $data = [];
                foreach ($posts as $post) {
                    $data[] = (object)[
                        'contenido' => $post->content,
                        'fecha' => date('Y-m-d H:i:s', $post->timecreated),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('privacy:path:posts', 'local_chuspasocial')],
                    (object)['posts' => $data]
                );
            }

            // 2. Exportar Comentarios
            $comments = $DB->get_records('local_chuspasocial_comments', ['userid' => $userid]);
            if (!empty($comments)) {
                // ... lógica de formateo ...
                writer::with_context($context)->export_data(
                    [get_string('privacy:path:comments', 'local_chuspasocial')],
                    (object)['comments' => $comments]
                );
            }

            // 3. Exportar Reacciones
            $reactions = $DB->get_records('local_chuspasocial_reactions', ['userid' => $userid]);
            if (!empty($reactions)) {
                writer::with_context($context)->export_data(
                    [get_string('privacy:path:reactions', 'local_chuspasocial')],
                    (object)['reactions' => $reactions]
                );
            }

            // 4. Exportar Seguimientos
            $follows = $DB->get_records('local_chuspasocial_follows', ['userid' => $userid]);
            if (!empty($follows)) {
                writer::with_context($context)->export_data(
                    [get_string('privacy:path:follows', 'local_chuspasocial')],
                    (object)['follows' => $follows]
                );
            }

            // 5. Exportar Anuncios
            $announcements = $DB->get_records('local_chuspasocial_announcements', ['userid' => $userid]);
            if (!empty($announcements)) {
                writer::with_context($context)->export_data(
                    [get_string('privacy:path:announcements', 'local_chuspasocial')],
                    (object)['announcements' => $announcements]
                );
            }
        }
    }
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Implementación de borrado (puede ir vacío o con lógica básica por ahora)
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // Implementación de borrado (puede ir vacío o con lógica básica por ahora)
    }
}