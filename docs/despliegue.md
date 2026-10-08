# Guía de Despliegue

## Prerrequisitos

Antes de instalar `local_chuspasocial` en un sitio Moodle de producción, el administrador debe confirmar que el servidor cumple con lo siguiente.

### Versiones soportadas

| Componente | Versión requerida | Cómo verificarlo |
|---|---|---|
| Moodle | 4.5 LTS | **Administración del sitio** > **Notificaciones**: la versión aparece al pie de la página. |
| PHP | 8.1, 8.2 u 8.3 | **Administración del sitio** > **Servidor** > **Información PHP**, o en consola: `php -v` |

> Moodle 4.5 también exige su propia base de datos compatible (por ejemplo MariaDB 10.6.7+, MySQL 8.0+ o PostgreSQL 13+). Si Moodle ya funciona en el servidor, este requisito está cubierto.

### Cron activo

El plugin usa tareas programadas de Moodle (notificaciones y procesos en segundo plano), por lo que el cron debe ejecutarse **cada minuto**.

1. Agregue la siguiente línea al crontab del usuario del servidor web (por ejemplo `www-data`), ajustando las rutas a su instalación:
   ```text
   * * * * * /usr/bin/php /var/www/moodle/admin/cli/cron.php > /dev/null
   ```
   Para editar el crontab de ese usuario: `sudo crontab -u www-data -e`
2. Verifique que el cron está corriendo en **Administración del sitio** > **Servidor** > **Tareas** > **Tareas programadas**: la columna de última ejecución debe mostrar horas recientes.
3. Si el cron no se ha ejecutado en las últimas 24 horas, Moodle muestra una advertencia en **Administración del sitio** > **Notificaciones**.

### Mensajería habilitada

El plugin envía notificaciones y permite contactar a vendedores del Marketplace mediante la mensajería de Moodle.

- Desde la interfaz: busque el ajuste `messaging` en el buscador de **Administración del sitio** y active **Habilitar sistema de mensajería**. Guarde los cambios.
- Desde la consola (en la carpeta raíz de Moodle):
  ```text
  php admin/cli/cfg.php --name=messaging --set=1
  ```

### Etiquetas (tags) habilitadas

El muro social usa la API de etiquetas de Moodle para los hashtags, el seguimiento de temas y el filtrado de publicaciones.

- Desde la interfaz: busque el ajuste `usetags` en el buscador de **Administración del sitio** (está en **Características avanzadas**) y active la funcionalidad de etiquetas. Guarde los cambios.
- Desde la consola:
  ```text
  php admin/cli/cfg.php --name=usetags --set=1
  ```

### Comprobación rápida

Los siguientes comandos deben devolver `1`:

```text
php admin/cli/cfg.php --name=messaging
php admin/cli/cfg.php --name=usetags
```

---

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