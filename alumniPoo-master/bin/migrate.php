<?php

require_once __DIR__ . '/../core/bootstrap.php';

$ran = (new \Formulair\Core\MigrationRunner())->run();

echo $ran
    ? "✓ Migrations appliquées : " . implode(', ', $ran) . "\n"
    : "Aucune migration en attente\n";
