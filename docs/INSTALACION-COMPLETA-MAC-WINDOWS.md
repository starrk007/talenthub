# INSTALACIÓN COMPLETA DESDE CERO — TalentHub GTO

**Destinatarios:** alumnos universitarios sin instalación previa de PHP. **Sistemas:** macOS (Apple Silicon/Intel) y Windows 10/11 (PowerShell). **Fecha de revisión:** 26 de octubre de 2026.

## 1. Qué se instala, qué NO se instala

| Software | Versión objetivo | Para qué lo usaremos | Obligatorio |
|---|---|---|---|
| Visual Studio Code | Estable | Editor de código | Sí (o editor equivalente) |
| Git | Estable | Ramas y control de versiones | Sí |
| Node.js | **24.11+** (Node 24 LTS actualizado) | Compilar Nuxt y ejecutar npm | Sí |
| npm | Incluido en Node, 10+ | Dependencias del frontend | Sí |
| PHP | 8.4+ | API REST sin frameworks | Sí |
| PHP `curl` | Extensión habilitada | Llamadas HTTPS a Firebase | Sí desde sesión 02 |
| PHP `json` | Extensión disponible | Peticiones y respuestas JSON | Sí |
| PHP `openssl` / TLS | Soporte TLS | HTTPS a Google APIs | Sí desde sesión 02 |
| PHP `mbstring` | Extensión opcional | Longitud de textos Unicode | Recomendado |
| Firebase Spark | Gratis | Authentication y Firestore | Desde sesión 02 |
| Firebase CLI | Via npm, opcional | Desplegar reglas Firestore | Recomendado |
| Cuenta Netlify | Plan Free | Publicar SPA Nuxt | Sesión 04 |
| PHP hosting externo | Gratuito si reúne condiciones | Publicar API PHP | Sesión 04 |

No se necesita Apache, XAMPP, MAMP, Docker, MySQL, PostgreSQL, Composer, Laravel, Symfony, Slim, PHP SDK de Firebase ni Blaze. Usamos el servidor incorporado de PHP **solo en desarrollo**.

> Las nuevas versiones de Nuxt 4 requieren Node 22.21+ o Node 24.11+; aquí normalizamos la clase sobre Node 24 LTS. No recomendamos instalar una versión antigua de Node 22.

## 2. Instalación de PHP en macOS

### 2.1. Preparar Homebrew

En Terminal:

```bash
xcode-select --install
brew --version
```

Si `brew` no existe, instalarlo desde https://brew.sh siguiendo **sus instrucciones oficiales**. Al concluir, cargar el entorno según el mensaje que Homebrew muestre; ejemplo típico en Apple Silicon:

```bash
eval "$(/opt/homebrew/bin/brew shellenv)"
```

En Mac Intel el prefijo suele ser `/usr/local`, por lo que debe consultarse el prefijo real mediante `brew --prefix`.

### 2.2. PHP 8.4

```bash
brew update
brew install php@8.4
```

PHP 8.4 se instala como versión alternativa; agrega su binario al PATH en zsh **sin sobrescribir otros perfiles**:

```bash
echo 'export PATH="'"$(brew --prefix php@8.4)"'/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
which php
php -v
php --ini
php -m | grep -Ei 'curl|openssl|mbstring|json'
```

Esperado: `PHP 8.4.x` y módulos `curl`, `openssl` y `json`; se recomienda `mbstring`. Si `php` indica otra versión, ejecuta `echo "$PATH"` y comprueba la primera ruta con `which php`.

### 2.3. Node.js, Git y VS Code

Opción con instalador oficial Node 24 LTS: https://nodejs.org/en/download/ . También es válido instalar Node 24 mediante gestor de versiones. Verifica:

```bash
node -v
npm -v
git --version
```

Si instalas VS Code desde https://code.visualstudio.com/, en la paleta de comandos usa **Shell Command: Install 'code' command in PATH** para permitir `code .` desde Terminal.

