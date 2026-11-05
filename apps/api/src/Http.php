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