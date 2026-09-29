# Arquitectura del Sistema

## Visión general

## Componentes principales

## Diagrama de arquitectura
[Insertar diagrama]

## Tecnologías utilizadas

| Componente | Tecnología | Versión | Justificación |
|---|---|---|---|
| Plataforma Core / LMS | Moodle | 4.5 | Plataforma base para la gestión académica y funcionalidades del entorno. |
| Lenguaje Backend | PHP | 8.1+ | Lenguaje nativo del servidor sobre el cual está construido el core de Moodle 4.5. |
| Motor de Plantillas | Mustache | 2.0+ | Sistema oficial de plantillas para renderizar interfaces desacopladas de la lógica PHP. |
| Cargador de Módulos Frontend | AMD (RequireJS) | 2.3+ | Estándar de Moodle para la carga modular y asíncrona de scripts en el navegador. |
| Pruebas Unitarias / Integración | PHPUnit | 9.5+ | Framework utilizado para la ejecución de suite de pruebas unitarias y de componentes. |
| Pruebas de Aceptación / BDD | Behat | 3.x | Herramienta para automatizar pruebas de comportamiento e interfaz de usuario de extremo a extremo. |

## Decisiones de diseño

### Decisión 1
**Contexto:**
**Decisión:**
**Consecuencias:**

## Flujo de datos