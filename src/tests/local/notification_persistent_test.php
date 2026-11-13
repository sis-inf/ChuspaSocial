<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
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
 * Pruebas unitarias para la persistencia de notificaciones.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\persistent\notification
 */
class notification_persistent_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea una notificación de prueba.
     *
     * @param int $userid Identificador del destinatario.
     * @param string $message Mensaje de la notificación.
     * @return notification
     */
    private function create_test_notification(
        int $userid,
        string $message = 'Notificación de prueba'
    ): notification {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->message = $message;
        $record->timecreated = time();

        $notification = new notification(0, $record);
        $notification->create();

        return $notification;
    }

    /**
     * Comprueba la creación de una notificación.
     */
    public function test_create_notification(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $notification = $this->create_test_notification($user->id);

        $this->assertGreaterThan(0, $notification->get('id'));
        $this->assertTrue(
            $DB->record_exists(notification::TABLE, [
                'id' => $notification->get('id'),
            ])
        );
    }

    /**
     * Comprueba la asociación con el destinatario.
     */
    public function test_notification_recipient_association(): void {
        $user = $this->getDataGenerator()->create_user();

        $notification = $this->create_test_notification($user->id);

        $this->assertEquals($user->id, $notification->get('userid'));
    }

    /**
     * Comprueba la recuperación de una notificación almacenada.
     */
    public function test_retrieve_existing_notification(): void {
        $user = $this->getDataGenerator()->create_user();

        $created = $this->create_test_notification($user->id);
        $retrieved = new notification($created->get('id'));

        $this->assertEquals(
            $created->get('id'),
            $retrieved->get('id')
        );
    }

    /**
     * Comprueba que la información registrada se conserva.
     */
    public function test_notification_information_is_preserved(): void {
        $user = $this->getDataGenerator()->create_user();
        $message = 'Mensaje que debe conservarse';

        $created = $this->create_test_notification($user->id, $message);
        $retrieved = new notification($created->get('id'));

        $this->assertSame($message, $retrieved->get('message'));
    }

    /**
     * Comprueba la consulta de una notificación inexistente.
     */
    public function test_retrieve_nonexistent_notification(): void {
        $this->expectException(\dml_missing_record_exception::class);

        new notification(999999999);
    }
}
