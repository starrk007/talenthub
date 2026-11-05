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

?>