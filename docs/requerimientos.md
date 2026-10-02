# Requerimientos del Proyecto

## Requerimientos Funcionales

### Muro

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-006 | Eliminar una publicación propia | Alta | Pendiente |
| RF-008 | Cargar más publicaciones | Media | Pendiente |

### Módulo Tags

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-015 | Etiquetar una publicación | Alta | Pendiente |

### Módulo Tags

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-016 | Filtrar el muro por tag | Alta | Pendiente |

### Módulo Tags

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-017 | Seguir un tag | Media | Pendiente |

#### Criterios de aceptación

### RF-006

**Criterio 1**

- **Dado** un usuario autenticado que tiene una publicación propia en el muro,
- **Cuando** selecciona la opción de eliminar su publicación y confirma la acción,
- **Entonces** el sistema elimina la publicación seleccionada y deja de mostrarla en el muro.

  **Criterio 2**

- **Dado** un usuario que intenta eliminar una publicación creada por otro usuario,
- **Cuando** confirma la acción de eliminación,
- **Entonces** el sistema rechaza la operación y mantiene la publicación sin cambios.

### RF-008 - Cargar más publicaciones

#### Criterio 1
* **Dado** que el usuario se encuentra en el módulo Muro y existen más publicaciones disponibles en la base de datos,
* **Cuando** visualice el final de las publicaciones actuales y haga clic en el botón "Cargar más",
* **Entonces** el sistema deberá cargar y mostrar las siguientes publicaciones en la pantalla sin recargar la página.

#### Criterio 2
* **Dado** que el usuario está en el módulo Muro,
* **Cuando** ya se hayan cargado todas las publicaciones existentes en la base de datos,
* **Entonces** el botón "Cargar más" deberá quedar oculto o deshabilitado.
### Comentarios

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-012 | Ver los comentarios de una publicación | Alta | Pendiente |

#### Criterios de aceptación

### RF-012

**Criterio 1**
- **Dado** un usuario que visualiza una publicación con comentarios,
- **Cuando** consulta la publicación,
- **Entonces** el sistema muestra sus comentarios en orden cronológico debajo de la publicación.

**Criterio 2**
- **Dado** un usuario que visualiza una publicación sin comentarios,
- **Cuando** consulta la publicación,
- **Entonces** el sistema muestra el área de comentarios sin entradas y permite identificar que aún no existen comentarios.
### Reacciones

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-013 | Reaccionar a una publicación | Alta | Pendiente |

#### Criterios de aceptación

### RF-013

**Criterio 1**
- **Dado** un usuario autenticado que visualiza una publicación,
- **Cuando** selecciona la opción «Me gusta»,
- **Entonces** el sistema registra la reacción del usuario en esa publicación.

**Criterio 2**
- **Dado** un usuario que ya reaccionó con «Me gusta» a una publicación,
- **Cuando** vuelve a seleccionar la misma opción,
- **Entonces** el sistema actualiza el estado de su reacción sin crear registros duplicados.

### Seguidores
### Anuncios

### RF-017 - Seguir un tag

#### Criterio 1
* **Dado** que el usuario se encuentra visualizando una etiqueta (tag) específica en el sistema,
* **Cuando** haga clic en el botón de "Seguir",
* **Entonces** el sistema deberá registrar la suscripción y empezar a mostrar las publicaciones con este tag en su feed personalizado.

#### Criterio 2
* **Dado** que el usuario ya sigue una etiqueta específica,
* **Cuando** decida hacer clic en el botón de "Dejar de seguir",
* **Entonces** el sistema deberá remover la etiqueta de sus suscripciones y dejar de priorizar esas publicaciones en su feed.
### RF-016 - Filtrar el muro por tag

#### Criterio 1
* **Dado** que el usuario se encuentra visualizando las publicaciones en el muro,
* **Cuando** haga clic sobre una etiqueta (tag) específica en cualquier publicación,
* **Entonces** el sistema deberá recargar el listado mostrando únicamente las publicaciones que contengan dicha etiqueta.

