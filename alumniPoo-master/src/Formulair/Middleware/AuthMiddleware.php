<?php

namespace Formulair\Middleware;

class AuthMiddleware
{
    public static function requireLogin(): void
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireLogout(): void
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function isLoggedIn(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public static function getCurrentUserId(): ?int
    {
        session_start();
        return $_SESSION['user_id'] ?? null;
    }

    public static function logout(): void
    {
        session_start();
        session_destroy();
    }
}
