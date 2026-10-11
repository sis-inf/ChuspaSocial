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