## 3. Instalación de PHP en Windows 10/11

### 3.1. Obtener y extraer PHP sin XAMPP

1. Ve a https://windows.php.net/download/ . Selecciona PHP **8.4 x64 Non Thread Safe (NTS)** en ZIP, una compilación de la rama 8.4 (no un enlace obsoleto ni ARM si tu equipo es x64). Para el servidor integrado `php -S`, NTS es adecuado.
2. Instala el **Microsoft Visual C++ Redistributable x64** vigente, si lo exige el binario de PHP (enlace desde las instrucciones oficiales de PHP para Windows).
3. Crea `C:\\tools\\php` y extrae allí el contenido del ZIP. Debe quedar `C:\\tools\\php\\php.exe`, no un directorio anidado `php-8.4...`.
4. En `C:\\tools\\php`, copia `php.ini-development` y renómbralo `php.ini` (mantén la copia original).
5. Abre `php.ini` en VS Code o Notepad y comprueba lo siguiente:

```ini
extension_dir = "ext"
extension=curl
extension=openssl
extension=mbstring
```

`json` es una funcionalidad integrada en PHP moderno, no requiere descomentar `extension=json`. Si una extensión ya está habilitada, no agregues una línea duplicada.

### 3.2. Agregar PHP al PATH

Abre **Inicio → Editar las variables de entorno del sistema → Variables de entorno**. En `Path` del usuario selecciona `Nuevo` y agrega `C:\\tools\\php`. Cierra TODAS las terminales y abre una PowerShell nueva.

Ejecuta:

```powershell
where.exe php
php -v
php --ini
php -m | Select-String 'curl|openssl|mbstring|json'
```

`where.exe php` debe mostrar la ruta correcta. Si muestra PHP de XAMPP antes, cambia el orden de `Path` o usa directamente `C:\\tools\\php\\php.exe`.

### 3.3. Node, Git y VS Code

Instala Node.js **24 LTS** desde https://nodejs.org/en/download/ usando instalador Windows (.msi), Git desde https://git-scm.com/download/win y VS Code desde https://code.visualstudio.com/.

Comprueba en PowerShell NUEVA:

```powershell
node -v
npm -v
git --version
php -v
```

Si PowerShell bloquea `npm.ps1`, prueba `npm.cmd -v` o usa Git Bash para ejecutar `npm`. No recomendamos cambiar globalmente la política de ejecución para superar este error.

## 4. Verificar conectividad PHP HTTPS

```bash
php -r 'echo "PHP: ",PHP_VERSION,PHP_EOL; var_export(extension_loaded("curl")); echo PHP_EOL; var_export(extension_loaded("openssl")); echo PHP_EOL;'
```

En PowerShell:

```powershell
php -r 'echo PHP_VERSION, PHP_EOL; var_export(extension_loaded("curl")); echo PHP_EOL;'
```

La segunda comprobación debe imprimir `true`. **No deshabilites** `CURLOPT_SSL_VERIFYPEER` para resolver un error de certificado; corrige el CA bundle/`curl.cainfo` documentado por tu distribución PHP.

## 5. Primer repositorio vacío (sesión 01)

macOS:

```bash
mkdir -p "$HOME/Projects/talenthub-gto"
cd "$HOME/Projects/talenthub-gto"
git init -b master
code .
```

Windows PowerShell:

```powershell
New-Item -ItemType Directory -Path "$HOME\\Projects\\talenthub-gto" -Force | Out-Null
Set-Location "$HOME\\Projects\\talenthub-gto"
git init -b master
code .
```

Crea las carpetas/archivos siguiendo **`GUIA-SESION-01.md`**. No comiences copiando el ZIP final: ese ZIP es referencia/solucionario.

## 6. Instalar dependencias de Nuxt (al terminar los archivos package.json)

Desde la raíz del monorepo:

```bash
npm install
npm run check:php
npm run test:api
```

**Paquetes npm directos del frontend** (definidos en `apps/web/package.json`):

