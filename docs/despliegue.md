# Guía de Despliegue

## Instalación desde archivo ZIP (Producción)

Esta guía describe el procedimiento para instalar el plugin `local_chuspasocial` mediante la interfaz gráfica de Moodle para entornos de producción.

### Requisitos previos
- Cuenta con rol de **Administrador del sitio** en Moodle.
- Versión de Moodle 4.5 o superior.
- Archivo comprimido `local_chuspasocial.zip` descargado desde la sección de **Releases** del repositorio.

### Pasos de instalación
1. Inicie sesión en la plataforma Moodle como **Administrador del sitio**.
2. Navegue a **Administración del sitio** > **Plugins** > **Instalar plugins**.
3. En la sección **Cargar un archivo ZIP**, arrastre el archivo `local_chuspasocial.zip` o selecciónelo mediante el explorador de archivos.
4. En la casilla **Tipo de plugin**, asegúrese de seleccionar `Local plugin (local)`.
5. Presione el botón **Instalar plugin desde archivo ZIP**.
6. En la pantalla de verificación de requerimientos y compatibilidad, presione **Continuar**.
7. En la vista de actualización de la base de datos, haga clic en **Actualizar base de datos de Moodle ahora**.

---

## Evidencia de validación en instalación limpia

El procedimiento de instalación desde el archivo `.zip` fue validado exitosamente en un entorno con una instalación limpia de Moodle.

### Registro de instalación
- **Entorno:** Moodle 4.5+ (Build: 20241018) | PHP 8.1.x
- **Método de prueba:** Carga de `local_chuspasocial.zip` a través de `/admin/tool/installaddon/index.php`.
- **Resultado:** El plugin fue detectado, instalado y registrado en la base de datos correctamente sin presentar errores de dependencias ni advertencias durante el proceso.