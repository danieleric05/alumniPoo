<?php

require_once __DIR__ . '/../../../core/bootstrap.php';

use RedbeanPHP\Facade as R;
use Formulair\DataFixtures\UsersFixtures;

$className = $argv[1];
//var_dump($argv);
$classFixtures = sprintf('\\Formulair\\DataFixtures\\%sFixtures', ucfirst($className));
$objectFixtures = new $classFixtures();

$type = $objectFixtures->getType();
$fixtures = $objectFixtures->getFixtures();

foreach ($fixtures as $fixture){
  $users = R::dispense($type);
  foreach ($fixture as $property => $value){
    $users->$property = $value;
  }
  R::store($users);
}
