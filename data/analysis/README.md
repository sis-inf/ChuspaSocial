# Análisis de métricas

Esta carpeta reúne las consultas SQL que miden las métricas de éxito de ChuspaSocial.
Todas devuelven solo agregados o identificadores numéricos (`userid`): ninguna expone
nombres, correos ni contenido de publicaciones.

## Métricas analizadas

| Métrica | Consulta | Qué mide | Tablas de origen (en la consulta) | Tablas del plugin (docs/modelo-datos.md) |
|---|---|---|---|---|
| Usuarios más activos | `usuarios_activos.sql` | Publicaciones, comentarios e interacciones totales por usuario, de mayor a menor. | `tabla_publicaciones`, `tabla_comentarios` (campo `userid`) | `local_chuspasocial_post`, `local_chuspasocial_comment` |
| Tasa de libros vendidos | `tasa_vendidos.sql` | Por mes: libros publicados, libros vendidos y la tasa vendidos / publicados. | `tabla_libros` (campos `fecha_publicacion`, `fecha_venta`) | `local_chuspasocial_listing` |

Las consultas usan nombres genéricos de tabla y columna; antes de correrlas hay que
ajustarlos al esquema real descrito en `docs/modelo-datos.md`.

## Ejemplo de resultado

Usuarios más activos (`usuarios_activos.sql`):

| userid | total_publicaciones | total_comentarios | interacciones_totales |
|---|---|---|---|
| 14 | 12 | 34 | 46 |
| 5 | 8 | 15 | 23 |
| 89 | 1 | 5 | 6 |

Tasa de libros vendidos (`tasa_vendidos.sql`):

| anio | mes | libros_publicados | libros_vendidos | tasa_vendidos |
|---|---|---|---|---|
| 2026 | 7 | 40 | 12 | 0.30 |
| 2026 | 8 | 55 | 31 | 0.56 |
| 2026 | 9 | 48 | 29 | 0.60 |

Los valores son de ejemplo, no datos reales.

## Datos personales

- Los usuarios se identifican solo por `userid`; no se cruzan con la tabla `user` de Moodle.
- La tasa de libros vendidos solo devuelve totales por mes.
