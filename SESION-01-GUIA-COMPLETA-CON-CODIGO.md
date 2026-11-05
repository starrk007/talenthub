# TALENTHUB GTO — SESIÓN 01/04

## Fundamentos, monorepo, instalación y primera API REST

**Duración de clase:** 90 minutos (el proyecto completo incluye trabajo complementario). **Resultado:** Desde una carpeta vacía, construir una API PHP que responde JSON y una interfaz Nuxt que muestra una vacante de demostración. **No requiere Firebase todavía**.

## 1. Antes de escribir código (10–15 min)

1. Lee `00-INSTALACION-MAC-WINDOWS.md` para instalar **PHP 8.4+, cURL, Node.js 24, npm, Git y VS Code**. PHP puro **no usa Composer**.
2. Crea la carpeta de trabajo y abre VS Code. Ejecuta **solo los comandos de tu sistema operativo**.

**macOS (Terminal):**

```bash
mkdir -p "$HOME/Projects/talenthub-gto"
cd "$HOME/Projects/talenthub-gto"
git init -b master
mkdir -p apps/api/{public,src,config,tests} apps/web/app/{assets/css,components,composables,pages/jobs,plugins,stores,types} firebase docs
code .
```

**Windows (PowerShell):**

```powershell
New-Item -ItemType Directory -Force -Path "$HOME/Projects/talenthub-gto" | Out-Null
Set-Location "$HOME/Projects/talenthub-gto"
git init -b master
$dirs = @('apps/api/public','apps/api/src','apps/api/config','apps/api/tests','apps/web/app/assets/css','apps/web/app/components','apps/web/app/composables','apps/web/app/pages/jobs','apps/web/app/plugins','apps/web/app/stores','apps/web/app/types','firebase','docs')
$dirs | ForEach-Object { New-Item -ItemType Directory -Force -Path $_ | Out-Null }
code .
```

3. En VS Code, abre `talenthub-gto`. Selecciona la carpeta indicada y usa **New File**, pega el contenido del bloque y guarda con `Ctrl+S` o `Cmd+S`.
4. **No ejecutes `npm install` todavía**; primero crearás los dos archivos `package.json` en los pasos 1 y 2 de la sección de código.

**Importante:** `package.json` y `apps/web/package.json` son archivos DISTINTOS y los dos son necesarios. No uses `npm create nuxt`; crearemos manualmente el proyecto para entender cada archivo.

## 2. Árbol del CÓDIGO FUENTE acumulado al terminar esta sesión

```text
talenthub-gto/
├── apps/
│   ├── api/
│   │   ├── config/
│   │   │   └── local.php.example
│   │   ├── public/
│   │   │   ├── .htaccess
│   │   │   └── index.php
│   │   ├── src/
│   │   │   ├── ApiException.php
│   │   │   ├── Application.php
│   │   │   ├── bootstrap.php
│   │   │   ├── Config.php
│   │   │   └── Http.php
│   │   ├── tests/
│   │   │   ├── requirements.php
│   │   │   └── smoke.php
│   │   └── router.php
│   └── web/
│       ├── app/
│       │   ├── assets/
│       │   │   └── css/
│       │   │       └── main.css
│       │   ├── components/
│       │   │   └── AppHeader.vue
│       │   ├── composables/
│       │   │   └── useApi.ts
│       │   ├── pages/
│       │   │   ├── jobs/
│       │   │   │   └── [id].vue
│       │   │   ├── [...slug].vue
│       │   │   └── index.vue
│       │   ├── plugins/
│       │   │   └── firebase.client.ts
│       │   ├── stores/
│       │   │   └── auth.ts
│       │   ├── types/
│       │   │   └── index.ts
│       │   └── app.vue
│       ├── .env.example
│       ├── nuxt.config.ts
│       └── package.json
├── .gitignore
├── netlify.toml
└── package.json
```

Este árbol muestra los **archivos de código y configuración de la aplicación**, sin las guías Markdown, `README` ni el material de respaldo del ZIP. Las carpetas `node_modules/`, `.nuxt/` y `.output/` son generadas por npm/Nuxt y tampoco aparecen. Los archivos locales `.env` y `local.php` son exclusivos de cada equipo.

## 3. Lista de archivos: ¿qué creo y qué reemplazo?

