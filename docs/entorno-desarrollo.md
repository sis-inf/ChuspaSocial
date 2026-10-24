# Entorno de desarrollo

## Windows con XAMPP

Esta gu├¡a deja ChuspaSocial funcionando en Windows con XAMPP y Moodle 4.5 LTS,
con los cambios de `src/` reflejados al instante en Moodle.
Fue probada con XAMPP (PHP 8.2.12), MariaDB 10.11.19 y Moodle 4.5.15.

### Requisitos

- Windows 10 u 11.
- [XAMPP](https://www.apachefriends.org/) con **PHP 8.1, 8.2 u 8.3**.
- **MariaDB 10.6.7 o superior** (o MySQL 8.0+), requisito de Moodle 4.5.
- [Git para Windows](https://git-scm.com/download/win).
- Tu fork de este repositorio clonado en tu equipo (en adelante, `<RUTA_REPO>`).

Comprueba las versiones que trae tu XAMPP:

```powershell
C:\xampp\php\php.exe --version
C:\xampp\mysql\bin\mysql.exe --version
```

Si MariaDB es anterior a 10.6.7 (por ejemplo, 10.4.x), sigue la
[actualizaci├│n de MariaDB](#actualizar-mariadb-si-tu-xampp-trae-una-versi├│n-anterior-a-1067)
antes de continuar.

### 1. Configurar PHP

1. Abre `C:\xampp\php\php.ini` y verifica que estas extensiones est├®n activas
   (sin `;` al inicio): `intl`, `zip`, `gd`, `soap`, `sodium` y `mysqli`.
2. Busca la l├¡nea `;max_input_vars = 1000` y d├®jala **sin `;`** y con este valor:

```ini
   max_input_vars = 5000
```

3. Guarda el archivo y reinicia **Apache** desde el XAMPP Control Panel.
4. Comprueba que PHP lee el valor nuevo:

```powershell
   C:\xampp\php\php.exe -r "echo ini_get('max_input_vars');"
```

   Debe responder `5000`.

### 2. Iniciar la base de datos y crearla

1. En el XAMPP Control Panel, inicia **Apache** y **MySQL**.
2. Comprueba que MariaDB responde en el puerto 3306:

```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT VERSION();"
```

3. Crea la base de datos de Moodle:

```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE moodle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Clonar Moodle 4.5

```powershell
cd C:\xampp\htdocs
git clone --branch MOODLE_405_STABLE --depth 1 https://github.com/moodle/moodle.git moodle
mkdir C:\xampp\moodledata
```

La carpeta `moodledata` debe quedar **fuera** de `htdocs`.

### 4. Enlazar el plugin en `local/chuspasocial`

Crea un enlace (junction) desde la carpeta `src/` de tu repositorio.
Este paso debe hacerse **antes** de instalar Moodle.

```powershell
New-Item -ItemType Junction `
  -Path C:\xampp\htdocs\moodle\local\chuspasocial `
  -Target <RUTA_REPO>\src
```

Comprueba el enlace:

```powershell
Test-Path C:\xampp\htdocs\moodle\local\chuspasocial\version.php
```

Debe responder `True`. Si `New-Item` da error de permisos, abre PowerShell como administrador.

### 5. Instalar Moodle

```powershell
cd C:\xampp\htdocs\moodle
C:\xampp\php\php.exe admin\cli\install.php `
  --wwwroot=http://localhost/moodle `
  --dataroot=C:\xampp\moodledata `
  --dbtype=mariadb --dbhost=127.0.0.1 --dbname=moodle `
  --dbuser=root --dbpass= `
  --fullname='ChuspaSocial Dev' --shortname=chuspa `
  --adminuser=admin --adminpass='<CONTRASE├æA>' `
  --adminemail=admin@example.com `
  --agree-license --non-interactive
```

- La contrase├▒a debe cumplir la pol├¡tica de Moodle: m├¡nimo 8 caracteres,
  con may├║scula, min├║scula, n├║mero y un s├¡mbolo.
- Tarda varios minutos. No lo interrumpas hasta ver `Installation completed successfully.`
- Las advertencias de `openssl_pkey_export` del m├│dulo LTI son normales en XAMPP
  y no impiden la instalaci├│n.

### 6. Verificar la instalaci├│n

1. Comprueba que se cre├│ la tabla del plugin:

```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "SHOW TABLES FROM moodle LIKE 'mdl_local_chuspasocial%';"
```

2. Abre `http://localhost/moodle` e inicia sesi├│n con el usuario `admin`.
3. Ve a **Administraci├│n del sitio > Extensiones > Resumen de extensiones**
   y busca `local_chuspasocial`. Debe aparecer instalado.

### Actualizar MariaDB si tu XAMPP trae una versi├│n anterior a 10.6.7

1. Descarga el **ZIP** (no el MSI) de MariaDB 10.11 LTS para Windows desde
   [mariadb.org/download](https://mariadb.org/download/).
   En **Package Type** elige `ZIP file`, no `ZIP file (debug symbols only)`.
2. En el XAMPP Control Panel, det├®n MySQL.
3. Aparta la carpeta vieja y coloca la nueva:

```powershell
   Rename-Item C:\xampp\mysql C:\xampp\mysql_old
   Expand-Archive "$HOME\Downloads\mariadb-10.11.*-winx64.zip" C:\xampp\
   Rename-Item (Get-Item C:\xampp\mariadb-10.11.*) mysql
   Copy-Item C:\xampp\mysql_old\bin\my.ini C:\xampp\mysql\bin\my.ini
```

4. Inicia MySQL desde el panel y comprueba la versi├│n:

```powershell
   C:\xampp\mysql\bin\mysql.exe --version
```

La base de datos nueva empieza vac├¡a y el usuario `root` queda sin contrase├▒a.
Para volver atr├ís, det├®n MySQL, elimina `C:\xampp\mysql` y renombra `mysql_old` a `mysql`.

### Problemas frecuentes

| S├¡ntoma | Causa probable | Soluci├│n |
| --- | --- | --- |
| El instalador pide una versi├│n mayor de MariaDB | XAMPP trae una MariaDB antigua | Seguir la secci├│n de actualizaci├│n de MariaDB |
| `ERROR 2002 Can't connect to server` | MySQL est├í detenido en el panel | Iniciar MySQL desde el XAMPP Control Panel |
| `max_input_vars` sigue en 1000 | La l├¡nea sigue comentada con `;` | Quitar el `;` en `php.ini` y reiniciar Apache |
| Falta una extensi├│n de PHP (`intl`, `zip`, etc.) | Est├í comentada en `php.ini` | Quitar el `;` y reiniciar Apache |
| `New-Item` da error de permisos | Faltan privilegios | Abrir PowerShell como administrador |
| El plugin no aparece en Extensiones | El enlace apunta a otra carpeta | Comprobar que `local\chuspasocial\version.php` existe |
| La instalaci├│n falla a medias | Error en el c├│digo del plugin | Corregirlo y reinstalar (ver abajo) |

### Reinstalar desde cero

Si la instalaci├│n falla, vac├¡a la base de datos y los archivos antes de repetirla:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE moodle; CREATE DATABASE moodle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Remove-Item C:\xampp\htdocs\moodle\config.php -ErrorAction SilentlyContinue
Remove-Item C:\xampp\moodledata\* -Recurse -Force
```

Despu├®s vuelve a ejecutar el paso 5.

### Instalaci├│n sin enlace (solo para probar)

Si solo quieres probar el plugin sin desarrollar, inst├ílalo desde un zip.
Los pasos est├ín en [`src/README.md`](../src/README.md).

## Problemas comunes

### Error `max_input_vars`

**S├¡ntoma**

Durante la instalaci├│n o comprobaci├│n del entorno, Moodle muestra el mensaje:

`PHP setting max_input_vars must be at least 5000.`

**Causa**

La directiva `max_input_vars` de PHP est├í configurada con un valor menor que `5000`. Moodle necesita al menos ese l├¡mite para procesar correctamente formularios con una gran cantidad de campos.

**Soluci├│n**

1. Localiza el archivo `php.ini` utilizado por el servidor web.
2. Busca la directiva `max_input_vars` y establece:

   ```ini
   max_input_vars = 5000
   ```

3. Guarda el archivo y reinicia el servicio web o PHP para aplicar el cambio.
4. Verifica el valor activo con:

   ```bash
   php -i | grep max_input_vars
   ```

El valor efectivo debe ser `5000` o superior.

## Moodle Docker

Esta sección explica cómo preparar un entorno de desarrollo utilizando Moodle 4.5, Docker y el código local del plugin ChuspaSocial. El objetivo es disponer de una instalación de Moodle separada de la instalación realizada con XAMPP, para desarrollar y probar los cambios del plugin sin tener que copiar los archivos manualmente cada vez.

El entorno utiliza el repositorio oficial [moodle-docker](https://github.com/moodlehq/moodle-docker), que proporciona los archivos de configuración necesarios para ejecutar Moodle y sus servicios mediante contenedores Docker.

### Requisitos

Antes de comenzar, comprobar que el equipo dispone de los siguientes programas:

- Windows 10 u 11.
- Git para Windows.
- Docker Desktop con el motor de Docker iniciado y WSL 2 habilitado.
- Visual Studio Code.
- Conexión a Internet.
- Espacio libre suficiente para descargar Moodle, las imágenes de Docker y los datos de la base de datos.

#### 1.1. Comprobar las herramientas instaladas

Abrir PowerShell y ejecutar los siguientes comandos, uno por uno:

```powershell
git --version
docker --version
docker compose version
```

Cada comando debe mostrar la versión correspondiente. Si alguno no se reconoce, instalar o configurar la herramienta que falta antes de continuar.

Después, comprobar que Docker puede ejecutar contenedores:

```powershell
docker info
```

Si aparece un error indicando que no puede conectarse al servidor de Docker, abrir Docker Desktop y esperar hasta que termine de iniciar.

### 2. Descargar el repositorio moodle-docker

El repositorio `moodle-docker` contiene los archivos que permiten levantar Moodle, el servidor web y la base de datos.

Abrir PowerShell y entrar en la carpeta de descargas:

```powershell
cd C:\Users\danap\Downloads
```

Descargar el repositorio oficial:

```powershell
git clone https://github.com/moodlehq/moodle-docker.git
```

Entrar en la carpeta descargada:

```powershell
cd moodle-docker
```

Comprobar que existen los archivos principales:

```powershell
Test-Path .\README.md
Test-Path .\config.docker-template.php
Test-Path .\bin
```

Los tres comandos deben devolver `True`.

**Importante:** si la carpeta `moodle-docker` ya existe, no volver a clonarla. Entrar en la carpeta existente y comprobar que contiene los archivos necesarios.

### 3. Descargar Moodle 4.5

Moodle debe descargarse en la carpeta `moodle`, dentro del directorio `moodle-docker`.

Primero, comprobar si ya existe una copia de Moodle:

```powershell
Test-Path .\moodle\version.php
```

Si el resultado es `False`, ejecutar:

```powershell
git clone -b MOODLE_405_STABLE https://github.com/moodle/moodle.git moodle
```

Este comando descarga la rama estable de Moodle 4.5.

Si el resultado anterior fue `True`, no volver a clonar. Comprobar la rama actual con:

```powershell
git -C .\moodle branch --show-current
```

Verificar que el archivo principal de versión está presente:

```powershell
Test-Path .\moodle\version.php
```

El resultado esperado es `True`.

La estructura inicial debe ser similar a la siguiente:

```text
Downloads/
└── moodle-docker/
    ├── bin/
    ├── base.yml
    ├── config.docker-template.php
    ├── README.md
    └── moodle/
        ├── admin/
        ├── lib/
        ├── local/
        ├── version.php
        └── ...
```

No se deben colocar los archivos de ChuspaSocial en la raíz de Moodle. El plugin debe quedar dentro de `local/chuspasocial`.

### 4. Configurar el entorno de Moodle Docker

Antes de iniciar los contenedores, es necesario indicar a `moodle-docker` dónde está el código de Moodle y qué motor de base de datos se utilizará.

Para este ejemplo se utilizará PostgreSQL, una de las opciones admitidas por el proyecto oficial. La configuración concreta debe mantenerse igual durante la preparación y el inicio de los contenedores.

Desde PowerShell, situado dentro de `moodle-docker`, ejecutar:

```powershell
$env:MOODLE_DOCKER_WWWROOT = (Resolve-Path .\moodle).Path
$env:MOODLE_DOCKER_DB = "pgsql"
```

Comprobar las variables:

```powershell
$env:MOODLE_DOCKER_WWWROOT
$env:MOODLE_DOCKER_DB
```

La primera debe mostrar la ruta completa de la carpeta `moodle` y la segunda debe mostrar `pgsql`.

A continuación, preparar el archivo de configuración que Moodle necesita para trabajar dentro de Docker:

```powershell
Copy-Item .\config.docker-template.php .\moodle\config.php
```

Comprobar que se creó correctamente:

```powershell
Test-Path .\moodle\config.php
```

El resultado esperado es `True`.

El archivo `config.docker-template.php` obtiene la configuración de conexión y otros parámetros del entorno Docker. Por ello, no debe sustituirse por una configuración de XAMPP ni introducir manualmente los datos de conexión de una instalación diferente.

Si se cierra PowerShell, las variables definidas con `$env:` dejan de estar disponibles en la nueva sesión. Por eso, deben volver a establecerse antes de ejecutar los comandos de Docker en otra sesión.

### 5. Integrar el código local de ChuspaSocial

El objetivo es que Moodle pueda acceder al plugin desde:

```text
local/chuspasocial
```

En el repositorio de ChuspaSocial, el código del plugin se encuentra dentro de `src/`, según la estructura del proyecto. Antes de crear el montaje, comprobar que existe el directorio:

```powershell
Test-Path C:\Users\danap\Downloads\Chuspasocial\src
```

El resultado esperado es `True`.

También es necesario revisar que `src/` contiene directamente los archivos y directorios propios del plugin. No se debe montar una carpeta que contenga otro nivel adicional innecesario, porque Moodle necesita encontrar los archivos del plugin en la ruta correcta.

#### 5.1. Crear el montaje del plugin

`moodle-docker` permite personalizar sus servicios mediante el archivo `local.yml`. Este archivo puede utilizarse para montar el código del plugin desde Windows dentro del contenedor web.

Desde la carpeta `moodle-docker`, crear o editar el archivo:

```text
moodle-docker/local.yml
```

Añadir la siguiente configuración:

```yaml
services:
  webserver:
    volumes:
      - "C:/Users/danap/Downloads/Chuspasocial/src:/var/www/html/local/chuspasocial"
```

Esta configuración es un ejemplo para la ubicación indicada. Debe verificarse que la carpeta local existe y que la ruta de destino corresponde al directorio de Moodle dentro del contenedor. Si `local.yml` ya contiene otras personalizaciones, añadir el montaje dentro del servicio existente en lugar de duplicar la clave `services` o el servicio `webserver`.

Guardar el archivo y comprobar que la ruta local no contiene errores.

El montaje permite que los cambios realizados en el código local se reflejen dentro del contenedor sin tener que copiar los archivos después de cada modificación.

**Precaución:** esta configuración solo es válida si `src/` es realmente la raíz del plugin Moodle. Si contiene otros módulos o si la estructura del proyecto es diferente, se debe ajustar el origen del montaje para que `version.php`, `db/` y los demás archivos del plugin queden directamente dentro de `local/chuspasocial`.

### 6. Iniciar los contenedores

Antes de iniciar Moodle, comprobar que Docker Desktop continúa activo y que PowerShell está situado en la carpeta `moodle-docker`.

Volver a establecer las variables de entorno si es necesario:

```powershell
$env:MOODLE_DOCKER_WWWROOT = (Resolve-Path .\moodle).Path
$env:MOODLE_DOCKER_DB = "pgsql"
```

El proyecto oficial incluye el comando `bin/moodle-docker-compose` para gestionar los servicios. En Windows, utilizar el mecanismo de ejecución compatible con la instalación y la versión descargada de `moodle-docker`; no asumir que todos los scripts de Linux se ejecutan directamente desde PowerShell.

Cuando se haya identificado el ejecutable o envoltorio compatible, iniciar los servicios en segundo plano mediante la operación equivalente a:

```text
moodle-docker-compose up -d
```

La primera ejecución puede tardar varios minutos porque Docker debe descargar las imágenes necesarias.

Comprobar el estado de los contenedores:

```powershell
docker ps
```

Revisar que los servicios web y de base de datos estén iniciados. Si algún servicio se detiene repetidamente, revisar los registros antes de continuar.

No instalar Moodle hasta que el servidor web y la base de datos estén disponibles.

### 7. Esperar a que la base de datos esté disponible

Aunque los contenedores aparezcan como iniciados, la base de datos puede necesitar tiempo adicional para prepararse.

Consultar los registros con:

```powershell
docker ps
```

Después, utilizar el procedimiento de espera de base de datos documentado por `moodle-docker` para la versión descargada. Algunos scripts auxiliares del proyecto están destinados a shells de tipo Unix, por lo que debe comprobarse su compatibilidad con Windows antes de ejecutarlos desde PowerShell.

Si la base de datos todavía no responde, esperar y revisar sus registros. No continuar con la instalación mientras el servicio no esté preparado.

### 8. Instalar Moodle y crear la base de datos

El proyecto `moodle-docker` proporciona un procedimiento para inicializar la base de datos de Moodle para pruebas manuales.

Una vez que los servicios estén preparados, ejecutar el comando de instalación documentado por el proyecto, desde la carpeta `moodle-docker` y dentro del contenedor web.

La forma del comando de inicialización de la base de datos es la siguiente:

```bash
bin/moodle-docker-compose exec webserver php admin/cli/install_database.php --agree-license --fullname="Moodle Docker" --shortname="moodle_docker" --summary="Entorno de desarrollo Moodle Docker" --adminpass="CambiarEstaClave123!" --adminemail="admin@example.com"
```

Este comando corresponde al procedimiento de inicialización de base de datos para pruebas manuales. La contraseña del ejemplo es únicamente ilustrativa: utilizar una contraseña de desarrollo adecuada y no reutilizarla en un entorno público o de producción.

En Windows, ejecutar el comando mediante el envoltorio compatible del proyecto o desde el shell que este documente. No copiar literalmente un comando Bash en PowerShell si el script no es compatible con ese shell.

La inicialización de base de datos no debe repetirse sobre una instalación existente sin comprobar antes su estado, porque puede provocar errores o pérdida de datos.

### 9. Abrir Moodle en el navegador

La configuración predeterminada de `moodle-docker` utiliza normalmente la dirección local:

```text
http://localhost:8000/
```

Abrir esta dirección en el navegador cuando los contenedores estén funcionando.

Comprobar lo siguiente:

1. La página de Moodle carga sin mostrar errores del servidor.
2. La instalación de Moodle se ha completado correctamente.
3. Es posible acceder con la cuenta de administración configurada durante la instalación.
4. Moodle muestra su página principal.
5. No aparecen errores de conexión con la base de datos.

Si el navegador no puede abrir Moodle, revisar el estado de los contenedores, los registros y la configuración del puerto. Si se ha cambiado el puerto predeterminado, utilizar la dirección correspondiente.

### 10. Verificar la integración de ChuspaSocial

Con Moodle funcionando, comprobar que el plugin está disponible en la ruta esperada.

Dentro del contenedor, la ruta de destino debe ser:

```text
/var/www/html/local/chuspasocial
```

Comprobar también que la carpeta contiene los archivos requeridos por el plugin Moodle, como su archivo `version.php` y los directorios que utilice el proyecto.

Después:

1. Iniciar sesión en Moodle con una cuenta administradora.
2. Acceder a la sección de administración de extensiones o notificaciones.
3. Comprobar si Moodle reconoce el plugin ChuspaSocial.
4. Si Moodle solicita instalar o actualizar la extensión, revisar la información que presenta antes de confirmar.
5. Verificar que el plugin no genera errores de PHP.
6. Modificar un archivo de prueba del plugin y comprobar que el cambio aparece en el código montado dentro del contenedor.

La comprobación final debe confirmar que Moodle reconoce el plugin y que los cambios del código local se reflejan correctamente. Si el plugin no aparece, revisar el montaje, la estructura de directorios y los permisos.

### 11. Revisar los registros y solucionar problemas

Si Moodle no funciona como se espera, consultar primero el estado de los contenedores:

```powershell
docker ps -a
```

Para consultar los registros de los servicios:

```powershell
docker logs NOMBRE_DEL_CONTENEDOR
```

Sustituir `NOMBRE_DEL_CONTENEDOR` por el nombre real mostrado por Docker.

Problemas habituales:

- **Docker no responde:** abrir Docker Desktop y comprobar que el motor está activo.
- **No se encuentra Moodle:** verificar `MOODLE_DOCKER_WWWROOT` y la existencia de `moodle/version.php`.
- **No se conecta a la base de datos:** comprobar que el servicio está iniciado y que la variable `MOODLE_DOCKER_DB` coincide con la configuración elegida.
- **Moodle no carga en el navegador:** revisar el puerto configurado y los registros del servidor web.
- **ChuspaSocial no aparece:** comprobar que el montaje apunta a la raíz correcta del plugin y que sus archivos están en `local/chuspasocial`.
- **Los cambios no se reflejan:** revisar que el volumen está montado correctamente y que se está editando la carpeta local correspondiente.
- **Se producen errores después de modificar la configuración:** revisar `local.yml`, corregir la sintaxis YAML y volver a crear los servicios siguiendo el procedimiento oficial.

### 12. Detener y reiniciar el entorno

Para detener los contenedores sin eliminar los datos del entorno, utilizar la operación equivalente a:

```text
moodle-docker-compose stop
```

Para volver a iniciarlos:

```text
moodle-docker-compose start
```

Si se necesita eliminar los contenedores creados para el entorno, el proyecto documenta la operación:

```text
moodle-docker-compose down
```

Antes de utilizar `down`, revisar las advertencias de la documentación y confirmar qué datos se conservarán. No eliminar volúmenes ni directorios de datos si se necesita mantener la instalación.

Al volver a trabajar, comprobar que Docker está iniciado, restablecer las variables de entorno y verificar que la base de datos esté disponible.

### 13. Prueba final del procedimiento

Antes de considerar terminada esta guía, ejecutar el proceso completo en un entorno limpio y registrar los resultados.

La comprobación debe incluir:

- [ ] Git, Docker y Docker Compose funcionan.
- [ ] El repositorio `moodle-docker` se descarga correctamente.
- [ ] Moodle 4.5 se encuentra en la rama estable correspondiente.
- [ ] El archivo `config.php` se prepara correctamente.
- [ ] Los contenedores se inician sin errores.
- [ ] La base de datos está disponible.
- [ ] Moodle abre en el navegador y permite iniciar sesión.
- [ ] El código de ChuspaSocial está montado en `local/chuspasocial`.
- [ ] Moodle reconoce el plugin.
- [ ] Los cambios locales del plugin se reflejan dentro del entorno Docker.
- [ ] El entorno puede detenerse y volver a iniciarse siguiendo los pasos documentados.

Si algún paso no funciona, corregir la guía para incluir el comando exacto que haya funcionado en el entorno utilizado. Registrar también las versiones de Docker, Moodle y `moodle-docker` que se probaron.

**Resultado esperado:** disponer de una instalación local de Moodle 4.5 administrada mediante Docker, con el código de ChuspaSocial accesible desde `local/chuspasocial` y preparada para realizar tareas de desarrollo y pruebas.

**Referencias oficiales**

- Repositorio y guía de `moodle-docker`: https://github.com/moodlehq/moodle-docker
- Repositorio de Moodle: https://github.com/moodle/moodle

