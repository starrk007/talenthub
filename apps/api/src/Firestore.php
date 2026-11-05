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

?>