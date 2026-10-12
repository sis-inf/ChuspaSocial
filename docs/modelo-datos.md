# Modelo de datos de ChuspaSocial

## Introducción

El presente documento describe de manera general el modelo de datos del sistema ChuspaSocial.

Su propósito es organizar y documentar las tablas que formarán parte de la base de datos antes de iniciar su implementación.

Esta documentación permitirá mantener una estructura clara, facilitar el desarrollo del sistema y establecer criterios comunes para la definición de las tablas.

## Tablas del modelo de datos

El modelo de datos contempla un total de siete tablas:

1. `local_chuspasocial_post`
2. `local_chuspasocial_comment`
3. `local_chuspasocial_reaction`
4. `local_chuspasocial_follow`
5. `local_chuspasocial_report`
6. `local_chuspasocial_listing`
7. `local_chuspasocial_lstcourse`

## Regla de nomenclatura de Moodle

Para mantener la compatibilidad con las restricciones de Moodle, los nombres de las tablas de la base de datos deben tener un máximo de 28 caracteres.

Esta regla debe respetarse al definir los nombres de las siete tablas del modelo de datos.

## Tabla local_chuspasocial_comment

La tabla local_chuspasocial_comment almacena los comentarios realizados por los usuarios en las publicaciones de ChuspaSocial.

### Estructura de la tabla

| Campo | Tipo XMLDB | Nulo | Descripción |
|---|---|---|---|
| id | int(10) | No | Identificador único del comentario. |
| postid | int(10) | No | Identificador de la publicación a la que pertenece el comentario. |
| userid | int(10) | No | Identificador del usuario que realiza el comentario. |
| content | text | No | Contenido del comentario realizado por el usuario. |
| contentformat | int(2) | No | Formato utilizado para almacenar el contenido del comentario. |
| timecreated | int(10) | No | Fecha y hora de creación del comentario en formato Unix. |
| timemodified | int(10) | No | Fecha y hora de la última modificación del comentario en formato Unix. |

### Relaciones con otras tablas

La tabla `local_chuspasocial_comment` se relaciona con otras tablas del sistema mediante los siguientes campos:

- **postid:** establece una relación con la tabla `local_chuspasocial_post`, identificando la publicación a la que pertenece cada comentario.

- **userid:** establece una relación con la tabla estándar `user` de Moodle, identificando al usuario que realizó el comentario.

### Descripción de las relaciones

Una publicación registrada en `local_chuspasocial_post` puede tener varios comentarios asociados mediante el campo `postid`.

Asimismo, un usuario registrado en la tabla `user` de Moodle puede realizar varios comentarios, los cuales se relacionan mediante el campo `userid`.

Estas relaciones permiten organizar los comentarios, identificar a sus autores y determinar a qué publicación pertenece cada uno.
## Tabla `local_chuspasocial_report` (reportes de contenido)

Almacena los reportes que los usuarios hacen sobre publicaciones, comentarios y anuncios.

| Campo | Tipo XMLDB | Nulo | Descripción |
|---|---|---|---|
| `id` | INT(10), secuencia | NO | Clave primaria autoincremental del reporte. |
| `itemtype` | CHAR(20) | NO | Tipo de contenido reportado. Valores permitidos: `post`, `comment`, `listing`. |
| `itemid` | INT(10) | NO | ID del contenido reportado (se interpreta según `itemtype`). |
| `reporterid` | INT(10) | NO | ID del usuario que realiza el reporte (referencia a `user.id`). |
| `reason` | TEXT | NO | Motivo del reporte. |
| `status` | CHAR(20) | NO | Estado del reporte. Valores permitidos: `open`, `resolved`, `dismissed`. Por defecto: `open`. |
| `resolvedby` | INT(10) | SÍ | ID del usuario que resolvió el reporte (referencia a `user.id`). Nulo mientras no se resuelva. |
| `timecreated` | INT(10) | NO | Marca de tiempo Unix de creación. |
| `timeresolved` | INT(10) | SÍ | Marca de tiempo Unix de la resolución. Nulo mientras no se resuelva. |

### Valores permitidos de `itemtype`

| Valor | Significado |
|---|---|
| `post` | El reporte es sobre una publicación. |
| `comment` | El reporte es sobre un comentario. |
| `listing` | El reporte es sobre un anuncio de libro. |
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
| `externalcontact` | CHAR(255) | SÍ | Contacto externo del vendedor (teléfono, correo u otro medio). |
| `status` | CHAR(20) | NO | Estado del anuncio. Valores permitidos: `available` (disponible), `sold` (vendido). Por defecto: `available`. |
| `timecreated` | INT(10) | NO | Marca de tiempo Unix de creación. |
| `timemodified` | INT(10) | NO | Marca de tiempo Unix de la última modificación. |

### Valores permitidos de `status`

| Valor | Significado |
|---|---|
| `open` | El reporte está pendiente de revisión. |
| `resolved` | El reporte fue atendido y se tomó una acción. |
| `dismissed` | El reporte fue revisado y descartado sin acción. |
| `available` | El libro está disponible para la venta. |
| `sold` | El libro ya fue vendido. |
