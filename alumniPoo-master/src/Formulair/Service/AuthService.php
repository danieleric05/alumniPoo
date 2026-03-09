<?php

namespace Formulair\Service;

use Formulair\Model\Users;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class AuthService
{
    public function authenticate(string $sLogin, string $sPassword): ?OODBBean
    {
        $user = R::findOne('users', 'sLogin = ?', [$sLogin]);

        if ($user === null) {
            return null;
        }

        // Export user data to access password (RedBean magic getter may return null)
        $userData = $user->export();
        $passwordHash = $userData['sPassword'] ?? null;

        // Check if password is null or empty
        if (empty($passwordHash)) {
            return null;
        }

        if (!password_verify($sPassword, $passwordHash)) {
            return null;
        }

        return $user;
    }

    public function register(string $sLogin, string $sPassword, string $FirstName, string $LastName): OODBBean
    {
        $hashedPassword = password_hash($sPassword, PASSWORD_BCRYPT);
        $dCreation = date('Y-m-d H:i:s');

        // Use direct SQL to avoid RedBean's snake_case conversion
        R::exec(
            'INSERT INTO users (sLogin, sPassword, FirstName, LastName, dCreation, iStatus) VALUES (?, ?, ?, ?, ?, ?)',
            [$sLogin, $hashedPassword, $FirstName, $LastName, $dCreation, 1]
        );

        $userId = R::getInsertID();

        return R::load('users', $userId);
    }

    public function userExists(string $sLogin): bool
    {
        return R::findOne('users', 'sLogin = ?', [$sLogin]) !== null;
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
}