#### Criterio 2
* **Dado** que el muro se encuentra filtrado por una etiqueta específica,
* **Cuando** el usuario decida limpiar el filtro o hacer clic en la opción de restablecer el muro,
* **Entonces** el sistema deberá mostrar nuevamente todas las publicaciones disponibles sin ningún tipo de filtro.
### RF-015 - Etiquetar una publicación

#### Criterio 1
* **Dado** que el usuario se encuentra creando o editando una publicación,
* **Cuando** empiece a escribir en el campo de etiquetas o use los tags predefinidos de Moodle,
* **Entonces** el sistema deberá asociar de forma correcta dichas etiquetas a la publicación al momento de guardarla.

#### Criterio 2
* **Dado** que una publicación cuenta con etiquetas asociadas,
* **Cuando** se visualice la publicación en el muro,
* **Entonces** las etiquetas deberán ser visibles para todos los usuarios y permitir la navegación o filtrado por esos tags.


| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-001 | | Alta | Pendiente |

| RF-020 | Ver la lista de seguidores y seguidos | Baja | Pendiente |

#### Criterios de aceptación

### RF-020

**Criterio 1**
- **Dado** un usuario autenticado que sigue a otros usuarios y tiene seguidores,
- **Cuando** accede a la lista de seguidores y seguidos,
- **Entonces** ve ambas listas separadas y paginadas, mostrando la información básica de cada usuario.

**Criterio 2**
- **Dado** un usuario con muchos seguidores o seguidos,
- **Cuando** navega por la lista,
- **Entonces** la lista se pagina correctamente y puede avanzar o retroceder entre páginas sin perder el contexto.
| RF-021 | Ver el feed personalizado | Media | Pendiente |

#### Criterios de aceptación

### RF-021

**Criterio 1**
- **Dado** un usuario autenticado que sigue a otros usuarios y tags,
- **Cuando** accede a su feed personalizado,
- **Entonces** ve las publicaciones de los usuarios y tags que sigue, ordenadas por fecha.

**Criterio 2**
- **Dado** un usuario que sigue nuevos usuarios o tags,
- **Cuando** recarga su feed personalizado,
- **Entonces** las nuevas publicaciones de esos usuarios y tags aparecen en el feed.
| RF-022 | Publicar un anuncio oficial en una materia | Alta | Pendiente |

#### Criterios de aceptación

### RF-022

**Criterio 1**
- **Dado** un docente autenticado en una materia,
- **Cuando** marca una publicación como anuncio oficial,
- **Entonces** el anuncio aparece destacado en el muro de la materia para todos los inscritos.

**Criterio 2**
- **Dado** un docente que redacta una publicación en su materia,
- **Cuando** la marca como anuncio oficial y confirma,
- **Entonces** el anuncio queda registrado como oficial con autor, fecha y materia, y es visible para los estudiantes inscritos.
| RF-023 | Publicar un anuncio institucional en el muro general | Media | Pendiente |

#### Criterios de aceptación

### RF-023

**Criterio 1**
- **Dado** un administrador autenticado en la plataforma,
- **Cuando** publica un anuncio institucional desde el módulo Anuncios,
- **Entonces** el anuncio aparece en el muro general visible para todos los usuarios.

**Criterio 2**
- **Dado** un administrador que redacta un anuncio institucional,
- **Cuando** confirma la publicación,
- **Entonces** el anuncio queda registrado con autor, fecha y contenido, y es visible en el muro general.
| RF-024 | Destacar visualmente los anuncios oficiales | Media | Pendiente |

#### Criterios de aceptación

### RF-024

**Criterio 1**
- **Dado** un anuncio oficial publicado en el muro,
- **Cuando** el usuario visualiza el muro o el anuncio,
- **Entonces** el anuncio se muestra con un color y un ícono distintos que lo diferencian de las publicaciones normales.

