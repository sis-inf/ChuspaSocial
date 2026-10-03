<?php
namespace local_tuplugin\privacy; // CAMBIA ESTO por el namespace de tu plugin

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\plugin\provider as plugin_provider;
use core_privacy\local\request\writer;

class provider implements plugin_provider {
    
    // 1. Declarar qué datos guarda el plugin (RNF-006)
    public static function get_metadata(collection $collection) : collection {
        // Ejemplo: $collection->add_database_table('tu_tabla_posts', [...], 'privacy:metadata:posts');
        // Debes declarar: publicaciones, comentarios, reacciones, seguimientos y anuncios.
        return $collection;
    }

    // 2. Obtener los contextos donde el usuario tiene datos
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();
        // Aquí debes consultar tus tablas y agregar los contextos (ej. \context_system::instance())
        return $contextlist;
    }

    // 3. Exportar los datos (El corazón de este issue)
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            // 1. Exportar Publicaciones
            $posts = $DB->get_records('tu_tabla_posts', ['userid' => $userid]);
            if (!empty($posts)) {
                writer::with_context($context)->export_data(['Publicaciones'], (object)['posts' => $posts]);
            }

            // 2. Exportar Comentarios
            $comments = $DB->get_records('tu_tabla_comments', ['userid' => $userid]);
            if (!empty($comments)) {
                writer::with_context($context)->export_data(['Comentarios'], (object)['comments' => $comments]);
            }

            // 3. Exportar Reacciones
            $reactions = $DB->get_records('tu_tabla_reactions', ['userid' => $userid]);
            if (!empty($reactions)) {
                writer::with_context($context)->export_data(['Reacciones'], (object)['reactions' => $reactions]);
            }

            // 4. Exportar Seguimientos
            $follows = $DB->get_records('tu_tabla_follows', ['userid' => $userid]);
            if (!empty($follows)) {
                writer::with_context($context)->export_data(['Seguimientos'], (object)['follows' => $follows]);
            }

            // 5. Exportar Anuncios
            $announcements = $DB->get_records('tu_tabla_announcements', ['userid' => $userid]);
            if (!empty($announcements)) {
                writer::with_context($context)->export_data(['Anuncios'], (object)['announcements' => $announcements]);
            }
        }
    }

    // 4. Métodos obligatorios de la interfaz (pueden ir vacíos si no aplican en este issue)
    public static function delete_data_for_all_users_in_context(\context $context) {}
    public static function delete_data_for_user(approved_contextlist $contextlist) {}
}