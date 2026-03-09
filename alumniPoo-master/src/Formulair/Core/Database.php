<?php

namespace Formulair\Core;

use RedBeanPHP\Facade as R;

class Database
{
    private static ?Database $instance = null;
    private static bool $initialized = false;

    private function __construct()
    {
        if (self::$initialized) {
            return;
        }

        Environment::load();

        $dbType = Environment::get('DB_TYPE', 'mysql');
        $dbName = Environment::get('DB_NAME', 'cnda');

        try {
            if ($dbType === 'sqlite') {
                // SQLite DSN - fichier local dans le répertoire du projet
                $dbPath = getcwd() . '/' . $dbName;
                @mkdir(dirname($dbPath), 0755, true);
                $dsn = 'sqlite:' . $dbPath;
                R::setup($dsn);
            } else {
                // MySQL/MariaDB DSN
                $dbHost = Environment::get('DB_HOST', 'localhost');
                $dbPort = Environment::get('DB_PORT', 3306);
                $dbUser = Environment::get('DB_USER', 'root');
                $dbPassword = Environment::get('DB_PASSWORD', '');

                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s',
                    $dbType,
                    $dbHost,
                    $dbPort,
                    $dbName
                );

                R::setup($dsn, $dbUser, $dbPassword);
            }

            // RedBean configuration
            $debug = Environment::get('APP_DEBUG', 'false') === 'true';
            if ($debug) {
                R::debug(true);
            }

            $freeze = Environment::get('REDBEAN_FREEZE', 'false') === 'true';
            R::freeze($freeze);

            self::$initialized = true;
        } catch (\Exception $e) {
            $msg = 'Database connection failed: ' . $e->getMessage();
            if (Environment::get('APP_DEBUG') === 'true') {
                $msg .= ' (Type: ' . $dbType . ', Path: ' . ($dbPath ?? 'N/A') . ')';
            }
            throw new \RuntimeException($msg);
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function init(): void
    {
        self::getInstance();
    }

    public static function close(): void
    {
        R::close();
        self::$initialized = false;
    }
}