| Paquete | Uso |
|---|---|
| `nuxt` | Framework Vue para el frontend |
| `vue` | Componentes y reactividad |
| `firebase` | Firebase Authentication en navegador |
| `pinia` | Estado global |
| `@pinia/nuxt` | Integración Pinia / Nuxt |
| `tailwindcss` | Utilidades CSS |
| `@tailwindcss/vite` | Integración Tailwind 4 con Vite |
| `daisyui` | Componentes visuales Tailwind |
| `typescript` | Tipado |
| `vue-tsc` | Typecheck Vue |

**Paquetes PHP: NINGUNO**: toda la API se programa en PHP nativo. Para evitar inconsistencias en las clases, **no ejecutes** `npm create nuxt@latest` si ya seguiste la creación manual; el archivo `package.json` del proyecto define exactamente las dependencias.

## 7. Servidores locales (dos terminales)

Terminal A en raíz:

```bash
npm run dev:api
```

Terminal B en raíz:

```bash
npm run dev:web
```

Verificación:

- `http://127.0.0.1:8000/api/v1/health`: responde JSON `{ "ok": true, ... }`.
- `http://127.0.0.1:8000/api/v1/jobs`: lista de demostración en sesión 01; inicialmente vacía en sesión 02 hasta que haya una empresa y vacantes creadas.
- `http://localhost:3000`: frontend Nuxt.

No cierres una terminal mientras necesites el servidor correspondiente. Para detener, presiona `Ctrl + C`.

## 8. Firebase Spark desde la consola (sesión 02)

1. Ingresa a https://console.firebase.google.com/ y selecciona **Agregar proyecto** (elige un ID único; ejemplo `talenthub-gto-clase-2026`). No actives Blaze.
2. En el proyecto ve a **Authentication → Sign-in method → Email/Password** y habilítalo.
3. En **Firestore Database → Create database**, elige ubicación y **modo de producción**. La base por defecto debe llamarse `(default)`.
4. En **Configuración del proyecto → Tus apps**, registra una app `Web` (icono `</>`); copia `apiKey`, `authDomain`, `projectId` y `appId`.
5. Crea `apps/web/.env` desde `.env.example`, sustituyendo los cuatro valores públicos. No incluyas comillas si no hacen falta.
6. Crea `apps/api/config/local.php` desde `local.php.example` sustituyendo `firebase_project_id`, `firebase_api_key` y orígenes permitidos.
7. Abre **Firestore Database → Rules** y pega **el contenido completo de `firebase/firestore.rules`**, selecciona `Publish`. **Nunca uses** `allow read, write: if true`.
8. Opcional: instala Firebase CLI y despliega reglas:

```bash
npm install -g firebase-tools
firebase login
firebase projects:list
firebase use --add
firebase deploy --only firestore:rules
```

`firebase use --add` puede requerir una entrada en `.firebaserc` (creada por la CLI); evita guardar secretos. Ejecuta los comandos desde la raíz donde está `firebase.json`.

**Nota didáctica:** el token Firebase ID se obtiene en Nuxt con el SDK oficial; PHP lo recibe y hace llamadas cURL a REST Firestore; las reglas Firestore determinan cada permiso. Nunca coloques claves privadas de cuenta de servicio en el repositorio.

## 9. Cómo probar sesiones 03 y 04 con cuentas docentes

1. Registra con un correo de pruebas `empresa.ejemplo@...` como `COMPANY`; crea una vacante.
2. Cierra sesión y crea un correo distinto `alumno.ejemplo@...` como `STUDENT`; revisa la vacante.
3. Desde el alumno, postúlate; comprueba que no puede postularse dos veces.
4. Como empresa, abre el listado de candidatos y cambia el estado a `REVIEWING`.
5. Vuelve a estudiante y comprueba el cambio. No uses cuentas o información personal real en esta demostración.

## 10. Git/GitHub didáctico

