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
 * Pruebas de resolve_and_hide para publicaciones, comentarios y anuncios.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\report_service::resolve_and_hide
 */
final class report_service_moderation_test extends advanced_testcase {
    /**
     * Crea un reporte abierto directamente en la base de datos.
     *
     * @param string $itemtype Tipo de contenido reportado.
     * @param int $itemid Id del contenido reportado.
     * @return int Id del reporte creado.
     */
    private function crear_reporte(string $itemtype, int $itemid): int {
        global $DB;

        $reporter = $this->getDataGenerator()->create_user();

        return $DB->insert_record('local_chuspasocial_report', (object) [
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'reporterid' => $reporter->id,
            'reason' => 'Contenido inapropiado',
            'status' => 'open',
            'timecreated' => time(),
        ]);
    }

    /**
     * Crea un anuncio de libro disponible directamente en la base de datos.
     *
     * @param string $title Titulo del libro.
     * @return int Id del anuncio creado.
     */
    private function crear_anuncio(string $title): int {
        global $DB;

        $seller = $this->getDataGenerator()->create_user();

        return $DB->insert_record('local_chuspasocial_listing', (object) [
            'userid' => $seller->id,
            'title' => $title,
            'author' => 'Autor de prueba',
            'description' => '',
            'descriptionformat' => FORMAT_HTML,
            'price' => 10,
            'status' => 'available',
            'timecreated' => time(),
            'timemodified' => time(),
        ]);
    }

    /**
     * Comprueba que el reporte quedo resuelto por el usuario actual.
     *
     * @param int $reportid Id del reporte.
     * @param int $moderatorid Id del moderador que lo resolvio.
     */
    private function assert_reporte_resuelto(int $reportid, int $moderatorid): void {
        global $DB;

        $report = $DB->get_record('local_chuspasocial_report', ['id' => $reportid], '*', MUST_EXIST);
        $this->assertSame('resolved', $report->status);
        $this->assertEquals($moderatorid, $report->resolvedby);
        $this->assertGreaterThan(0, (int) $report->timeresolved);
    }

    /**
     * Resolver un reporte de publicacion la oculta, como hasta ahora.
     */
    public function test_resolver_reporte_de_publicacion(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('local_chuspasocial');
        $post = $generator->create_post();
        $reportid = $this->crear_reporte('post', $post->get('id'));

        report_service::resolve_and_hide($reportid);

        $this->assertEquals(1, $DB->get_field('local_chuspasocial_post', 'hidden', ['id' => $post->get('id')]));
        $this->assert_reporte_resuelto($reportid, (int) $USER->id);
    }

    /**
     * Resolver un reporte de comentario lo oculta y deja de aparecer en get_comments.
     */
    public function test_resolver_reporte_de_comentario(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('local_chuspasocial');
        $post = $generator->create_post();
        $reportado = $generator->create_comment(['postid' => $post->get('id'), 'content' => 'Reportado']);
        $visible = $generator->create_comment(['postid' => $post->get('id'), 'content' => 'Visible']);
        $reportid = $this->crear_reporte('comment', $reportado->get('id'));

        report_service::resolve_and_hide($reportid);

        $this->assertEquals(1, $DB->get_field('local_chuspasocial_comment', 'hidden', ['id' => $reportado->get('id')]));
        $comments = comment_service::get_comments($post->get('id'));
        $this->assertCount(1, $comments);
        $this->assertEquals($visible->get('id'), $comments[0]->id);
        $this->assert_reporte_resuelto($reportid, (int) $USER->id);
    }

    /**
     * Resolver un reporte de anuncio lo oculta y deja de aparecer en la busqueda.
     */
    public function test_resolver_reporte_de_anuncio(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $listingid = $this->crear_anuncio('Calculo moderado');
        $this->assertCount(1, listing_service::search('Calculo moderado', 0, 10));
        $reportid = $this->crear_reporte('listing', $listingid);

        report_service::resolve_and_hide($reportid);

        $this->assertEquals(1, $DB->get_field('local_chuspasocial_listing', 'hidden', ['id' => $listingid]));
        $this->assertSame([], listing_service::search('Calculo moderado', 0, 10));
        $this->assert_reporte_resuelto($reportid, (int) $USER->id);
    }

    /**
     * Un usuario sin la capacidad de moderar no puede resolver reportes.
     */
    public function test_resolver_sin_capacidad_de_moderar(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('local_chuspasocial');
        $comment = $generator->create_comment();
        $reportid = $this->crear_reporte('comment', $comment->get('id'));

        $this->setUser($this->getDataGenerator()->create_user());

        try {
            report_service::resolve_and_hide($reportid);
            $this->fail('Se esperaba required_capability_exception.');
        } catch (\required_capability_exception $e) {
            $this->assertEquals(0, $DB->get_field('local_chuspasocial_comment', 'hidden', ['id' => $comment->get('id')]));
            $this->assertSame('open', $DB->get_field('local_chuspasocial_report', 'status', ['id' => $reportid]));
        }
    }
}
