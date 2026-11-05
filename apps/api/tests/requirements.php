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