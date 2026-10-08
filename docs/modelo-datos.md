# Modelo de datos

## Tabla `local_chuspasocial_listing` (anuncios de libros)

Almacena los anuncios de libros publicados por los usuarios.

| Campo | Tipo XMLDB | Nulo | Descripción |
|---|---|---|---|
| `id` | INT(10), secuencia | NO | Clave primaria autoincremental del anuncio. |
| `userid` | INT(10) | NO | ID del usuario que publica el anuncio (referencia a `user.id`). |
| `title` | CHAR(255) | NO | Título del libro. |
| `author` | CHAR(255) | NO | Autor del libro. |
| `description` | TEXT | SÍ | Descripción del anuncio. |
| `descriptionformat` | INT(4) | NO | Formato de la descripción (constantes `FORMAT_*` de Moodle; por defecto `FORMAT_HTML` = 1). |
| `price` | NUMBER(10,2) | NO | Precio del libro. |
| `contact` | CHAR(255) | SÍ | Contacto externo del vendedor (teléfono, correo u otro medio). |
| `status` | CHAR(20) | NO | Estado del anuncio. Valores permitidos: `available` (disponible), `sold` (vendido). Por defecto: `available`. |
| `timecreated` | INT(10) | NO | Marca de tiempo Unix de creación. |
| `timemodified` | INT(10) | NO | Marca de tiempo Unix de la última modificación. |

### Valores permitidos de `status`

| Valor | Significado |
|---|---|
| `available` | El libro está disponible para la venta. |
| `sold` | El libro ya fue vendido. |