| Orden | Acción | Ruta exacta | ¿Para qué sirve? |
|---:|---|---|---|
| 01 | CREAR | `package.json` | Workspace npm: comandos comunes de instalación, ejecución y pruebas. |
| 02 | CREAR | `apps/web/package.json` | Paquetes npm reales de Nuxt, Vue, Firebase, Pinia, Tailwind y DaisyUI. |
| 03 | CREAR | `.gitignore` | Excluye node_modules, builds y configuración local del repositorio. |
| 04 | CREAR | `apps/web/nuxt.config.ts` | Configura Nuxt, Tailwind, Pinia, variables públicas y SSR estático. |
| 05 | CREAR | `apps/web/.env.example` | Plantilla de variables públicas para el navegador (no son secretos de servidor). |
| 06 | CREAR | `netlify.toml` | Comando de build y carpeta de publicación para Netlify. |
| 07 | CREAR | `apps/api/src/bootstrap.php` | Autocarga de clases PHP nativas, sin Composer. |
| 08 | CREAR | `apps/api/src/ApiException.php` | Errores HTTP controlados del backend. |
| 09 | CREAR | `apps/api/src/Config.php` | Lectura segura de la configuración local PHP. |
| 10 | CREAR | `apps/api/src/Http.php` | Lectura de JSON, tokens, CORS y respuestas HTTP. |
| 11 | CREAR | `apps/api/src/Application.php` | Enrutador y casos de uso REST; evoluciona en cada sesión. |
| 12 | CREAR | `apps/api/public/index.php` | Único punto de entrada público de la API. |
| 13 | CREAR | `apps/api/router.php` | Router del servidor integrado PHP para desarrollo local. |
| 14 | CREAR | `apps/api/public/.htaccess` | Reescritura de URL para alojamiento compatible con Apache. |
| 15 | CREAR | `apps/api/config/local.php.example` | Plantilla de Firebase y orígenes CORS del servidor PHP. |
| 16 | CREAR | `apps/api/tests/requirements.php` | Comprueba versión de PHP y extensiones requeridas. |
| 17 | CREAR | `apps/api/tests/smoke.php` | Pruebas PHP básicas de arranque y validaciones. |
| 18 | CREAR | `apps/web/app/assets/css/main.css` | Importaciones y tema Tailwind CSS 4 + DaisyUI. |
| 19 | CREAR | `apps/web/app/types/index.ts` | Interfaces TypeScript que describen los datos de la API. |
| 20 | CREAR | `apps/web/app/plugins/firebase.client.ts` | Inicialización de Firebase Auth en el navegador. |
| 21 | CREAR | `apps/web/app/composables/useApi.ts` | Cliente HTTP hacia la API PHP, con token cuando existe. |
| 22 | CREAR | `apps/web/app/stores/auth.ts` | Estado de sesión y perfil mediante Pinia. |
| 23 | CREAR | `apps/web/app/components/AppHeader.vue` | Barra de navegación; cambia al añadir áreas privadas. |
| 24 | CREAR | `apps/web/app/app.vue` | Diseño raíz de Nuxt. |
| 25 | CREAR | `apps/web/app/pages/index.vue` | Listado y búsqueda de vacantes. |
| 26 | CREAR | `apps/web/app/pages/jobs/[id].vue` | Detalle de una vacante y, después, postulación. |
| 27 | CREAR | `apps/web/app/pages/[...slug].vue` | Página para rutas no encontradas. |

> **Ruta:** todas las rutas son relativas a la carpeta `talenthub-gto/`. Un nombre como `apps/web/package.json` significa que debes crear el archivo **dentro de** `apps/web/`, no en la raíz. **Cada bloque de código es el archivo completo**; no copies el encabezado Markdown dentro del archivo. Las rutas con `[id].vue` conservan literalmente los corchetes.

## 4. CÓDIGO COMPLETO — copiar en este orden

### Paso 01 — `package.json` · CREAR ARCHIVO NUEVO

Workspace npm: comandos comunes de instalación, ejecución y pruebas.

**ATENCIÓN: este primer `package.json` va en la RAÍZ `talenthub-gto/`. No lo pongas dentro de `apps/web/`.**

**Ubicación en VS Code:** `talenthub-gto/package.json`.