**Criterio 2**
- **Dado** un anuncio oficial y una publicación regular en el mismo muro,
- **Cuando** el usuario los compara visualmente,
- **Entonces** puede identificar el anuncio oficial por su estilo destacado (color e ícono) sin necesidad de leer el contenido.

### Actividad de Moodle

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-025 | Mostrar tareas nuevas en el muro de la materia | Media | Pendiente |

#### Criterios de aceptación

### RF-025

**Criterio 1**
- **Dado** un docente que crea una nueva tarea en una materia,
- **Cuando** la tarea se publica,
- **Entonces** se genera automáticamente una publicación de actividad en el muro de la materia visible para los estudiantes inscritos.

**Criterio 2**
- **Dado** un estudiante inscrito en una materia con tareas nuevas,
- **Cuando** accede al muro de la materia,
- **Entonces** ve las publicaciones de actividad correspondientes a las tareas nuevas, con la información de la tarea y su fecha.

### Administración

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-042 | Configurar los límites del muro: longitud máxima de una publicación, cantidad máxima de imágenes por publicación y número de publicaciones mostradas por página | Baja | Pendiente |

#### Criterios de aceptación

### RF-042

**Criterio 1**
- **Dado** un administrador autenticado en el panel de Administración,
- **Cuando** configura la longitud máxima permitida para una publicación,
- **Entonces** las publicaciones que superen ese límite no pueden guardarse y el sistema muestra un mensaje de error.

**Criterio 2**
- **Dado** un administrador autenticado en el panel de Administración,
- **Cuando** configura el número máximo de imágenes por publicación y el número de publicaciones mostradas por página,
- **Entonces** el muro respeta esos límites al crear publicaciones y al paginar el feed de cada materia.

### Marketplace

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-035 | Buscar libros por título o autor | Media | Pendiente |

#### Criterios de aceptación

### RF-035

**Criterio 1**
- **Dado** que el usuario se encuentra en el buscador del Marketplace,
- **Cuando** ingresa el título de un libro existente y ejecuta la búsqueda de texto,
- **Entonces** el sistema debe retornar el libro correspondiente en los resultados.

**Criterio 2**
- **Dado** que el usuario se encuentra en el buscador del Marketplace,
- **Cuando** ingresa el nombre de un autor en el campo de texto,
- **Entonces** el sistema debe mostrar una lista con todos los libros registrados bajo ese autor.
### Notificaciones

| ID | Descripción | Prioridad | Estado |
|---|---|---|---|
| RF-027 | Notificar un comentario en mi publicación | Media | Pendiente |

#### Criterios de aceptación

### RF-027

**Criterio 1**
- **Dado** un usuario que tiene una publicación en el muro,
- **Cuando** otro usuario escribe un comentario en esa publicación,
- **Entonces** el autor recibe una notificación de Moodle con el nombre de quien comentó.

**Criterio 2**
- **Dado** un usuario que comenta su propia publicación,
- **Cuando** se guarda el comentario,
- **Entonces** el sistema no envía ninguna notificación.

## Requerimientos No Funcionales

| ID | Descripción | Categoría | Estado |
|---|---|---|---|
| RNF-001 | El plugin debe instalarse y funcionar sin errores en Moodle 4.5 LTS (rama MOODLE_405_STABLE). Verificación: ejecutar la suite PHPUnit y moodle-plugin-ci sobre Moodle 4.5 en el CI, que debe pasar sin fallos. | Compatibilidad | Pendiente |
| RNF-002 | El plugin debe instalarse y ejecutarse sin errores en PHP 8.1, 8.2 y 8.3. Verificación: ejecutar la suite PHPUnit y moodle-plugin-ci sobre las tres versiones de PHP en el CI, que debe pasar sin fallos. | Compatibilidad | Pendiente |


## Requerimientos de Sistema

| ID | Descripción |
|---|---|
| RS-001 | |
