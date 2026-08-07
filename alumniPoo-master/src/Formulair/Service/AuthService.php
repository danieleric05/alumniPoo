<?php

namespace Formulair\Service;

use Formulair\Model\Users;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class AuthService
{
    public const ROLE_MEMBER = 0;
    public const ROLE_ADMIN = 1;

    private const MAX_ATTEMPTS = 5;
    private const WINDOW_MINUTES = 15;

    public function isRateLimited(string $sLogin, string $ip): bool
    {
        $since = date('Y-m-d H:i:s', time() - self::WINDOW_MINUTES * 60);

        $count = R::count('login_attempt', 'd_attempt > ? AND (s_login = ? OR s_ip = ?)', [$since, $sLogin, $ip]);

        return $count >= self::MAX_ATTEMPTS;
    }

    public function recordFailedAttempt(string $sLogin, string $ip): void
    {
        $attempt = R::dispense('login_attempt');
        $attempt->sLogin = $sLogin;
        $attempt->sIp = $ip;
        $attempt->dAttempt = date('Y-m-d H:i:s');

        R::store($attempt);
    }

    public function clearFailedAttempts(string $sLogin, string $ip): void
    {
        $attempts = R::find('login_attempt', 's_login = ? OR s_ip = ?', [$sLogin, $ip]);
        R::trashAll($attempts);
    }

    public function authenticate(string $sLogin, string $sPassword): ?OODBBean
    {
        $user = R::findOne('users', 's_login = ?', [$sLogin]);

        if ($user === null) {
            return null;
        }

        $passwordHash = $user->sPassword;

        // Check if password is null or empty
        if (empty($passwordHash)) {
            return null;
        }

        if (!password_verify($sPassword, $passwordHash)) {
            return null;
        }

        if ((int) $user->iStatus !== 1) {
            return null;
        }

        return $user;
    }

    public function register(string $sLogin, string $sPassword, string $FirstName, string $LastName): OODBBean
    {
        $user = R::dispense('users');
        $user->sLogin = $sLogin;
        $user->sPassword = password_hash($sPassword, PASSWORD_BCRYPT);
        $user->FirstName = $FirstName;
        $user->LastName = $LastName;
        $user->dCreation = date('Y-m-d H:i:s');
        $user->iStatus = 1;
        $user->iRole = self::ROLE_MEMBER;

        R::store($user);

        return $user;
    }

    public function userExists(string $sLogin): bool
    {
        return R::findOne('users', 's_login = ?', [$sLogin]) !== null;
    }

    public function getUserById(int $id): ?OODBBean
    {
        return R::load('users', $id);
    }

    public function updateUser(OODBBean $user): OODBBean
    {
        R::store($user);
        return $user;
    }

    public function isAdmin(OODBBean $user): bool
    {
        return (int) $user->iRole === self::ROLE_ADMIN;
    }

    public function getAllUsers(): array
    {
        return R::findAll('users', 'ORDER BY s_login');
    }

    public function updateUserRole(int $id, int $role): ?OODBBean
    {
        $user = R::load('users', $id);

        if (!$user->id) {
            return null;
        }

        $user->iRole = $role === self::ROLE_ADMIN ? self::ROLE_ADMIN : self::ROLE_MEMBER;
        R::store($user);

        return $user;
    }

    public function updateUserStatus(int $id, int $status): ?OODBBean
    {
        $user = R::load('users', $id);

        if (!$user->id) {
            return null;
        }

        $user->iStatus = $status === 1 ? 1 : 0;
        R::store($user);

        return $user;
    }

    public function deleteUser(int $id): bool
    {
        $user = R::load('users', $id);

        if (!$user->id) {
            return false;
        }

        R::trashAll(R::find('user_work_experience', 'i_user = ?', [$id]));
        R::trashAll(R::find('user_contact_info', 'i_user = ?', [$id]));
        R::trash($user);

        return true;
    }
}
