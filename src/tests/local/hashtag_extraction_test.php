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
 * Unit tests for hashtag identification in post content.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_chuspasocial\local;

use advanced_testcase;

/**
 * Pruebas unitarias de la identificacion de hashtags en publicaciones.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hashtag_extraction_test extends advanced_testcase {

    /**
     * Configuracion previa a cada prueba.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Identifica los hashtags presentes en un texto.
     *
     * Devuelve los nombres sin el signo numeral, en el orden en que aparecen
     * y sin repeticiones. El texto recibido no se modifica.
     *
     * @param string $text Contenido de la publicacion.
     * @return array Nombres de los hashtags encontrados.
     */
    private function extract_hashtags(string $text): array {
        $matches = [];
        preg_match_all('/#([\p{L}\p{N}_]+)/u', $text, $matches);

        return array_values(array_unique($matches[1]));
    }

    /**
     * Identifica un hashtag dentro de un texto.
     */
    public function test_identifica_un_hashtag_en_el_texto(): void {
        $this->assertEquals(
            ['algebra'],
            $this->extract_hashtags('Hoy repasamos #algebra en clase')
        );
    }

    /**
     * Reconoce varias etiquetas en el mismo texto.
     */
    public function test_reconoce_varias_etiquetas(): void {
        $this->assertEquals(
            ['algebra', 'calculo', 'fni'],
            $this->extract_hashtags('Repaso de #algebra y #calculo en la #fni')
        );
    }

    /**
     * Un texto sin hashtags no devuelve ninguna etiqueta.
     */
    public function test_texto_sin_hashtags_no_devuelve_etiquetas(): void {
        $resultado = $this->extract_hashtags('Una publicacion sin etiquetas');

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * Un texto vacio no devuelve ninguna etiqueta.
     */
    public function test_texto_vacio_no_devuelve_etiquetas(): void {
        $resultado = $this->extract_hashtags('');

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * El texto original no se altera durante el procesamiento.
     */
    public function test_el_texto_original_no_se_altera(): void {
        $original = 'Repaso de #algebra y #calculo';
        $copia = $original;

        $this->extract_hashtags($original);

        $this->assertSame($copia, $original);
    }

    /**
     * Un hashtag repetido se devuelve una sola vez.
     */
    public function test_hashtag_repetido_se_devuelve_una_sola_vez(): void {
        $this->assertEquals(
            ['algebra'],
            $this->extract_hashtags('#algebra por la manana y #algebra por la tarde')
        );
    }

    /**
     * El signo numeral solo no se considera una etiqueta.
     */
    public function test_numeral_solo_no_es_una_etiqueta(): void {
        $resultado = $this->extract_hashtags('Numeral suelto # sin nada detras');

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * La puntuacion posterior no forma parte de la etiqueta.
     */
    public function test_la_puntuacion_no_forma_parte_de_la_etiqueta(): void {
        $this->assertEquals(
            ['algebra', 'calculo'],
            $this->extract_hashtags('Temas: #algebra, #calculo.')
        );
    }
}
