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

?>