<?php
namespace local_chuspasocial\local\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Clase persistente para la entidad follow.
 *
 * @package    local_chuspasocial
 * @copyright  2026 Jairo David Medrano Huanca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class follow extends \core\persistent {

    /** @var string La tabla de la base de datos asociada. */
    const TABLE = 'local_chuspasocial_follow';

    /**
     * Definición de las propiedades de la persistencia alineadas con el modelo.
     * 
     * @return array
     */
    protected static function define_properties() {
        return array(
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'itemtype' => array(
                'type' => PARAM_ALPHA,
            ),
            'itemid' => array(
                'type' => PARAM_INT,
            ),
            'timecreated' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
        );
    }

    /**
     * Validador para el campo itemtype asegurando que esté dentro de las opciones permitidas.
     * 
     * @param string $value El valor a validar.
     * @return true
     * @throws \invalid_persistent_exception Si el valor no es válido.
     */
    protected function validate_itemtype($value) {
        $choices = array('user', 'course');

        if (!in_array($value, $choices)) {
            throw new \invalid_persistent_exception("El valor '$value' para itemtype está fuera de las opciones permitidas.");
        }

        return true;
    }
}