```json
{
  "name": "talenthub-gto-monorepo",
  "version": "1.0.0",
  "private": true,
  "workspaces": [
    "apps/web"
  ],
  "scripts": {
    "check:php": "php apps/api/tests/requirements.php",
    "dev:web": "npm run dev -w @talenthub/web",
    "build:web": "npm run generate -w @talenthub/web",
    "typecheck": "npm run typecheck -w @talenthub/web",
    "dev:api": "php -S 127.0.0.1:8000 -t apps/api/public apps/api/router.php",
    "test:api": "php apps/api/tests/smoke.php"
  },
  "engines": {
    "node": ">=24.11.0",
    "npm": ">=10.0.0"
  }
}
```

### Paso 02 — `apps/web/package.json` · CREAR ARCHIVO NUEVO

Paquetes npm reales de Nuxt, Vue, Firebase, Pinia, Tailwind y DaisyUI.

**ATENCIÓN: este es OTRO archivo distinto: va dentro de `talenthub-gto/apps/web/`. Define TODOS los paquetes del frontend.**

**Ubicación en VS Code:** `talenthub-gto/apps/web/package.json`.

```json
{
  "name": "@talenthub/web",
  "version": "1.0.0",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "nuxt dev --host 0.0.0.0 --port 3000",
    "generate": "nuxt generate",
    "typecheck": "nuxt typecheck",
    "postinstall": "nuxt prepare"
  },
  "dependencies": {
    "nuxt": "^4.0.0",
    "vue": "^3.5.0",
    "firebase": "^12.0.0",
    "pinia": "^3.0.0",
    "@pinia/nuxt": "^0.11.0",
    "tailwindcss": "^4.0.0",
    "@tailwindcss/vite": "^4.0.0",
    "daisyui": "^5.0.0"
  },
  "devDependencies": {
    "typescript": "^5.8.0",
    "vue-tsc": "^3.1.0"
  },
  "engines": {
    "node": ">=24.11.0",
    "npm": ">=10.0.0"
  }
}
```

### Paso 03 — `.gitignore` · CREAR ARCHIVO NUEVO

Excluye node_modules, builds y configuración local del repositorio.

**Ubicación en VS Code:** `talenthub-gto/.gitignore`.

```text
node_modules/
.nuxt/
.output/
apps/web/.env
apps/api/config/local.php
.DS_Store
coverage/
*.log
*.zip
```

### Paso 04 — `apps/web/nuxt.config.ts` · CREAR ARCHIVO NUEVO

Configura Nuxt, Tailwind, Pinia, variables públicas y SSR estático.

**Ubicación en VS Code:** `talenthub-gto/apps/web/nuxt.config.ts`.

```typescript
import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  compatibilityDate: '2026-07-01',
  ssr: false,
  modules: ['@pinia/nuxt'],
  css: ['~/assets/css/main.css'],
  vite: { plugins: [tailwindcss()] },
  app: { head: { title: 'TalentHub GTO | Empleo universitario', meta: [{ name: 'description', content: 'Vacantes, prácticas profesionales y talento universitario en Guanajuato.' }] } },
  runtimeConfig: {
    public: {
      apiBase: '',
      firebaseApiKey: '',
      firebaseAuthDomain: '',
      firebaseProjectId: '',
      firebaseAppId: ''
    }
  }
})
```

### Paso 05 — `apps/web/.env.example` · CREAR ARCHIVO NUEVO

Plantilla de variables públicas para el navegador (no son secretos de servidor).

**Ubicación en VS Code:** `talenthub-gto/apps/web/.env.example`.

```dotenv
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000
NUXT_PUBLIC_FIREBASE_API_KEY=AIza_REEMPLAZAR
NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN=tu-proyecto.firebaseapp.com
NUXT_PUBLIC_FIREBASE_PROJECT_ID=tu-proyecto
NUXT_PUBLIC_FIREBASE_APP_ID=1:000000000000:web:xxxx
```

### Paso 06 — `netlify.toml` · CREAR ARCHIVO NUEVO

Comando de build y carpeta de publicación para Netlify.

**Ubicación en VS Code:** `talenthub-gto/netlify.toml`.

```toml
[build]
  command = "npm run build:web"
  publish = "apps/web/.output/public"
[build.environment]
  NODE_VERSION = "24"
[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

### Paso 07 — `apps/api/src/bootstrap.php` · CREAR ARCHIVO NUEVO

Autocarga de clases PHP nativas, sin Composer.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/bootstrap.php`.

```php
<?php
declare(strict_types=1);
spl_autoload_register(static function (string $class): void {
    $prefix = 'TalentHub\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = __DIR__ . '/' . substr($class, strlen($prefix)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
```

### Paso 08 — `apps/api/src/ApiException.php` · CREAR ARCHIVO NUEVO

