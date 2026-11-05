# TALENTHUB GTO — SESIÓN 02/04

## Firebase Spark y consumo de Firestore REST con PHP

**Duración de clase:** 90 minutos (el proyecto completo incluye trabajo complementario). **Resultado:** Conectar PHP a Firestore usando cURL y su API REST (sin librerías PHP), y configurar las reglas de seguridad. La lista queda vacía hasta crear vacantes en la sesión 03.

## 1. Preparar Firebase Spark y el proyecto anterior (15–20 min)

**No vuelvas a crear el monorepo.** Abre el mismo `talenthub-gto/` que funcionó en la sesión 01. **Los dos `package.json` se conservan sin cambios.**

```bash
git switch develop
git switch -c feature/sesion-02
```

1. En https://console.firebase.google.com/ crea un proyecto en **Spark (gratuito)**. No habilites Blaze.
2. En **Authentication → Sign-in method** activa Email/Password.
3. En **Firestore Database** crea `(default)` en **modo producción**; selecciona la región adecuada (la región es fija para esa base).
4. En **Configuración del proyecto → Tus apps** registra una **app web**. Copia `apiKey`, `authDomain`, `projectId`, `appId`.
5. Crea los archivos nuevos o modificados de la tabla de la sección 3. **No instales paquetes npm nuevos**: Firebase ya estaba declarado en `apps/web/package.json` desde la sesión 01.

**Configuración local, macOS:**

```bash
cp apps/web/.env.example apps/web/.env
cp apps/api/config/local.php.example apps/api/config/local.php
```

**Configuración local, Windows PowerShell:**

```powershell
Copy-Item apps/web/.env.example apps/web/.env
Copy-Item apps/api/config/local.php.example apps/api/config/local.php
```

**Contenido esperado de `apps/web/.env`** (sustituye valores de ejemplo por los datos de TU Firebase):

```dotenv
NUXT_PUBLIC_API_BASE=http://127.0.0.1:8000
NUXT_PUBLIC_FIREBASE_API_KEY=AIza_TU_API_KEY
NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN=tu-proyecto.firebaseapp.com
NUXT_PUBLIC_FIREBASE_PROJECT_ID=tu-proyecto
NUXT_PUBLIC_FIREBASE_APP_ID=1:000000000000:web:tu-app-id
```

