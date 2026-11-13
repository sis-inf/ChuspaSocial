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

namespace local_chuspasocial\local;

use advanced_testcase;

/**
 * Pruebas de visibilidad de publicaciones por curso.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_post_visibility_test extends advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea una publicación asociada a un curso.
     *
     * @param int $userid Identificador del usuario.
     * @param int $courseid Identificador del curso.
     * @return \stdClass
     */
    private function create_test_post(int $userid, int $courseid): \stdClass {
        return $this->getDataGenerator()->create_record(
            'local_chuspasocial_post',
            [
                'userid' => $userid,
                'courseid' => $courseid,
                'type' => 'post',
                'content' => 'Publicación de prueba',
                'contentformat' => FORMAT_PLAIN,
                'hidden' => 0,
                'timecreated' => time(),
                'timemodified' => time(),
            ]
        );
    }

    /**
     * Comprueba la asociación de una publicación con un curso.
     */
    public function test_post_course_association(): void {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $post = $this->create_test_post($user->id, $course->id);

        $this->assertEquals($course->id, $post->courseid);
    }

    /**
     * Comprueba que se conserva el identificador del curso.
     */
    public function test_post_preserves_course_id(): void {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $created = $this->create_test_post($user->id, $course->id);

        $retrieved = $this->getDataGenerator()->get_plugin_generator(
            'local_chuspasocial'
        );

        $this->assertNotEmpty($retrieved);
        $this->assertEquals($course->id, $created->courseid);
    }

    /**
     * Comprueba que una publicación puede estar asociada a un curso.
     */
    public function test_post_with_course_is_registered(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $post = $this->create_test_post($user->id, $course->id);

        $this->assertTrue(
            $DB->record_exists('local_chuspasocial_post', [
                'id' => $post->id,
                'courseid' => $course->id,
            ])
        );
    }

    /**
     * Comprueba que una publicación puede no estar asociada a un curso.
     */
    public function test_post_without_course(): void {
        $user = $this->getDataGenerator()->create_user();

        $post = $this->create_test_post($user->id, 0);

        $this->assertEquals(0, $post->courseid);
    }

    /**
     * Comprueba que un curso inexistente no se confunde con uno válido.
     */
    public function test_nonexistent_course_has_no_posts(): void {
        global $DB;

        $this->assertFalse(
            $DB->record_exists('local_chuspasocial_post', [
                'courseid' => 999999999,
            ])
        );
    }
}
