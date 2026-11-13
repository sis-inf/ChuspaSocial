<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License
// as published by the Free Software Foundation, either version 3
// of the License, or (at your option) any later version.
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
 * Pruebas unitarias para la persistencia de reacciones.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\persistent\reaction
 */
class reaction_persistent_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea una reacción de prueba.
     *
     * @param int $userid Identificador del usuario.
     * @param int $postid Identificador de la publicación.
     * @return reaction
     */
    private function create_test_reaction(int $userid, int $postid): reaction {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->postid = $postid;

        $reaction = new reaction(0, $record);
        $reaction->create();

        return $reaction;
    }

    /**
     * Crea una publicación para las pruebas.
     *
     * @param int $userid Identificador del usuario.
     * @return \stdClass
     */
    private function create_test_post(int $userid): \stdClass {
        return $this->getDataGenerator()->create_record(
            'local_chuspasocial_post',
            [
                'userid' => $userid,
                'courseid' => 0,
                'type' => 'post',
                'content' => 'Publicación de prueba',
                'contentformat' => FORMAT_PLAIN,
                'hidden' => 0,
                'timecreated' => time(),
                'timemodified' => time(),
            ]
        );
    }

    public function test_create_reaction(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $post = $this->create_test_post($user->id);

        $reaction = $this->create_test_reaction($user->id, $post->id);

        $this->assertGreaterThan(0, $reaction->get('id'));
        $this->assertTrue(
            $DB->record_exists(reaction::TABLE, [
                'id' => $reaction->get('id'),
            ])
        );
    }

    public function test_reaction_user_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $post = $this->create_test_post($user->id);

        $reaction = $this->create_test_reaction($user->id, $post->id);

        $this->assertEquals($user->id, $reaction->get('userid'));
    }

    public function test_reaction_post_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $post = $this->create_test_post($user->id);

        $reaction = $this->create_test_reaction($user->id, $post->id);

        $this->assertEquals($post->id, $reaction->get('postid'));
    }

    public function test_retrieve_existing_reaction(): void {
        $user = $this->getDataGenerator()->create_user();
        $post = $this->create_test_post($user->id);

        $created = $this->create_test_reaction($user->id, $post->id);
        $retrieved = new reaction($created->get('id'));

        $this->assertEquals($created->get('id'), $retrieved->get('id'));
    }

    public function test_retrieve_nonexistent_reaction(): void {
        $this->expectException(\dml_missing_record_exception::class);

        new reaction(999999999);
    }
}
