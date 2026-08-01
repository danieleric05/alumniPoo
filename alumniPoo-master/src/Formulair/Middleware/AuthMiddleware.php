<?php

namespace Formulair\Middleware;

class AuthMiddleware
{
    private static function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireLogin(): void
    {
        self::ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireLogout(): void
    {
        self::ensureSessionStarted();

        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function isLoggedIn(): bool
    {
        self::ensureSessionStarted();
        return isset($_SESSION['user_id']);
    }

    public static function getCurrentUserId(): ?int
    {
        self::ensureSessionStarted();
        return $_SESSION['user_id'] ?? null;
    }

    public static function logout(): void
    {
        self::ensureSessionStarted();
        session_destroy();
    }
}