**`apps/api/config/local.php`** es una COPIA editable del `apps/api/config/local.php.example` de sesión 01. Abre la copia y reemplaza `firebase_project_id`, `firebase_api_key` y `allowed_origins` con los dominios locales. **No uses ese nombre en la consola Firebase**: es un archivo local PHP. Ambos archivos (`.env`, `local.php`) están en `.gitignore` y no deben subirse a GitHub.

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
│   │   │   ├── Auth.php
│   │   │   ├── bootstrap.php
│   │   │   ├── Config.php
│   │   │   ├── Firestore.php
│   │   │   ├── Http.php
│   │   │   ├── Remote.php
│   │   │   └── Validator.php
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
├── firebase/
│   ├── firestore.indexes.json
│   └── firestore.rules
├── .gitignore
├── firebase.json
├── netlify.toml
└── package.json
```

Este árbol muestra los **archivos de código y configuración de la aplicación**, sin las guías Markdown, `README` ni el material de respaldo del ZIP. Las carpetas `node_modules/`, `.nuxt/` y `.output/` son generadas por npm/Nuxt y tampoco aparecen. Los archivos locales `.env` y `local.php` son exclusivos de cada equipo.

## 3. Lista de archivos: ¿qué creo y qué reemplazo?

| Orden | Acción | Ruta exacta | ¿Para qué sirve? |
|---:|---|---|---|
| 01 | CREAR | `apps/api/src/Remote.php` | Cliente HTTPS cURL con control de errores. |
| 02 | CREAR | `apps/api/src/Validator.php` | Validación de texto, enums e IDs. |
| 03 | CREAR | `apps/api/src/Firestore.php` | Operaciones REST de documentos Firestore y transformación de tipos. |
| 04 | CREAR | `apps/api/src/Auth.php` | Consulta y validación de identidad en Firebase Authentication. |
| 05 | **REEMPLAZAR COMPLETO** | `apps/api/src/Application.php` | Enrutador y casos de uso REST; evoluciona en cada sesión. |
| 06 | **REEMPLAZAR COMPLETO** | `apps/api/tests/smoke.php` | Pruebas PHP básicas de arranque y validaciones. |
| 07 | CREAR | `firebase.json` | Indica a Firebase CLI dónde están las reglas e índices. |
| 08 | CREAR | `firebase/firestore.rules` | Permisos de lectura/escritura vinculados al usuario autenticado. |
| 09 | CREAR | `firebase/firestore.indexes.json` | Índices Firestore para consultas. |

> **Ruta:** todas las rutas son relativas a la carpeta `talenthub-gto/`. Un nombre como `apps/web/package.json` significa que debes crear el archivo **dentro de** `apps/web/`, no en la raíz. **Cada bloque de código es el archivo completo**; no copies el encabezado Markdown dentro del archivo. Las rutas con `[id].vue` conservan literalmente los corchetes.

## 4. CÓDIGO COMPLETO — copiar en este orden

### Paso 01 — `apps/api/src/Remote.php` · CREAR ARCHIVO NUEVO

Cliente HTTPS cURL con control de errores.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Remote.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class Remote
{
    public static function request(string $method, string $url, ?array $body = null, ?string $token = null): array
    {
        if (!function_exists('curl_init')) {
            throw new ApiException('PHP requiere extensión cURL.', 503);
        }
        $curl = curl_init($url);
        if ($curl === false) {
            throw new ApiException('No se puede inicializar cURL.', 503);
        }
        $headers = ['Accept: application/json', 'Content-Type: application/json'];
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        if ($body !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR));
        }
        $raw = curl_exec($curl);
        $code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($raw === false) {
            throw new ApiException('Servicio remoto no disponible.', 502);
        }
        $decoded = json_decode($raw, true);
        if ($code >= 400) {
            if ($code === 401 || $code === 403) {
                throw new ApiException('Acceso denegado por Firebase.', 403);
            }
            if ($code === 404) {
                throw new ApiException('Recurso no encontrado.', 404);
            }
            if ($code === 409) {
                throw new ApiException('Registro duplicado.', 409);
            }
            throw new ApiException('Error de servicio externo (' . $code . ').', $code >= 500 ? 502 : 422);
        }
        if (!is_array($decoded)) {
            throw new ApiException('Respuesta remota inválida.', 502);
        }
        return $decoded;
    }
}
```

### Paso 02 — `apps/api/src/Validator.php` · CREAR ARCHIVO NUEVO

