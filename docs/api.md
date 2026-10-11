# Documentación de API

## Base URL

http://localhost:PUERTO

## Endpoints

### GET /
**Descripción:**
**Parámetros:**
**Respuesta:**
```json
{
}
```
## Funciones Externas

### `local_chuspasocial_toggle_follow`

Permite seguir o dejar de seguir a un usuario o etiqueta (*tag*) sin necesidad de recargar la página.

**Descripción:**  
Alterna el estado de seguimiento (*follow/unfollow*) para un usuario o etiqueta especificados dentro de la plataforma.

**Parámetros:**  
* `component` (string): Tipo de objetivo a seguir. Valores permitidos: `'user'` o `'tag'`.
* `itemid` (int): ID del usuario o del tag al que se desea seguir o dejar de seguir.

**Capacidades requeridas:**  
* `local/chuspasocial:follow`

**Respuesta de ejemplo (JSON):**  
```json
{
  "status": true,
  "action": "followed",
  "message": "Ahora sigues a este usuario."
}
```

### `local_chuspasocial_report_item`

Permite reportar un contenido u objeto específico dentro de la plataforma.

**Descripción:**  
Envía un reporte sobre un elemento especificado indicando el componente, el ID del elemento y el motivo del reporte.

**Parámetros:**  
* `component` (string): Componente o tipo de elemento reportado (ej. `'post'`, `'comment'`, `'user'`).
* `itemid` (int): ID del elemento que se reporta.
* `reason` (string): Motivo o descripción del reporte.

**Capacidades requeridas:**  
* `local/chuspasocial:report`

**Respuesta de ejemplo (JSON):**  
```json
{
  "status": true,
  "message": "Reporte enviado con éxito."
}
```

## Códigos de error

| Código | Descripción |
|---|---|
| 200 | OK |
| 400 | Bad Request |
| 401 | Not Logged In |
| 403 | No Permissions |
| 500 | Internal Server Error |