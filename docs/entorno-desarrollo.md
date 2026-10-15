# Entorno de desarrollo

Guía de configuración y solución de problemas del entorno de desarrollo de ChuspaSocial sobre Moodle 4.5 LTS.

## Problemas comunes

### Error `max_input_vars` al iniciar sesión o al guardar formularios

**Síntoma:**
La sesión no se completa correctamente, los formularios no se guardan o se pierden variables. En el registro de errores de PHP aparece el aviso exacto:

```
PHP Warning:  PHP input variables exceeded 1000. Truncating.
```

Cuando `max_input_vars` está por debajo del valor exigido por Moodle, PHP deja de leer las variables de la petición más allá del límite y la solicitud queda truncada.

**Causa:**
Moodle envía y recibe muchas variables de entrada por petición; el valor por defecto de PHP (`max_input_vars = 1000`) no es suficiente. El plugin y Moodle 4.5 LTS exigen `max_input_vars >= 5000`.

**Solución:**
En el fichero `php.ini` (o en la configuración del contenedor de `moodle-docker`) define:

```ini
max_input_vars = 5000
```

Tras el cambio, reinicia el servicio de PHP (o el contenedor) para que la nueva configuración se aplique. Verifica el valor efectivo con:

```bash
php -i | findstr max_input_vars
```

o, en un contexto web:

```bash
php -r "echo ini_get('max_input_vars');"
```

El valor debe mostrar `5000` o superior.
