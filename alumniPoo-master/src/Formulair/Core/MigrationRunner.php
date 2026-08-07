<?php

namespace Formulair\Core;

use RedBeanPHP\Facade as R;

class MigrationRunner
{
    private string $migrationsDir;

    public function __construct(?string $migrationsDir = null)
    {
        $this->migrationsDir = $migrationsDir ?? dirname(__DIR__, 3) . '/migrations';
    }

    public function run(): array
    {
        $this->ensureMigrationsTable();

        $applied = R::getCol('SELECT filename FROM schema_migrations');
        $pending = array_diff($this->migrationFiles(), $applied);
        sort($pending);

        $ran = [];

        foreach ($pending as $filename) {
            $sql = file_get_contents($this->migrationsDir . '/' . $filename);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            R::begin();

            try {
                foreach ($statements as $statement) {
                    R::exec($statement);
                }

                R::exec(
                    'INSERT INTO schema_migrations (filename, applied_at) VALUES (?, ?)',
                    [$filename, date('Y-m-d H:i:s')]
                );

                R::commit();
                $ran[] = $filename;
            } catch (\Exception $e) {
                R::rollback();
                throw new \RuntimeException("Migration $filename failed: " . $e->getMessage(), 0, $e);
            }
        }

        return $ran;
    }

    private function ensureMigrationsTable(): void
    {
        R::exec('CREATE TABLE IF NOT EXISTS schema_migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(191) UNIQUE,
            applied_at DATETIME
        )');
    }

    private function migrationFiles(): array
    {
        $files = glob($this->migrationsDir . '/*.sql');

        return array_map('basename', $files ?: []);
    }
}
