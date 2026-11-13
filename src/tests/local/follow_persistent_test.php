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
 * Pruebas unitarias para la persistencia de relaciones de seguimiento.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\persistent\follow
 */
class follow_persistent_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea una relación de seguimiento de prueba.
     *
     * @param int $userid Usuario que sigue.
     * @param int $followeduserid Usuario seguido.
     * @return follow
     */
    private function create_test_follow(int $userid, int $followeduserid): follow {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->followeduserid = $followeduserid;

        $follow = new follow(0, $record);
        $follow->create();

        return $follow;
    }

    /**
     * Comprueba el registro de una relación de seguimiento.
     */
    public function test_create_follow(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $followeduser = $this->getDataGenerator()->create_user();

        $follow = $this->create_test_follow($user->id, $followeduser->id);

        $this->assertGreaterThan(0, $follow->get('id'));
        $this->assertTrue(
            $DB->record_exists(follow::TABLE, [
                'id' => $follow->get('id'),
            ])
        );
    }

    /**
     * Comprueba la asociación con el usuario que sigue.
     */
    public function test_follow_user_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $followeduser = $this->getDataGenerator()->create_user();

        $follow = $this->create_test_follow($user->id, $followeduser->id);

        $this->assertEquals($user->id, $follow->get('userid'));
    }

    /**
     * Comprueba la asociación con el usuario seguido.
     */
    public function test_followed_user_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $followeduser = $this->getDataGenerator()->create_user();

        $follow = $this->create_test_follow($user->id, $followeduser->id);

        $this->assertEquals($followeduser->id, $follow->get('followeduserid'));
    }

    /**
     * Comprueba la recuperación de una relación almacenada.
     */
    public function test_retrieve_existing_follow(): void {
        $user = $this->getDataGenerator()->create_user();
        $followeduser = $this->getDataGenerator()->create_user();

        $created = $this->create_test_follow($user->id, $followeduser->id);
        $retrieved = new follow($created->get('id'));

        $this->assertEquals($created->get('id'), $retrieved->get('id'));
    }

    /**
     * Comprueba la consulta de una relación inexistente.
     */
    public function test_retrieve_nonexistent_follow(): void {
        $this->expectException(\dml_missing_record_exception::class);

        new follow(999999999);
    }
}
