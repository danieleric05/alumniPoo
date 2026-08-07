<?php

namespace Formulair\Middleware;

use Formulair\Service\AuthService;
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

    public static function requireAdmin(): void
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            header('Location: /dashboard');
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        self::ensureSessionStarted();

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $user = R::load('users', (int) $_SESSION['user_id']);

        return $user->id && (int) $user->iRole === AuthService::ROLE_ADMIN;
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
