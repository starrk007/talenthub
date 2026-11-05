<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
if (!class_exists(\TalentHub\Application::class)) {
    fwrite(STDERR, "No se pudo cargar la aplicación PHP.\n");
    exit(1);
}
echo "Smoke PHP sesión 01 OK\n";
