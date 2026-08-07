<?php

namespace Formulair\Middleware;

use Formulair\Service\PermissionService;
use RedBeanPHP\Facade as R;

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

    public static function requirePermission(string $key): void
    {
        self::requireLogin();

        if (!self::hasPermission($key)) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function hasPermission(string $key): bool
    {
        self::ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        return (new PermissionService())->userHasPermission((int) $_SESSION['user_id'], $key);
    }

    public static function isMembershipActive(): bool
    {
        self::ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $user = R::load('users', (int) $_SESSION['user_id']);

        return $user->id && !empty($user->dCotisationValidUntil) && $user->dCotisationValidUntil >= date('Y-m-d');
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
