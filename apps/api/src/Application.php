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

?>