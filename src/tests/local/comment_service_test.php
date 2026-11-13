<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_chuspasocial\local;

use advanced_testcase;

/**
 * Pruebas unitarias para el metodo add_comment.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\comment_service::add_comment
 */
class comment_service_test extends advanced_testcase {
    /**
     * Configuracion previa a cada test.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Crea un post de prueba directamente en la base de datos.
     *
     * @param int $userid Autor del post.
     * @param int $hidden 1 si el post esta oculto.
     * @return int Id del post creado.
     */
    private function crear_post(int $userid, int $hidden = 0): int {
        global $DB;

        $post = new \stdClass();
        $post->userid = $userid;
        $post->courseid = 0;
        $post->type = 'post';
        $post->content = 'Post de prueba';
        $post->contentformat = FORMAT_PLAIN;
        $post->hidden = $hidden;
        $post->timecreated = time();
        $post->timemodified = time();

        return $DB->insert_record('local_chuspasocial_post', $post);
    }

    /**
     * Test: Un usuario puede comentar un post y el comentario se guarda.
     */
    public function test_add_comment_valido(): void {
        global $DB;

        $generator = $this->getDataGenerator();
        $autor = $generator->create_user();
        $comentarista = $generator->create_user();
        $postid = $this->crear_post($autor->id);

        $sink = $this->redirectMessages();

        $this->setUser($comentarista);
        $commentid = comment_service::add_comment($postid, 'Buen aporte')->get('id');

        $this->assertGreaterThan(0, $commentid);

        $comment = $DB->get_record('local_chuspasocial_comment', ['id' => $commentid], '*', MUST_EXIST);
        $this->assertEquals($postid, $comment->postid);
        $this->assertEquals($comentarista->id, $comment->userid);
        $this->assertSame('Buen aporte', $comment->content);

        $sink->close();
    }

    /**
     * Test: Comentar un post que no existe lanza una excepcion.
     */
    public function test_add_comment_no_visible(): void {
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(\moodle_exception::class);
        comment_service::add_comment(999999, $user->id, 'Comentario');
    }

    /**
     * Test: Comentar un post ajeno envia una notificacion al autor del post.
     */
    public function test_add_comment_sin_capacidad(): void {
        $generator = $this->getDataGenerator();
        $autor = $generator->create_user();
        $comentarista = $generator->create_user();
        $postid = $this->crear_post($autor->id);

        $sink = $this->redirectMessages();

        comment_service::add_comment($postid, $comentarista->id, 'Hola');

        $mensajes = $sink->get_messages();
        $this->assertCount(1, $mensajes);
        $this->assertEquals($autor->id, $mensajes[0]->useridto);

        $sink->close();
    }

    /**
     * Test: Un comentario vacio se rechaza y no se crea ningun registro.
     */
    public function test_add_comment_vacio(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $postid = $this->crear_post($user->id);
        $this->setUser($user);

        try {
            comment_service::add_comment($postid, '');
            $this->fail('Se esperaba una excepcion por comentario vacio.');
        } catch (\invalid_parameter_exception $e) {
            $this->assertSame(0, $DB->count_records('local_chuspasocial_comment'));
        }
    }

    /**
     * Test: Un comentario formado solo por espacios en blanco se rechaza y no se crea ningun registro.
     */
    public function test_add_comment_en_blanco(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $postid = $this->crear_post($user->id);
        $this->setUser($user);

        try {
            comment_service::add_comment($postid, "   \t\n  ");
            $this->fail('Se esperaba una excepcion por comentario en blanco.');
        } catch (\invalid_parameter_exception $e) {
            $this->assertSame(0, $DB->count_records('local_chuspasocial_comment'));
        }
    }

    /**
     * Test: get_comments devuelve los comentarios del mas antiguo al mas reciente.
     *
     * @covers \local_chuspasocial\local\comment_service::get_comments
     */
    public function test_get_comments_orden_mas_antiguo_primero(): void {
        global $DB;

        $generator = $this->getDataGenerator()->get_plugin_generator('local_chuspasocial');
        $post = $generator->create_post();
        $postid = $post->get('id');

        $reciente = $generator->create_comment(['postid' => $postid, 'content' => 'Reciente']);
        $antiguo = $generator->create_comment(['postid' => $postid, 'content' => 'Antiguo']);
        $DB->set_field('local_chuspasocial_comment', 'timecreated', 2000, ['id' => $reciente->get('id')]);
        $DB->set_field('local_chuspasocial_comment', 'timecreated', 1000, ['id' => $antiguo->get('id')]);

        $comments = comment_service::get_comments($postid);

        $this->assertCount(2, $comments);
        $this->assertEquals($antiguo->get('id'), $comments[0]->id);
        $this->assertEquals($reciente->get('id'), $comments[1]->id);
    }

    /**
     * Test: get_comments de un post no visible no lanza excepcion y devuelve un array vacio.
     *
     * @covers \local_chuspasocial\local\comment_service::get_comments
     */
    public function test_get_comments_post_no_visible_devuelve_vacio(): void {
        $generator = $this->getDataGenerator()->get_plugin_generator('local_chuspasocial');
        $post = $generator->create_post(['hidden' => 1]);
        $generator->create_comment(['postid' => $post->get('id')]);

        $this->assertSame([], comment_service::get_comments($post->get('id')));
    }
}
