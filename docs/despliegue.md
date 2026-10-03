# Guía de Despliegue

## Ajustes del plugin
A continuación se describen las opciones de configuración disponibles para el administrador del sitio en la plataforma Moodle para el plugin `local_chuspasocial`:

| Ajuste | Nombre interno | Descripción | Valor por defecto |
| :--- | :--- | :--- | :--- |
| **Habilitar Marketplace** | `enablemarketplace` | Permite activar o desactivar el módulo de mercado dentro del plugin. | `1` (Habilitado) |
| **Longitud máxima del post** | `maxpostlength` | Número máximo de caracteres permitidos por cada publicación. | `280` |
| **Imágenes máximas por post** | `maxpostimages` | Cantidad máxima de imágenes adjuntas que se pueden incluir en una publicación. | `4` |
| **Publicaciones por página** | `postsperpage` | Límite de publicaciones que se renderizan por cada página o carga. | `10` |
| **Moneda** | `currency` | Código de moneda de tres letras (ISO 4217) utilizado en el Marketplace. | `USD` |

## Evidencia de validación en instalación limpia
Los pasos de instalación y los ajustes descritos fueron probados y validados en un entorno con una instalación limpia de Moodle.

### Registro de ejecución CLI (`admin/cli/upgrade.php`)
```text
$ php admin/cli/upgrade.php --non-interactive
== Upgrading Moodle database from version 4.5 (Build: 20241018) to 4.5+ ==

-->local_chuspasocial
++ Success (0.04s) ++

Database upgrade completed successfully.
Purging caches...
Done.