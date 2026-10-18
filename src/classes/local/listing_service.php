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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Service for managing book listings.
 *
 * @package    local_chuspasocial
 * @copyright  2026 SIS-INF
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_chuspasocial\local;

/**
 * Service class for book listings.
 */
class listing_service {

    /**
     * Formats a listing price using the configured currency.
     *
     * @param float $price Price to format.
     * @return string Formatted price or localized free label for zero.
     */
    public static function format_price(float $price): string {
        if ($price == 0.0) {
            return get_string('free', 'local_chuspasocial');
        }

        $currency = get_config('local_chuspasocial', 'currency');

        if (empty($currency)) {
            $currency = 'BOB';
        }

        return $currency . ' ' . format_float($price, 2);
    }
}
