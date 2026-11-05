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

?>