```bash
git add .
git commit -m "chore: base proyecto TalentHub"
git switch -c develop
# antes de cada práctica:
git switch -c feature/sesion-01
# al terminar:
git add .
git commit -m "feat: sesion 01 php y nuxt"
git switch develop
git merge --no-ff feature/sesion-01
```

En sesiones siguientes, crea `feature/sesion-02`, etc. Al aprobar el trabajo, crea `release/v1.0.0` desde `develop`; pasa la rama de release a `master` tras pruebas y vuelve a integrar a `develop`. Configura el remoto GitHub después de crear el repositorio en GitHub; no se proporcionan URLs ficticias como si fueran repositorios existentes.

## 11. Netlify y API PHP (sesión 04)

**Limitación no negociable:** Netlify Free aloja Nuxt pero **no** el backend PHP puro. Mantendremos un único monorepo GitHub y dos alojamientos de ejecución (Netlify para web, hosting PHP para la API). Sin un hosting PHP externo o un cambio de backend, la API no será pública.

- Netlify: import GitHub, base directory raíz, build `npm run build:web`, publish `apps/web/.output/public`, Node `24`, variables `NUXT_PUBLIC_*`.
- API: hosting que **verifique** PHP 8.4+, cURL, HTTPS, escritura fuera de la carpeta pública, reglas `index.php`, peticiones salientes HTTPS a `identitytoolkit.googleapis.com` y `firestore.googleapis.com`.
- Establece `NUXT_PUBLIC_API_BASE=https://TU-API-PHP.example` en Netlify, redeploy y añade la URL Netlify a `allowed_origins` de PHP.
- Autoriza el dominio final en Firebase Authentication → Settings → Authorized domains.
- **No se garantiza** la disponibilidad de un hosting PHP gratis particular sin revisar su cuenta, restricciones, versión PHP y acceso a Google APIs.

Fuentes técnicas: https://www.php.net/manual/en/install.windows.php · https://formulae.brew.sh/formula/php@8.4 · https://firebase.google.com/docs/firestore/use-rest-api · https://firebase.google.com/docs/projects/billing/firebase-pricing-plans · https://firebase.google.com/docs/storage/faqs-storage-changes-announced-sept-2024 · https://docs.netlify.com/start/framework-setup/.

## 12. Errores frecuentes

| Error | Causa habitual | Corrección |
|---|---|---|
| `php: command not found` | PATH sin PHP | Reabrir Terminal; `which php` / `where.exe php` |
| `curl_init` no existe | extensión cURL no cargada | revisar `php.ini`, `php -m` y reiniciar terminal |
| PHP distinto al instalado | XAMPP o PHP viejo primero en PATH | priorizar PHP 8.4; revisar `php --ini` |
| `npm` no reconocido | Node no instalado en PATH | instalar Node 24 LTS, reabrir terminal |
| Engine `EBADENGINE` | Node viejo | cambiar a Node 24.11+ |
| `NUXT_PUBLIC_*` vacías | falta `.env` | copiar desde `.env.example` y reiniciar Nuxt |
| error 403 de Firestore | reglas incorrectas, rol/permisos | publicar `firebase/firestore.rules`, revisar rol e ID token |
| error 503 `firebase_project_id` | PHP local sin configurar | copiar `config/local.php.example` a `local.php` |
| `Failed to fetch` | PHP apagado, CORS u URL errónea | revisar puerto 8000, `NUXT_PUBLIC_API_BASE`, `allowed_origins` |
| `node_modules` no está | aún no corriste npm install | desde raíz `npm install` |
| `/api/...` muestra HTML en Netlify | falta API PHP pública | publicar API separada y configurar su URL o proxy |
| 404 recargando `/jobs/...` | redirección SPA no aplicada | revisar `netlify.toml` y publicar `.output/public` |

**Criterio de fin de instalación:** `php -v`, `php -m`, `node -v`, `npm -v`, `npm run check:php`, `npm run test:api`, `GET /api/v1/health` y página Nuxt accesible.
