<?php

namespace local_chuspasocial\local;

defined('MOODLE_INTERNAL') || die();

use local_chuspasocial\local\persistent\post;
use moodle_exception;

/**
 * Servicio para la gestion de publicaciones (posts).
 * 
 * @package    local_chuspasocial 
 */
class post_service {
    
    /**
     * Actualiza el contenido y formato de un post existente.
     * 
     * @param int $postid ID de la pubkicacion a actualizar.
     * @param string $content Nuevo contenido.
     * @param int $format Formato del contenido. 
     * @throws moodle_exception Si el usuario actual no es el autor del post.
     */
    public static function update_post(int $postid, string $content, int $format): void {
        global $USER;

        // Instanciar la publicacion existente
        $post = new post($postid);

        // Criterio de Aceptacion: Valida que el usuario actual sea el autor
        if ((int) $post->get('userid') !== (int) $USER->id) {
            throw new moodle_exception('cannotupdatepost', 'local_chuspasocial', '', null, 'Solo el autor puede editar esta publicación.');
        }

        // Actualizar datos
        $post->set('content', $content);
        $post->set('format', $format);
        $post->set('timemodified', time());
        $post->set('usermodified', $USER->id);

        // Guardar cambios en la base de datos
        $post->update();
    }
}
