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

/**
 * Unit tests for filtering the feed by tag.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_chuspasocial\local;

use advanced_testcase;

/**
 * Pruebas unitarias del filtrado de publicaciones por etiqueta.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\tag_service::get_feed_by_tag
 */
class feed_tag_filter_test extends advanced_testcase {

    /**
     * Configuracion previa a cada prueba.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea una publicacion de prueba.
     *
     * @param int $userid Autor de la publicacion.
     * @param int $courseid Curso de la publicacion.
     * @param string $content Contenido de la publicacion.
     * @return int ID de la publicacion creada.
     */
    private function create_post(int $userid, int $courseid, string $content = 'Contenido'): int {
        global $DB;

        return (int) $DB->insert_record('local_chuspasocial_post', (object) [
            'userid' => $userid,
            'courseid' => $courseid,
            'type' => 'post',
            'content' => $content,
            'contentformat' => FORMAT_HTML,
            'hidden' => 0,
            'timecreated' => time(),
            'timemodified' => time(),
        ]);
    }

    /**
     * Asigna etiquetas a una publicacion.
     *
     * @param int $postid ID de la publicacion.
     * @param int $courseid ID del curso de la publicacion.
     * @param array $tags Etiquetas que se asignaran.
     */
    private function set_tags(int $postid, int $courseid, array $tags): void {
        $context = $courseid > 0
            ? \context_course::instance($courseid)
            : \context_system::instance();

        \core_tag_tag::set_item_tags(
            'local_chuspasocial',
            'local_chuspasocial_post',
            $postid,
            $context,
            $tags
        );
    }

    /**
     * Devuelve los identificadores de un listado de publicaciones.
     *
     * @param array $posts Publicaciones devueltas por el servicio.
     * @return array Identificadores de las publicaciones.
     */
    private function ids(array $posts): array {
        return array_map(static function ($post) {
            return (int) $post->id;
        }, $posts);
    }

    /**
     * Recupera todas las publicaciones que llevan la etiqueta solicitada.
     */
    public function test_recupera_todas_las_publicaciones_de_la_etiqueta(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        $primera = $this->create_post($user->id, $course->id, 'Primera');
        $this->set_tags($primera, $course->id, ['algebra']);

        $segunda = $this->create_post($user->id, $course->id, 'Segunda');
        $this->set_tags($segunda, $course->id, ['algebra']);

        $this->setUser($user);

        $resultado = tag_service::get_feed_by_tag('algebra', 0, 10);

        $this->assertCount(2, $resultado);
        $this->assertEqualsCanonicalizing([$primera, $segunda], $this->ids($resultado));
    }

    /**
     * Excluye las publicaciones que no llevan la etiqueta solicitada.
     */
    public function test_excluye_las_publicaciones_que_no_coinciden(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        $conetiqueta = $this->create_post($user->id, $course->id, 'Con etiqueta');
        $this->set_tags($conetiqueta, $course->id, ['algebra']);

        $otraetiqueta = $this->create_post($user->id, $course->id, 'Otra etiqueta');
        $this->set_tags($otraetiqueta, $course->id, ['calculo']);

        $sinetiqueta = $this->create_post($user->id, $course->id, 'Sin etiqueta');

        $this->setUser($user);

        $resultado = $this->ids(tag_service::get_feed_by_tag('algebra', 0, 10));

        $this->assertContains($conetiqueta, $resultado);
        $this->assertNotContains($otraetiqueta, $resultado);
        $this->assertNotContains($sinetiqueta, $resultado);
    }

    /**
     * Una etiqueta que no existe devuelve un listado vacio.
     */
    public function test_etiqueta_inexistente_devuelve_listado_vacio(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        $postid = $this->create_post($user->id, $course->id);
        $this->set_tags($postid, $course->id, ['algebra']);

        $this->setUser($user);

        $resultado = tag_service::get_feed_by_tag('etiqueta_que_no_existe', 0, 10);

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * Una etiqueta sin publicaciones asociadas devuelve un listado vacio.
     */
    public function test_etiqueta_sin_publicaciones_devuelve_listado_vacio(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        $this->setUser($user);

        $resultado = tag_service::get_feed_by_tag('algebra', 0, 10);

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * Todas las publicaciones devueltas llevan la etiqueta solicitada.
     */
    public function test_los_resultados_corresponden_al_filtro_solicitado(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        $esperados = [];

        $esperados[] = $primera = $this->create_post($user->id, $course->id, 'Primera');
        $this->set_tags($primera, $course->id, ['algebra']);

        $esperados[] = $segunda = $this->create_post($user->id, $course->id, 'Segunda');
        $this->set_tags($segunda, $course->id, ['algebra', 'calculo']);

        $tercera = $this->create_post($user->id, $course->id, 'Tercera');
        $this->set_tags($tercera, $course->id, ['calculo']);

        $this->setUser($user);

        $resultado = $this->ids(tag_service::get_feed_by_tag('algebra', 0, 10));

        $this->assertEqualsCanonicalizing($esperados, $resultado);
    }

    /**
     * Una publicacion con varias etiquetas aparece al filtrar por cualquiera de ellas.
     */
    public function test_publicacion_con_varias_etiquetas_aparece_en_cada_filtro(): void {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'student');

        $postid = $this->create_post($user->id, $course->id, 'Varias etiquetas');
        $this->set_tags($postid, $course->id, ['algebra', 'calculo']);

        $this->setUser($user);

        $this->assertEquals([$postid], $this->ids(tag_service::get_feed_by_tag('algebra', 0, 10)));
        $this->assertEquals([$postid], $this->ids(tag_service::get_feed_by_tag('calculo', 0, 10)));
    }
}
