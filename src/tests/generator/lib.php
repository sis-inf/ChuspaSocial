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

/**
 * Generador de datos de prueba para local_chuspasocial.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_chuspasocial\local\persistent\listing;
use local_chuspasocial\local\persistent\listing_course;

/**
 * Generador de datos de prueba para local_chuspasocial.
 */
class local_chuspasocial_generator extends component_generator_base {

    /** @var int Contador para generar valores unicos. */
    protected $listingcoursecount = 0;

    /**
     * Crea un vinculo entre un anuncio y una materia.
     *
     * Completa valores por defecto cuando no se indican y devuelve
     * la instancia persistente creada.
     *
     * @param array $record Valores a sobrescribir (opcional).
     * @return listing_course Instancia persistente creada.
     */
    public function create_listing_course(array $record = []): listing_course {
        $this->listingcoursecount++;

        if (empty($record['courseid'])) {
            $course = $this->datagenerator->create_course();
            $record['courseid'] = $course->id;
        }

        if (empty($record['listingid'])) {
            $user = $this->datagenerator->create_user();
            $listing = new listing(0, (object) [
                'userid' => $user->id,
                'title' => 'Libro de prueba ' . $this->listingcoursecount,
                'author' => 'Autor de prueba ' . $this->listingcoursecount,
            ]);
            $listing->create();
            $record['listingid'] = $listing->get('id');
        }

        $persistent = new listing_course(0, (object) $record);
        $persistent->create();

        return $persistent;
    }
}