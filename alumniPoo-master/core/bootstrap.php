<?php

require_once __DIR__.'/../vendor/autoload.php';

use Formulair\Core\Environment;
use Formulair\Core\Database;
use Formulair\Core\DbSessionHandler;
use RedBeanPHP\Facade as R;

// Load environment configuration
$projectRoot = dirname(dirname(__FILE__));
Environment::load($projectRoot . '/.env');

// Set error reporting
error_reporting(E_ALL);
if (Environment::get('APP_DEBUG', 'false') === 'true') {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set(Environment::get('APP_TIMEZONE', 'UTC'));

// Initialize database connection
try {
    Database::init();
} catch (\Exception $e) {
    die('Fatal Error: ' . $e->getMessage());
}

// Store sessions in the database rather than the local filesystem: the app
// runs behind a multi-threaded server (FrankenPHP) where file-based sessions
// written by one thread are not reliably visible to the next.
session_set_save_handler(new DbSessionHandler(R::getPDO()), true);
