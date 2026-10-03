# Arquitectura del Sistema

## Visión general
El plugin `local_chuspasocial` para Moodle 4.5 implementa una arquitectura modular por capas enfocada en la mantenibilidad, escalabilidad y la integración nativa con el core de Moodle.

## Descripciones de capas y componentes

### 1. Servicios Internos (`classes/local/`)
Encapsulan las reglas de negocio, validaciones del sistema y lógica de dominio del plugin. Aíslan los controladores HTTP y las API externas de la manipulación directa de datos.

### 2. Clases Persistent (`classes/persistent.php` / `classes/local/persistent/`)
Representan las entidades de datos y la capa de abstracción sobre la base de datos de Moodle (`$DB`). Gestionan la validación del esquema, las definiciones de campos y el ciclo de vida de los registros persistentes.

### 3. Funciones Externas (`classes/external/`)
Exponen endpoints y servicios web AJAX compatibles con la External API de Moodle. Se encargan de la recepción de solicitudes, validación de parámetros de entrada (`external_function_parameters`) y estructuración de respuestas devueltas al cliente.

### 4. Plantillas Mustache (`templates/`)
Definen la capa de presentación visual de la interfaz de usuario mediante sintaxis Mustache. Renderizan la estructura HTML de los componentes sociales del plugin de forma desacoplada de la lógica PHP.

### 5. Módulos AMD (`amd/src/`)
Implementan la lógica del lado del cliente utilizando JavaScript (AMD/ES6). Gestionan las interacciones dinámicas del usuario, la manipulación del DOM y las llamadas asíncronas (AJAX) a las funciones externas de Moodle.

---

## Tecnologías utilizadas

| Capa / Componente | Ubicación / Tecnología | Justificación |
|---|---|---|
| **Lógica de Negocio** | `classes/local/` (PHP 8.1+) | Centraliza las reglas del dominio y la reutilización de código. |
| **Persistencia** | `Persistent` / API `$DB` | Abstracción ORM nativa de Moodle para manejo de tablas. |
| **API Externa / AJAX** | `classes/external/` | Expone funciones web seguras para interacción asíncrona. |
| **Interfaz de Usuario** | `templates/*.mustache` | Plantillas Mustache nativas para renderizado limpio. |
| **Frontend Dinámico** | `amd/src/*.js` | Módulos AMD/RequireJS para interactividad del cliente. |