Validación de texto, enums e IDs.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Validator.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class Validator
{
    public static function text(array $data, string $field, int $max = 180, int $min = 1): string
    {
        $value = $data[$field] ?? null;
        if (!is_string($value)) {
            throw new ApiException("El campo {$field} debe ser texto.", 422);
        }
        $value = trim($value);
        $length = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
        if ($length < $min || $length > $max) {
            throw new ApiException("El campo {$field} requiere entre {$min} y {$max} caracteres.", 422);
        }
        return $value;
    }

    public static function oneOf(array $data, string $field, array $values): string
    {
        $value = self::text($data, $field, 64);
        if (!in_array($value, $values, true)) {
            throw new ApiException("Valor no permitido para {$field}.", 422);
        }
        return $value;
    }

    public static function documentId(string $id): string
    {
        if (!preg_match('/^[A-Za-z0-9_-]{8,160}$/', $id)) {
            throw new ApiException('Identificador inválido.', 400);
        }
        return $id;
    }
}
```

### Paso 03 — `apps/api/src/Firestore.php` · CREAR ARCHIVO NUEVO

Operaciones REST de documentos Firestore y transformación de tipos.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Firestore.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class Firestore
{
    private string $base;

    public function __construct()
    {
        $this->base = 'https://firestore.googleapis.com/v1/projects/'
            . rawurlencode(Config::required('firebase_project_id'))
            . '/databases/(default)/documents';
    }

    private static function value(mixed $value): array
    {
        if ($value === null) return ['nullValue' => null];
        if (is_bool($value)) return ['booleanValue' => $value];
        if (is_int($value)) return ['integerValue' => (string) $value];
        if (is_float($value)) return ['doubleValue' => $value];
        if (is_string($value)) return ['stringValue' => $value];
        if (is_array($value) && array_is_list($value)) return ['arrayValue' => ['values' => array_map(self::value(...), $value)]];
        if (is_array($value)) return ['mapValue' => ['fields' => self::fields($value)]];
        throw new ApiException('Tipo de dato no soportado.', 422);
    }

    private static function fields(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) $result[$key] = self::value($value);
        return $result;
    }

    private static function decode(array $value): mixed
    {
        if (array_key_exists('stringValue', $value)) return $value['stringValue'];
        if (array_key_exists('integerValue', $value)) return (int) $value['integerValue'];
        if (array_key_exists('doubleValue', $value)) return (float) $value['doubleValue'];
        if (array_key_exists('booleanValue', $value)) return $value['booleanValue'];
        if (array_key_exists('nullValue', $value)) return null;
        if (array_key_exists('timestampValue', $value)) return $value['timestampValue'];
        if (array_key_exists('arrayValue', $value)) return array_map(self::decode(...), $value['arrayValue']['values'] ?? []);
        if (array_key_exists('mapValue', $value)) return self::decodeFields($value['mapValue']['fields'] ?? []);
        return null;
    }

    private static function decodeFields(array $fields): array
    {
        $data = [];
        foreach ($fields as $key => $value) $data[$key] = self::decode($value);
        return $data;
    }

    private static function document(array $raw): array
    {
        $value = self::decodeFields($raw['fields'] ?? []);
        $value['id'] = basename($raw['name'] ?? '');
        return $value;
    }

    public function get(string $collection, string $id, ?string $token): ?array
    {
        Validator::documentId($id);
        try {
            return self::document(Remote::request('GET', $this->base . '/' . $collection . '/' . $id, null, $token));
        } catch (ApiException $e) {
            if ($e->httpStatus === 404) return null;
            throw $e;
        }
    }

    public function query(string $collection, string $field, string $value, ?string $token, int $limit = 30): array
    {
        $body = ['structuredQuery' => [
            'from' => [['collectionId' => $collection]],
            'where' => ['fieldFilter' => [
                'field' => ['fieldPath' => $field],
                'op' => 'EQUAL',
                'value' => ['stringValue' => $value],
            ]],
            'limit' => max(1, min($limit, 30)),
        ]];
        $results = Remote::request('POST', $this->base . ':runQuery', $body, $token);
        $documents = [];
        foreach ($results as $row) {
            if (isset($row['document'])) $documents[] = self::document($row['document']);
        }
        return $documents;
    }

    public function create(string $collection, string $id, array $data, string $token): array
    {
        Validator::documentId($id);
        return self::document(Remote::request('POST', $this->base . '/' . $collection . '?documentId=' . rawurlencode($id), ['fields' => self::fields($data)], $token));
    }

    public function update(string $collection, string $id, array $data, string $token): array
    {
        Validator::documentId($id);
        $query = http_build_query([
            'updateMask' => ['fieldPaths' => array_keys($data)],
            'currentDocument' => ['exists' => 'true'],
        ], '', '&', PHP_QUERY_RFC3986);
        return self::document(Remote::request('PATCH', $this->base . '/' . $collection . '/' . $id . '?' . $query, ['fields' => self::fields($data)], $token));
    }
}
```

