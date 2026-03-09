<?php

namespace Formulair\Core;

class Environment
{
    private static array $env = [];
    private static bool $loaded = false;

    public static function load(string $envFile = null): void
    {
        if (self::$loaded) {
            return;
        }

        if ($envFile === null) {
            $envFile = dirname(__DIR__) . '/.env';
        }

        if (file_exists($envFile)) {
            self::parseEnvFile($envFile);
        }

        // Load environment variables from system
        foreach ($_ENV as $key => $value) {
            self::$env[$key] = $value;
        }
        foreach ($_SERVER as $key => $value) {
            if (is_string($value)) {
                self::$env[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    private static function parseEnvFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }

                self::$env[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        return self::$env[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::$env[$key] = $value;
    }

    public static function all(): array
    {
        self::load();
        return self::$env;
    }
}
