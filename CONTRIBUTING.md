# Cómo contribuir a este proyecto

Gracias por tu interés en contribuir. Este proyecto usa
el **Forking Workflow**. Lee este documento antes de empezar.

---

## Flujo de trabajo

### 1. Haz fork del repositorio
Botón **Fork** en la esquina superior derecha de GitHub.

### 2. Clona tu fork
```bash
git clone https://github.com/TU-USUARIO/PROYECTO.git
cd PROYECTO
```

### 3. Agrega el repo original como upstream
```bash
git remote add upstream https://github.com/sis-inf/PROYECTO.git
```

### 4. Sincroniza antes de trabajar
```bash
git checkout dev
git pull upstream dev
```

### 5. Crea tu rama de trabajo
```bash
git checkout -b tipo/descripcion-corta
```

Ejemplos de nombres de rama:

feat/post-service-create
docs/rf-012-comentarios
fix/orden-comentarios
test/post-service-create
chore/configurar-phpcs-moodle
security/validar-capabilities-publicaciones

### 6. Trabaja y haz commits pequeños
```bash
git add .
git commit -m "tipo: descripción corta en presente"
```

### 7. Sube tu rama a tu fork
```bash
git push origin tipo/descripcion-corta
```

### 8. Abre un Pull Request
- Base: `sis-inf/PROYECTO` → rama `dev`
- Compare: `TU-USUARIO/PROYECTO` → tu rama

---

## Convención de commits

| Tipo | Cuándo usarlo |
|---|---|
| `feat:` | Nueva funcionalidad |
| `fix:` | Corrección de error |
| `docs:` | Documentación |
| `test:` | Pruebas |
| `chore:` | Configuración o CI/CD |
| `refactor:` | Mejora sin cambiar comportamiento |
| `security:` | Mejora de seguridad |
| `data:` | Análisis de datos |

### Ejemplos

feat: agregar creación de publicaciones en el muro
fix: corregir orden cronológico de comentarios
docs: documentar RF-012 para ver comentarios de una publicación
test: agregar pruebas para la creación de publicaciones
chore: configurar phpcs con el estándar de Moodle

---

## Reglas importantes

- ❌ Nunca hagas push directo a `main` o `dev`
- ❌ Nunca trabajes directamente en `main` o `dev`
- ✅ Un issue = una rama = un PR
- ✅ Todo PR debe referenciar su issue con `Closes #N`
- ✅ El PR debe pasar el CI antes de ser mergeado
- ✅ Todo PR necesita al menos una revisión

---

## Ramas del proyecto

| Rama | Propósito |
|---|---|
| `main` | Versión estable — solo recibe merges desde `dev` |
| `dev` | Rama de desarrollo principal |
| `feat/*` | Nuevas funcionalidades |
| `fix/*` | Correcciones |
| `docs/*` | Documentación |
| `test/*` | Pruebas |
| `chore/*` | Configuración |

## Convenciones de nombres de Moodle

Para mantener el código consistente con Moodle, usa estas convenciones:

- **Frankenstyle:** el nombre del componente es `local_chuspasocial`. Úsalo como prefijo cuando Moodle requiera identificar el plugin.
- **Tablas de base de datos:** sus nombres deben tener como máximo 28 caracteres y mantenerse relacionados con el componente.
- **Namespaces:** las clases del plugin deben usar el namespace `local_chuspasocial\...` y ubicarse dentro de `classes/` según su responsabilidad.
- **Claves de strings:** usa identificadores descriptivos en minúsculas y sin espacios, definidos en los archivos de idioma del componente.

---

## ¿No sabes por dónde empezar?

1. Revisa los issues abiertos con la etiqueta `good first issue`
2. Comenta en el issue que quieres trabajarlo
3. Espera confirmación antes de empezar
4. Sigue los pasos de este documento
