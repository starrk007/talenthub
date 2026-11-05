<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
use TalentHub\Config;
use TalentHub\Validator;
use TalentHub\ApiException;
assert_options(ASSERT_ACTIVE, 1);
assert(Config::required('firebase_project_id') === 'talenthub-aars');
assert(Validator::documentId('abcdefgh1234') === 'abcdefgh1234');
assert(Validator::text(['title' => ' Nuevo empleo '], 'title') === 'Nuevo empleo');
$threw = false;
try { Validator::documentId('../private'); } catch (ApiException) { $threw = true; }
if (!$threw) { fwrite(STDERR, "ERROR: identificador inválido permitido\n"); exit(1); }
echo "Smoke PHP OK\n";

?>