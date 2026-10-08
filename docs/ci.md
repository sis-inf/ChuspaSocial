# Integración continua

ChuspaSocial utiliza GitHub Actions para ejecutar verificaciones automáticas y tareas relacionadas con integración continua, despliegue y seguridad.

## Workflows

| Workflow | Cuándo corre | Qué verifica o ejecuta |
|---|---|---|
| `ci.yml` — CI | En Pull Requests dirigidos a `dev` o `main` | Verifica que el Pull Request tenga una descripción y que haga referencia a un issue mediante `Closes #`, `Fixes #` o `Refs #`. |
| `deploy.yml` — Deploy | En cada push a `main` | Ejecuta el workflow de despliegue. Actualmente contiene un paso de ejemplo pendiente de definir para el despliegue del proyecto. |
| `security.yml` — Security scan | En cada push a `dev` y en Pull Requests dirigidos a `main` | Ejecuta un análisis de seguridad mediante CodeQL para JavaScript/TypeScript. |

## Protección de ramas

Las ramas `main` y `dev` deben estar protegidas para evitar cambios directos y mantener las verificaciones del proyecto.

Las reglas de protección son:

- Los cambios deben realizarse mediante un Pull Request.
- Cada Pull Request requiere al menos **1 aprobación** antes de poder integrarse.
- Los **checks obligatorios** de GitHub Actions deben completarse correctamente antes de integrar el Pull Request.
- No se permite realizar **force push** sobre las ramas protegidas.
- Estas reglas son activadas y administradas por el docente.

### Rama `dev`

Los Pull Requests dirigidos a `dev` deben pasar las verificaciones correspondientes antes de ser integrados. El workflow `CI` comprueba la descripción del Pull Request y que este haga referencia a un issue.

Además, `Security scan` se ejecuta cuando se realiza un push a `dev`.

### Rama `main`

Los cambios hacia `main` deben realizarse mediante Pull Request y cumplir las reglas de protección antes de ser integrados.

El workflow `CI` verifica los Pull Requests dirigidos a `main`, mientras que `Security scan` realiza el análisis de seguridad en los Pull Requests dirigidos a `main`.

El workflow `Deploy` se ejecuta después de un push a `main` para realizar las tareas de despliegue configuradas.