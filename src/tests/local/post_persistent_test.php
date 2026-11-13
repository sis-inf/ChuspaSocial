
<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License
// as published by the Free Software Foundation, either version 3
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

namespace local_chuspasocial\local\persistent;

use advanced_testcase;

/**
 * Pruebas unitarias para la clase persistente post.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\persistent\post
 */
class post_persistent_test extends advanced_testcase {

    /**
     * Configuración previa a cada prueba.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea una publicación de prueba.
     *
     * @param int $userid ID del autor.
     * @param string $content Contenido de la publicación.
     * @return post Publicación creada.
     */
    private function create_test_post(int $userid, string $content = 'Publicación de prueba'): post {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->courseid = 0;
        $record->type = post::TYPE_POST;
        $record->content = $content;
        $record->contentformat = FORMAT_PLAIN;
        $record->hidden = 0;
        $record->timecreated = time();
        $record->timemodified = time();

        $post = new post(0, $record);
        $post->create();

        return $post;
    }

    /**
     * Comprueba que una publicación se registre correctamente.
     */
    public function test_create_post(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();

        $post = $this->create_test_post($user->id);

        $this->assertGreaterThan(0, $post->get('id'));
        $this->assertTrue(
            $DB->record_exists(post::TABLE, ['id' => $post->get('id')])
        );
    }

    /**
     * Comprueba que se pueda recuperar una publicación existente.
     */
    public function test_retrieve_existing_post(): void {
        $user = $this->getDataGenerator()->create_user();

        $created = $this->create_test_post($user->id);

        $retrieved = new post($created->get('id'));

        $this->assertEquals($created->get('id'), $retrieved->get('id'));
    }

    /**
     * Comprueba que el contenido se conserve correctamente.
     */
    public function test_post_content_is_preserved(): void {
        $user = $this->getDataGenerator()->create_user();
        $content = 'Contenido que debe conservarse';

        $created = $this->create_test_post($user->id, $content);

        $retrieved = new post($created->get('id'));

        $this->assertSame($content, $retrieved->get('content'));
    }

    /**
     * Comprueba que recuperar una publicación inexistente lance una excepción.
     */
    public function test_retrieve_nonexistent_post(): void {
        $this->expectException(\dml_missing_record_exception::class);

        new post(999999999);
    }

    /**
     * Comprueba que la publicación conserve la asociación con su autor.
     */
    public function test_post_author_association(): void {
        $user = $this->getDataGenerator()->create_user();

        $created = $this->create_test_post($user->id);

        $retrieved = new post($created->get('id'));

        $this->assertEquals($user->id, $retrieved->get('userid'));
    }
}