### Paso 04 — `apps/api/src/Auth.php` · CREAR ARCHIVO NUEVO

Consulta y validación de identidad en Firebase Authentication.

**Ubicación en VS Code:** `talenthub-gto/apps/api/src/Auth.php`.

```php
<?php
declare(strict_types=1);
namespace TalentHub;

final class Auth
{
    public static function identity(?string $token): array
    {
        if (!$token) {
            throw new ApiException('Debes iniciar sesión.', 401);
        }
        $apiKey = Config::required('firebase_api_key');
        try {
            $response = Remote::request('POST', 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . rawurlencode($apiKey), ['idToken' => $token]);
        } catch (ApiException) {
            throw new ApiException('Sesión vencida o token inválido.', 401);
        }
        $user = $response['users'][0] ?? null;
        if (!is_array($user) || !isset($user['localId']) || ($user['disabled'] ?? false)) {
            throw new ApiException('Usuario no autorizado.', 401);
        }
        return ['uid' => $user['localId'], 'email' => $user['email'] ?? ''];
    }

    public static function profile(string $token): array
    {
        $identity = self::identity($token);
        $profile = (new Firestore())->get('users', $identity['uid'], $token);
        if (!$profile) {
            throw new ApiException('Completa tu perfil antes de continuar.', 403);
        }
        if (($profile['uid'] ?? '') !== $identity['uid']) {
            throw new ApiException('Perfil inconsistente.', 403);
        }
        return $profile;
    }

    public static function role(string $token, string $required): array
    {
        $profile = self::profile($token);
        if (($profile['role'] ?? '') !== $required) {
            throw new ApiException('No tienes permisos para esta operación.', 403);
        }
        return $profile;
    }
}
```

