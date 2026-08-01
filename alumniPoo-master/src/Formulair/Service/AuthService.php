<?php

namespace Formulair\Service;

use Formulair\Model\Users;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class AuthService
{
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
}
