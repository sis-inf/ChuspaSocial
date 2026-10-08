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

---

## Actualización del plugin

Procedimiento para actualizar `local_chuspasocial` a una nueva versión en un sitio Moodle de producción. Requiere acceso de **Administrador del sitio** y acceso al servidor donde está instalado Moodle.

### 1. Respaldo previo
Antes de cualquier cambio, respalde:
- La base de datos de Moodle.
- La carpeta de datos `moodledata`.
- La carpeta actual del plugin, por ejemplo:
  ```text
  cp -r /var/www/moodle/local/chuspasocial /root/respaldo-chuspasocial
  ```

### 2. Activar el modo mantenimiento
Desde la carpeta raíz de Moodle:
```text
php admin/cli/maintenance.php --enable
```

### 3. Reemplazar la carpeta `local/chuspasocial`
1. Descargue el archivo `local_chuspasocial.zip` de la nueva versión desde la sección de **Releases** del repositorio.
2. Elimine la carpeta anterior del plugin. **No copie la nueva versión encima de la anterior**: los archivos que fueron eliminados en la nueva versión quedarían en el servidor.
   ```text
   rm -rf /var/www/moodle/local/chuspasocial
   ```
3. Descomprima la nueva versión en `local/`, de modo que el resultado sea `local/chuspasocial/version.php`:
   ```text
   unzip local_chuspasocial.zip -d /var/www/moodle/local/
   ```
4. Devuelva los permisos al usuario del servidor web (por ejemplo `www-data`):
   ```text
   chown -R www-data:www-data /var/www/moodle/local/chuspasocial
   ```

### 4. Ejecutar la actualización
Elija **una** de las dos opciones.

**Opción A: desde Notificaciones (interfaz web)**
1. Inicie sesión como **Administrador del sitio**.
2. Vaya a **Administración del sitio** > **Notificaciones**. Moodle detecta la nueva versión del plugin.
3. Revise la lista de plugins a actualizar, donde `local_chuspasocial` debe aparecer con la versión nueva.
4. Haga clic en **Actualizar base de datos de Moodle ahora**.

**Opción B: desde la consola (`admin/cli/upgrade.php`)**
```text
sudo -u www-data php admin/cli/upgrade.php --non-interactive
```
El proceso termina sin errores cuando muestra `-->local_chuspasocial` seguido de `++ Success ++`.

### 5. Desactivar el modo mantenimiento y purgar cachés
```text
php admin/cli/maintenance.php --disable
php admin/cli/purge_caches.php
```

### 6. Verificación
- En **Administración del sitio** > **Plugins** > **Vista general de los plugins**, `local_chuspasocial` muestra la nueva versión.
- **Administración del sitio** > **Notificaciones** no muestra actualizaciones pendientes.

> Si Notificaciones no detecta ninguna actualización, el número `$plugin->version` del nuevo `version.php` no es mayor que el instalado. Restaure la carpeta desde el respaldo del paso 1.