### Paso 05 — `apps/api/src/Application.php` · REEMPLAZAR EL ARCHIVO COMPLETO

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
            $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');
            $parts = explode('/', $path);
            if (array_slice($parts, 0, 2) !== ['api', 'v1']) {
                throw new ApiException('Ruta no encontrada.', 404);
            }
            $route = array_slice($parts, 2);
            $token = Http::token();
            if ($method === 'GET' && $route === ['health']) {
                Http::json(['ok' => true, 'service' => 'talenthub-php', 'version' => 'sesion-02']);
            }
            $db = new Firestore();
            if ($method === 'GET' && $route === ['jobs']) {
                Http::json(['data' => $db->query('jobs', 'status', 'OPEN', $token)]);
            }
            if ($method === 'GET' && count($route) === 2 && $route[0] === 'jobs') {
                $job = $db->get('jobs', $route[1], $token);
                if (!$job) throw new ApiException('Vacante no encontrada.', 404);
                Http::json(['data' => $job]);
            }
            throw new ApiException('Ruta no encontrada.', 404);
        } catch (ApiException $e) {
            Http::json(['error' => $e->getMessage()], $e->httpStatus);
        } catch (\Throwable $e) {
            error_log('TalentHub API: ' . $e->getMessage());
            Http::json(['error' => 'Error interno del servidor.'], 500);
        }
    }
}
```

### Paso 06 — `apps/api/tests/smoke.php` · REEMPLAZAR EL ARCHIVO COMPLETO

Pruebas PHP básicas de arranque y validaciones.

**Ubicación en VS Code:** `talenthub-gto/apps/api/tests/smoke.php`.

```php
<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
use TalentHub\Validator;
use TalentHub\ApiException;
assert_options(ASSERT_ACTIVE, 1);
assert(Validator::documentId('abcdefgh1234') === 'abcdefgh1234');
assert(Validator::text(['title' => ' Nuevo empleo '], 'title') === 'Nuevo empleo');
$threw = false;
try { Validator::documentId('../private'); } catch (ApiException) { $threw = true; }
if (!$threw) { fwrite(STDERR, "ERROR: identificador inválido permitido\n"); exit(1); }
echo "Smoke PHP OK\n";
```

### Paso 07 — `firebase.json` · CREAR ARCHIVO NUEVO

Indica a Firebase CLI dónde están las reglas e índices.

**Ubicación en VS Code:** `talenthub-gto/firebase.json`.

```json
{
  "firestore": {
    "rules": "firebase/firestore.rules",
    "indexes": "firebase/firestore.indexes.json"
  }
}
```

### Paso 08 — `firebase/firestore.rules` · CREAR ARCHIVO NUEVO

Permisos de lectura/escritura vinculados al usuario autenticado.

**Ubicación en VS Code:** `talenthub-gto/firebase/firestore.rules`.

```javascript
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    function signedIn() { return request.auth != null; }
    function hasRole(role) {
      return signedIn()
        && exists(/databases/$(database)/documents/users/$(request.auth.uid))
        && get(/databases/$(database)/documents/users/$(request.auth.uid)).data.role == role;
    }
    function admin() { return hasRole('ADMIN'); }
    function hasKeys(keys) {
      return request.resource.data.keys().hasOnly(keys)
        && request.resource.data.keys().hasAll(keys);
    }

    match /users/{uid} {
      allow get: if (signedIn() && request.auth.uid == uid) || admin();
      allow list: if admin();
      allow create: if signedIn() && request.auth.uid == uid
        && hasKeys(['uid', 'email', 'name', 'role', 'companyName', 'university', 'createdAt'])
        && request.resource.data.uid == uid
        && request.resource.data.email == request.auth.token.email
        && request.resource.data.name is string
        && request.resource.data.name.size() >= 2
        && request.resource.data.name.size() <= 100
        && request.resource.data.role in ['STUDENT', 'COMPANY']
        && request.resource.data.companyName is string
        && request.resource.data.university is string
        && request.resource.data.createdAt is string;
      allow update, delete: if false;
    }

    match /jobs/{jobId} {
      allow get, list: if resource.data.status == 'OPEN'
        || (signedIn() && resource.data.ownerId == request.auth.uid)
        || admin();
      allow create: if hasRole('COMPANY')
        && hasKeys(['ownerId', 'companyName', 'title', 'location', 'modality', 'description', 'status', 'createdAt'])
        && request.resource.data.ownerId == request.auth.uid
        && request.resource.data.companyName == get(/databases/$(database)/documents/users/$(request.auth.uid)).data.companyName
        && request.resource.data.status == 'OPEN'
        && request.resource.data.title is string
        && request.resource.data.title.size() >= 5
        && request.resource.data.title.size() <= 120
        && request.resource.data.description is string
        && request.resource.data.description.size() >= 20
        && request.resource.data.description.size() <= 2500
        && request.resource.data.location is string
        && request.resource.data.modality in ['REMOTO', 'HIBRIDO', 'PRESENCIAL']
        && request.resource.data.createdAt is string;
      allow update: if hasRole('COMPANY')
        && resource.data.ownerId == request.auth.uid
        && request.resource.data.ownerId == resource.data.ownerId
        && request.resource.data.companyName == resource.data.companyName
        && request.resource.data.createdAt == resource.data.createdAt
        && request.resource.data.diff(resource.data).affectedKeys().hasOnly(['title', 'location', 'modality', 'description', 'status'])
        && request.resource.data.status in ['OPEN', 'CLOSED']
        && request.resource.data.title.size() >= 2
        && request.resource.data.title.size() <= 120
        && request.resource.data.description.size() >= 20
        && request.resource.data.description.size() <= 2500;
      allow delete: if false;
    }

    match /applications/{applicationId} {
      allow get, list: if signedIn()
        && (resource.data.studentId == request.auth.uid
          || resource.data.companyId == request.auth.uid || admin());
      allow create: if hasRole('STUDENT')
        && hasKeys(['jobId', 'studentId', 'studentName', 'studentEmail', 'companyId', 'jobTitle', 'status', 'createdAt', 'updatedAt'])
        && request.resource.data.studentId == request.auth.uid
        && applicationId == request.resource.data.jobId + '_' + request.auth.uid
        && request.resource.data.studentEmail == request.auth.token.email
        && request.resource.data.studentName == get(/databases/$(database)/documents/users/$(request.auth.uid)).data.name
        && exists(/databases/$(database)/documents/jobs/$(request.resource.data.jobId))
        && get(/databases/$(database)/documents/jobs/$(request.resource.data.jobId)).data.status == 'OPEN'
        && request.resource.data.companyId == get(/databases/$(database)/documents/jobs/$(request.resource.data.jobId)).data.ownerId
        && request.resource.data.jobTitle == get(/databases/$(database)/documents/jobs/$(request.resource.data.jobId)).data.title
        && request.resource.data.status == 'SUBMITTED'
        && request.resource.data.createdAt is string
        && request.resource.data.updatedAt is string;
      allow update: if hasRole('COMPANY')
        && resource.data.companyId == request.auth.uid
        && request.resource.data.diff(resource.data).affectedKeys().hasOnly(['status', 'updatedAt'])
        && request.resource.data.status in ['REVIEWING', 'ACCEPTED', 'REJECTED']
        && request.resource.data.updatedAt is string;
      allow delete: if false;
    }
  }
}
```

### Paso 09 — `firebase/firestore.indexes.json` · CREAR ARCHIVO NUEVO

Índices Firestore para consultas.

**Ubicación en VS Code:** `talenthub-gto/firebase/firestore.indexes.json`.

```json
{
  "indexes": [],
  "fieldOverrides": []
}
```

## 5. Publicar reglas, probar y guardar el avance

Después de crear `firebase/firestore.rules`, abre Firestore Database → **Rules**, pega **todo el contenido** y selecciona **Publish**. Puedes publicar las reglas manualmente sin instalar Firebase CLI. **Jamás dejes `allow read, write: if true`.**

La colección `jobs` estará vacía durante esta sesión. Es normal: las empresas publicarán vacantes en la sesión 03.

**Terminal A — comprobaciones y API PHP:**

```bash
npm run check:php
npm run test:api
npm run dev:api
```

**Terminal B — frontend:**

```bash
npm run dev:web
```

**Comprueba:**

- `http://127.0.0.1:8000/api/v1/health` devuelve HTTP 200.
- `http://127.0.0.1:8000/api/v1/jobs` devuelve `{"data":[]}` antes de crear las primeras vacantes.
- `http://localhost:3000` muestra el estado sin vacantes, **sin error de conexión**.

Si aparece `503`, revisa `apps/api/config/local.php` y el `projectId`. Si aparece `502`, verifica cURL, certificados HTTPS y conexión a internet. Si aparece `403`, revisa reglas publicadas, modo de la base y filtros Firestore.

```bash
git add .
git commit -m "feat: sesion 02 Firestore REST con PHP puro"
git switch develop
git merge --no-ff feature/sesion-02
```

**Evidencia:** proyecto Spark, reglas publicadas y consulta Firestore exitosa. No muestres tokens en capturas.

## 6. REFERENCIA: ¿dónde están los `package.json`? (NO MODIFICAR EN ESTA SESIÓN)

Estos dos archivos fueron creados en la **sesión 01** y permanecen en el repositorio. Se muestran completos aquí **solo para comprobar o recuperar sus contenidos**. No crees un tercer `package.json` dentro de `apps/api`, porque PHP no usa npm.

**Ruta 1: `talenthub-gto/package.json` (raíz)**

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

**Ruta 2: `talenthub-gto/apps/web/package.json` (frontend)**

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

`npm install` se ejecuta **en la raíz**; no tienes que ejecutar `npm install` en `apps/web` ni instalar los paquetes uno por uno.
