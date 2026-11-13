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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

namespace local_chuspasocial\local\persistent;

use advanced_testcase;

/**
 * Pruebas unitarias para la clase persistente comment.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\persistent\comment
 */
class comment_persistent_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea un comentario de prueba.
     *
     * @param int $userid Identificador del usuario.
     * @param int $postid Identificador de la publicación.
     * @param string $content Contenido del comentario.
     * @return comment
     */
    private function create_test_comment(int $userid, int $postid,
            string $content = 'Comentario de prueba'): comment {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->postid = $postid;
        $record->content = $content;
        $record->contentformat = FORMAT_PLAIN;
        $record->timecreated = time();
        $record->timemodified = time();

        $comment = new comment(0, $record);
        $comment->create();

        return $comment;
    }

    /**
     * Comprueba el registro de un comentario.
     */
    public function test_create_comment(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $post = $this->getDataGenerator()->create_record(
            'local_chuspasocial_post',
            [
                'userid' => $user->id,
                'courseid' => 0,
                'type' => 'post',
                'content' => 'Publicación de prueba',
                'contentformat' => FORMAT_PLAIN,
                'hidden' => 0,
                'timecreated' => time(),
                'timemodified' => time(),
            ]
        );

        $comment = $this->create_test_comment($user->id, $post->id);

        $this->assertGreaterThan(0, $comment->get('id'));
        $this->assertTrue(
            $DB->record_exists('local_chuspasocial_comment', [
                'id' => $comment->get('id'),
            ])
        );
    }

    /**
     * Comprueba la recuperación de un comentario almacenado.
     */
    public function test_retrieve_existing_comment(): void {
        $user = $this->getDataGenerator()->create_user();
        $post = $this->getDataGenerator()->create_record(
            'local_chuspasocial_post',
            [
                'userid' => $user->id,
                'courseid' => 0,
                'type' => 'post',
                'content' => 'Publicación de prueba',
                'contentformat' => FORMAT_PLAIN,
                'hidden' => 0,
                'timecreated' => time(),
                'timemodified' => time(),
            ]
        );

        $created = $this->create_test_comment($user->id, $post->id);
        $retrieved = new comment($created->get('id'));

        $this->assertEquals($created->get('id'), $retrieved->get('id'));
    }

    /**
     * Comprueba la relación entre comentario y publicación.
     */
    public function test_comment_post_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $post = $this->getDataGenerator()->create_record(
            'local_chuspasocial_post',
            [
                'userid' => $user->id,
                'courseid' => 0,
                'type' => 'post',
                'content' => 'Publicación de prueba',
                'contentformat' => FORMAT_PLAIN,
                'hidden' => 0,
                'timecreated' => time(),
                'timemodified' => time(),
            ]
        );

        $created = $this->create_test_comment($user->id, $post->id);
        $retrieved = new comment($created->get('id'));

        $this->assertEquals($post->id, $retrieved->get('postid'));
    }

    /**
     * Comprueba la identificación del autor del comentario.
     */
    public function test_comment_author_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $post = $this->getDataGenerator()->create_record(
            'local_chuspasocial_post',
            [
                'userid' => $user->id,
                'courseid' => 0,
                'type' => 'post',
                'content' => 'Publicación de prueba',
                'contentformat' => FORMAT_PLAIN,
                'hidden' => 0,
                'timecreated' => time(),
                'timemodified' => time(),
            ]
        );

        $created = $this->create_test_comment($user->id, $post->id);
        $retrieved = new comment($created->get('id'));

        $this->assertEquals($user->id, $retrieved->get('userid'));
    }

    /**
     * Comprueba el comportamiento al consultar un comentario inexistente.
     */
    public function test_retrieve_nonexistent_comment(): void {
        $this->expectException(\dml_missing_record_exception::class);

        new comment(999999999);
    }
}