Errores HTTP controlados del backend.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/ApiException.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class ApiException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $httpStatus = 400)
    {
        parent::__construct($message);
    }
}
```

### Paso 09 — `apps/api/src/Config.php` · CREAR ARCHIVO NUEVO

Lectura segura de la configuración local PHP.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Config.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class Config
{
    private static ?array $values = null;

    public static function get(string $key): mixed
    {
        if (self::$values === null) {
            $file = dirname(__DIR__) . '/config/local.php';
            self::$values = is_file($file) ? require $file : [];
        }
        $environment = getenv(strtoupper($key));
        return $environment !== false && $environment !== '' ? $environment : (self::$values[$key] ?? null);
    }

    public static function required(string $key): string
    {
        $value = self::get($key);
        if (!is_string($value) || $value === '' || str_contains($value, 'REEMPLAZAR') || $value === 'tu-proyecto') {
            throw new ApiException('Configura ' . $key . ' en config/local.php.', 503);
        }
        return $value;
    }
}
```

### Paso 10 — `apps/api/src/Http.php` · CREAR ARCHIVO NUEVO

Lectura de JSON, tokens, CORS y respuestas HTTP.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Http.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class Http
{
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function body(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || strlen($raw) > 32768) {
            throw new ApiException('JSON demasiado grande.', 413);
        }
        try {
            $data = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new ApiException('JSON inválido.', 400);
        }
        if (!is_array($data) || array_is_list($data)) {
            throw new ApiException('Se requiere un objeto JSON.', 400);
        }
        return $data;
    }

    public static function token(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        return preg_match('/^Bearer ([A-Za-z0-9._-]+)$/', $header, $match) ? $match[1] : null;
    }

    public static function cors(): void
    {
        $allowed = Config::get('allowed_origins') ?? ['http://localhost:3000', 'http://127.0.0.1:3000'];
        if (is_string($allowed)) {
            $allowed = array_map('trim', explode(',', $allowed));
        }
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if ($origin && in_array($origin, $allowed, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
            header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Authorization, Content-Type');
            header('Access-Control-Max-Age: 600');
        }
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
```

### Paso 11 — `apps/api/src/Application.php` · CREAR ARCHIVO NUEVO

Enrutador y casos de uso REST; evoluciona en cada sesión.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Application.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;
final class Application
{
    public static function handle(): void
    {
        try {
            Http::cors();
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            $jobs = [
                [
                    'id' => 'demo0000000000000000000001',
                    'ownerId' => 'demoEmpresa',
                    'companyName' => 'Empresa Demo GTO',
                    'title' => 'Prácticas de desarrollo PHP',
                    'location' => 'Irapuato, Guanajuato',
                    'modality' => 'HIBRIDO',
                    'description' => 'Aprende desarrollo backend PHP y participa en proyectos digitales universitarios.',
                    'status' => 'OPEN',
                    'createdAt' => gmdate('c'),
                ],
            ];
            if ($method === 'GET' && $path === '/api/v1/health') {
                Http::json(['ok' => true, 'service' => 'talenthub-php', 'version' => 'sesion-01']);
            }
            if ($method === 'GET' && $path === '/api/v1/jobs') {
                Http::json(['data' => $jobs]);
            }
            if ($method === 'GET' && preg_match('#^/api/v1/jobs/([A-Za-z0-9_-]+)$#', (string)$path, $matches)) {
                if ($matches[1] === $jobs[0]['id']) Http::json(['data' => $jobs[0]]);
                throw new ApiException('Vacante no encontrada.', 404);
            }
            throw new ApiException('Ruta no encontrada.', 404);
        } catch (ApiException $e) {
            Http::json(['error' => $e->getMessage()], $e->httpStatus);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            Http::json(['error' => 'Error interno del servidor.'], 500);
        }
    }
}
```

### Paso 12 — `apps/api/public/index.php` · CREAR ARCHIVO NUEVO

Único punto de entrada público de la API.

**Ubicación en VS Code:** `talenthub-gto/apps/api/public/index.php`.

```php
<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
\TalentHub\Application::handle();
```

### Paso 13 — `apps/api/router.php` · CREAR ARCHIVO NUEVO

Router del servidor integrado PHP para desarrollo local.

**Ubicación en VS Code:** `talenthub-gto/apps/api/router.php`.

```php
<?php
declare(strict_types=1);
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . '/public' . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}
require __DIR__ . '/public/index.php';
```

### Paso 14 — `apps/api/public/.htaccess` · CREAR ARCHIVO NUEVO

Reescritura de URL para alojamiento compatible con Apache.

**Ubicación en VS Code:** `talenthub-gto/apps/api/public/.htaccess`.

```text
DirectoryIndex index.php
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L,QSA]
```

### Paso 15 — `apps/api/config/local.php.example` · CREAR ARCHIVO NUEVO

Plantilla de Firebase y orígenes CORS del servidor PHP.

**Ubicación en VS Code:** `talenthub-gto/apps/api/config/local.php.example`.

```php
<?php
declare(strict_types=1);

// Copiar como local.php. No subir local.php a Git.
return [
    'firebase_project_id' => 'tu-proyecto',
    'firebase_api_key' => 'AIza_REEMPLAZAR',
    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'https://tu-sitio.netlify.app',
    ],
];
```

### Paso 16 — `apps/api/tests/requirements.php` · CREAR ARCHIVO NUEVO

Comprueba versión de PHP y extensiones requeridas.

**Ubicación en VS Code:** `talenthub-gto/apps/api/tests/requirements.php`.

```php
<?php
declare(strict_types=1);

$checks = [
    'PHP 8.4 o superior' => PHP_VERSION_ID >= 80400,
    'Extension JSON' => extension_loaded('json'),
    'Extension cURL' => extension_loaded('curl') && function_exists('curl_init'),
    'cURL con TLS/SSL' => extension_loaded('curl') && !empty(curl_version()['ssl_version'] ?? ''),
];

fwrite(STDOUT, 'TalentHub GTO - Requisitos PHP' . PHP_EOL);
fwrite(STDOUT, 'PHP: ' . PHP_VERSION . ' | PHP ini: ' . (php_ini_loaded_file() ?: '(ninguno)') . PHP_EOL);
foreach ($checks as $label => $ok) {
    fwrite(STDOUT, sprintf('%s %s%s', $ok ? '[OK]' : '[ERROR]', $label, PHP_EOL));
}
foreach (['mbstring', 'openssl'] as $optional) {
    fwrite(STDOUT, sprintf('[%s] %s (opcional)%s', extension_loaded($optional) ? 'OK' : 'INFO', $optional, PHP_EOL));
}
$ok = !in_array(false, $checks, true);
fwrite(STDOUT, $ok ? 'Requisitos obligatorios: OK' . PHP_EOL : 'Corrige los requisitos en docs/INSTALACION-MAC-WINDOWS.md' . PHP_EOL);
exit($ok ? 0 : 1);
```

### Paso 17 — `apps/api/tests/smoke.php` · CREAR ARCHIVO NUEVO

Pruebas PHP básicas de arranque y validaciones.

**Ubicación en VS Code:** `talenthub-gto/apps/api/tests/smoke.php`.

```php
<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
if (!class_exists(\TalentHub\Application::class)) {
    fwrite(STDERR, "No se pudo cargar la aplicación PHP.\n");
    exit(1);
}
echo "Smoke PHP sesión 01 OK\n";
```

### Paso 18 — `apps/web/app/assets/css/main.css` · CREAR ARCHIVO NUEVO

Importaciones y tema Tailwind CSS 4 + DaisyUI.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/assets/css/main.css`.

```css
@import 'tailwindcss';
@plugin 'daisyui' {
  themes: corporate --default, dark --prefersdark;
}
@theme {
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
}
body { font-family: var(--font-sans); }
```

### Paso 19 — `apps/web/app/types/index.ts` · CREAR ARCHIVO NUEVO

Interfaces TypeScript que describen los datos de la API.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/types/index.ts`.

```typescript
export type Role = 'STUDENT' | 'COMPANY' | 'ADMIN'
export type Modality = 'REMOTO' | 'HIBRIDO' | 'PRESENCIAL'
export type ApplicationStatus = 'SUBMITTED' | 'REVIEWING' | 'ACCEPTED' | 'REJECTED'

export interface Profile {
  id: string
  uid: string
  email: string
  name: string
  role: Role
  companyName: string
  university: string
  createdAt: string
}

export interface Job {
  id: string
  ownerId: string
  companyName: string
  title: string
  location: string
  modality: Modality
  description: string
  status: 'OPEN' | 'CLOSED'
  createdAt: string
}

export interface Application {
  id: string
  jobId: string
  jobTitle: string
  studentId: string
  studentName: string
  studentEmail: string
  companyId: string
  status: ApplicationStatus
  createdAt: string
  updatedAt: string
}

export interface ApiResponse<T> { data: T }
```

### Paso 20 — `apps/web/app/plugins/firebase.client.ts` · CREAR ARCHIVO NUEVO

Inicialización de Firebase Auth en el navegador.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/plugins/firebase.client.ts`.

```typescript
import { initializeApp, getApps, getApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig().public
  if (!config.firebaseApiKey || !config.firebaseProjectId || !config.firebaseAppId) {
    console.warn('Configura NUXT_PUBLIC_FIREBASE_* antes de iniciar sesión.')
    return { provide: { firebaseAuth: null as ReturnType<typeof getAuth> | null } }
  }
  const app = getApps().length ? getApp() : initializeApp({
    apiKey: config.firebaseApiKey,
    authDomain: config.firebaseAuthDomain,
    projectId: config.firebaseProjectId,
    appId: config.firebaseAppId
  })
  return { provide: { firebaseAuth: getAuth(app) } }
})
```

### Paso 21 — `apps/web/app/composables/useApi.ts` · CREAR ARCHIVO NUEVO

Cliente HTTP hacia la API PHP, con token cuando existe.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/composables/useApi.ts`.

```typescript
import type { ApiResponse } from '~/types'
import { getIdToken } from 'firebase/auth'

export function useApi() {
  const apiBase = useRuntimeConfig().public.apiBase
  async function request<T>(path: string, options: { method?: string; body?: object } = {}): Promise<T> {
    const auth = useNuxtApp().$firebaseAuth
    const headers: Record<string, string> = { 'Content-Type': 'application/json' }
    if (auth?.currentUser) {
      headers.Authorization = `Bearer ${await getIdToken(auth.currentUser)}`
    }
    const url = `${apiBase.replace(/\/$/, '')}/api/v1${path}`
    try {
      const response = await $fetch<ApiResponse<T>>(url, {
        method: (options.method ?? 'GET') as 'GET' | 'POST' | 'PATCH' | 'DELETE',
        body: options.body,
        headers
      })
      return response.data
    } catch (error: unknown) {
      const failure = error as { data?: { error?: string }; message?: string }
      throw new Error(failure?.data?.error || failure?.message || 'Error de conexión con la API.')
    }
  }
  return { request }
}
```

### Paso 22 — `apps/web/app/stores/auth.ts` · CREAR ARCHIVO NUEVO

Estado de sesión y perfil mediante Pinia.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/stores/auth.ts`.

```typescript
import { defineStore } from 'pinia'
import { createUserWithEmailAndPassword, signInWithEmailAndPassword, signOut, onAuthStateChanged, type User } from 'firebase/auth'
import type { Profile, Role } from '~/types'

export const useAuthStore = defineStore('auth', () => {
  const user = shallowRef<User | null>(null)
  const profile = ref<Profile | null>(null)
  const ready = ref(false)
  const busy = ref(false)
  let initialized = false

  async function loadProfile() {
    const currentUid = user.value?.uid
    if (!currentUid) { profile.value = null; return }
    try {
      const fetched = await useApi().request<Profile>('/me')
      if (user.value?.uid === currentUid) profile.value = fetched
    } catch {
      // Un usuario nuevo puede existir en Authentication antes de crear su perfil.
      if (user.value?.uid === currentUid && profile.value?.uid !== currentUid) profile.value = null
    }
  }

  async function initialize(): Promise<void> {
    if (initialized || !import.meta.client) return
    initialized = true
    const auth = useNuxtApp().$firebaseAuth
    if (!auth) { ready.value = true; return }
    onAuthStateChanged(auth, async (current) => {
      if (user.value?.uid !== current?.uid) profile.value = null
      user.value = current
      await loadProfile()
      ready.value = true
    })
  }

  async function register(input: { email: string; password: string; name: string; role: Exclude<Role, 'ADMIN'>; companyName: string; university: string }) {
    const auth = useNuxtApp().$firebaseAuth
    if (!auth) throw new Error('Configura Firebase antes de registrarte.')
    busy.value = true
    try {
      if (auth.currentUser?.email?.toLowerCase() === input.email.toLowerCase()) {
        user.value = auth.currentUser
      } else {
        const credential = await createUserWithEmailAndPassword(auth, input.email, input.password)
        user.value = credential.user
      }
      profile.value = await useApi().request<Profile>('/profiles', { method: 'POST', body: {
        name: input.name, role: input.role, companyName: input.companyName, university: input.university
      } })
    } finally { busy.value = false }
  }

  async function login(email: string, password: string) {
    const auth = useNuxtApp().$firebaseAuth
    if (!auth) throw new Error('Configura Firebase antes de iniciar sesión.')
    busy.value = true
    try {
      const credential = await signInWithEmailAndPassword(auth, email, password)
      user.value = credential.user
      await loadProfile()
    } finally { busy.value = false }
  }

  async function logout() {
    const auth = useNuxtApp().$firebaseAuth
    if (auth) await signOut(auth)
    user.value = null
    profile.value = null
    await navigateTo('/')
  }

  return { user, profile, ready, busy, initialize, loadProfile, register, login, logout }
})
```

### Paso 23 — `apps/web/app/components/AppHeader.vue` · CREAR ARCHIVO NUEVO

Barra de navegación; cambia al añadir áreas privadas.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/components/AppHeader.vue`.

```vue
<template>
  <header class="border-b border-base-300 bg-base-100">
    <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
      <NuxtLink to="/" class="text-xl font-black text-primary">TalentHub GTO</NuxtLink>
      <span class="text-sm opacity-70">Proyecto académico · PHP puro</span>
    </nav>
  </header>
</template>
```

### Paso 24 — `apps/web/app/app.vue` · CREAR ARCHIVO NUEVO

Diseño raíz de Nuxt.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/app.vue`.

```vue
<template>
  <div class="min-h-screen bg-base-200 text-base-content">
    <AppHeader />
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6"><NuxtPage /></main>
    <footer class="border-t border-base-300 py-6 text-center text-xs opacity-65">TalentHub GTO · Proyecto demostrativo académico</footer>
  </div>
</template>
```

### Paso 25 — `apps/web/app/pages/index.vue` · CREAR ARCHIVO NUEVO

Listado y búsqueda de vacantes.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/pages/index.vue`.

```vue
<template>
  <div class="space-y-7">
    <section class="rounded-2xl bg-primary px-6 py-10 text-primary-content sm:px-10">
      <div class="badge badge-outline mb-3 text-primary-content">Talento universitario en Guanajuato</div>
      <h1 class="max-w-2xl text-3xl font-black sm:text-5xl">Tu siguiente oportunidad comienza aquí.</h1>
      <p class="mt-4 max-w-xl opacity-90">Descubre empleos, prácticas profesionales y oportunidades para desarrollar tu carrera.</p>
      <NuxtLink to="/register" class="btn btn-accent mt-6">Crear perfil gratuito</NuxtLink>
    </section>
    <section>
      <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <div><h2 class="text-2xl font-bold">Vacantes disponibles</h2><p class="text-sm opacity-65">Empresas y oportunidades para estudiantes y egresados.</p></div>
        <input v-model="search" class="input input-bordered w-full sm:w-72" placeholder="Buscar título, ciudad o empresa" aria-label="Buscar vacantes" />
      </div>
      <p v-if="loading" class="loading loading-spinner text-primary" />
      <div v-else-if="error" role="alert" class="alert alert-error">{{ error }}</div>
      <div v-else-if="!filtered.length" class="rounded-xl border border-dashed border-base-300 bg-base-100 p-10 text-center">Sin vacantes por ahora. Las empresas podrán crear las primeras oportunidades.</div>
      <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <article v-for="job in filtered" :key="job.id" class="card border border-base-300 bg-base-100 shadow-sm">
          <div class="card-body">
            <div class="badge badge-secondary badge-outline">{{ job.modality }}</div>
            <h3 class="card-title">{{ job.title }}</h3>
            <p class="font-medium">{{ job.companyName }}</p>
            <p class="text-sm opacity-60">{{ job.location }}</p>
            <p class="line-clamp-3 text-sm">{{ job.description }}</p>
            <div class="card-actions mt-3"><NuxtLink :to="`/jobs/${job.id}`" class="btn btn-primary btn-sm">Ver vacante →</NuxtLink></div>
          </div>
        </article>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import type { Job } from '~/types'
const { request } = useApi()
const jobs = ref<Job[]>([])
const search = ref('')
const loading = ref(true)
const error = ref('')
const filtered = computed(() => jobs.value.filter(job => `${job.title} ${job.companyName} ${job.location}`.toLowerCase().includes(search.value.toLowerCase())))
onMounted(async () => {
  try { jobs.value = await request<Job[]>('/jobs') }
  catch (e) { error.value = (e as Error).message }
  finally { loading.value = false }
})
</script>
```

### Paso 26 — `apps/web/app/pages/jobs/[id].vue` · CREAR ARCHIVO NUEVO

Detalle de una vacante y, después, postulación.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/pages/jobs/[id].vue`.

```vue
<template>
  <div class="mx-auto max-w-3xl">
    <NuxtLink to="/" class="link link-primary text-sm">← Volver a vacantes</NuxtLink>
    <div v-if="loading" class="mt-6 loading loading-spinner" />
    <p v-else-if="error" class="alert alert-error mt-5">{{ error }}</p>
    <article v-else-if="job" class="mt-5 rounded-xl bg-base-100 p-7">
      <div class="badge badge-secondary mb-3">{{ job.modality }}</div>
      <h1 class="text-3xl font-bold">{{ job.title }}</h1>
      <p class="mt-3 font-medium text-primary">{{ job.companyName }}</p>
      <p class="text-sm opacity-65">{{ job.location }}</p>
      <div class="divider" />
      <p class="whitespace-pre-line leading-7">{{ job.description }}</p>
      <p class="mt-5 text-sm opacity-65">Las postulaciones se implementan en la sesión 04.</p>
    </article>
  </div>
</template>
<script setup lang="ts">
import type { Job } from '~/types'
const route = useRoute()
const { request } = useApi()
const job = ref<Job | null>(null)
const error = ref('')
const loading = ref(true)
onMounted(async () => {
  try { job.value = await request<Job>(`/jobs/${route.params.id}`) }
  catch (e) { error.value = (e as Error).message }
  finally { loading.value = false }
})
</script>
```

### Paso 27 — `apps/web/app/pages/[...slug].vue` · CREAR ARCHIVO NUEVO

Página para rutas no encontradas.

**Ubicación en VS Code:** `talenthub-gto/apps/web/app/pages/[...slug].vue`.

```vue
<template>
  <div class="rounded-xl bg-base-100 p-12 text-center"><h1 class="text-5xl font-black">404</h1><p class="my-5">Página no encontrada.</p><NuxtLink to="/" class="btn btn-primary">Volver al inicio</NuxtLink></div>
</template>
```

## 5. Configuración que NO debe omitirse

Los dos archivos `package.json` se crean en los pasos **01 y 02** de la sección anterior. Sus paquetes no se instalan individualmente: **desde la raíz** se ejecuta `npm install` después de haber guardado todos los archivos del frontend.

**Archivo local `apps/web/.env` — CREAR MANUALMENTE (no se incluye en Git):**

```dotenv
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000
```

- La URL `http://127.0.0.1:8000` apunta al servidor PHP, mientras que Nuxt corre en `http://localhost:3000`.
- No necesitas crear `apps/api/config/local.php` todavía; se configurará en la sesión 02.
- Aunque el frontend contiene un plugin Firebase, no se conecta durante esta sesión.
- `node_modules/`, `.nuxt/` y `.output/` se generan automáticamente; **no los crees a mano**.

**Ejecuta en la RAÍZ del proyecto (`talenthub-gto/`):**

```bash
npm install
npm run check:php
npm run test:api
```

**Inicia DOS terminales separadas, ambas ubicadas en la raíz:**

```bash
# Terminal A: backend PHP
npm run dev:api
```

```bash
# Terminal B: frontend Nuxt
npm run dev:web
```

**Prueba desde el navegador:**

- `http://127.0.0.1:8000/api/v1/health`: JSON con `"ok":true`.
- `http://127.0.0.1:8000/api/v1/jobs`: lista con una vacante de demostración.
- `http://localhost:3000`: tarjetas de vacantes.

**Prueba en consola (macOS / Git Bash):**

```bash
curl -i http://127.0.0.1:8000/api/v1/health
curl -i http://127.0.0.1:8000/api/v1/jobs
```

**En Windows PowerShell:**

```powershell
curl.exe -i http://127.0.0.1:8000/api/v1/health
curl.exe -i http://127.0.0.1:8000/api/v1/jobs
```

**Versionamiento:**

```bash
git add .
git commit -m "feat: sesion 01 PHP y Nuxt base"
git switch -c develop
```

**Evidencia:** terminal del API, pantalla inicial Nuxt, respuesta `/health`, estado de Git.

