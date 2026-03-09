<?php

require_once __DIR__.'/../core/bootstrap.php';

use RedBeanPHP\Facade as R;
/**
 * @ $users \Formulair\Model\Users
 */
$users = R::dispense('users');
$users->slogin ='admin';

R::store($users);
