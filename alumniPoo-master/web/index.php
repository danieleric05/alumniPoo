<?php

require_once __DIR__.'/../core/bootstrap.php';

use Formulair\Core\Router;
use Formulair\Core\Logger;
use Formulair\Controller\AuthController;
use Formulair\Controller\AlumniController;
use Formulair\Middleware\AuthMiddleware;

$logger = new Logger();
$router = new Router($logger);

// Authentication routes
$authController = new AuthController();
$router->get('/login', fn($params) => $authController->login());
$router->post('/login', fn($params) => $authController->login());
$router->get('/register', fn($params) => $authController->register());
$router->post('/register', fn($params) => $authController->register());
$router->get('/logout', fn($params) => $authController->logout());

// Alumni routes (protected)
$alumniController = new AlumniController();
$router->get('/dashboard', fn($params) => $alumniController->dashboard());
$router->get('/profile', fn($params) => $alumniController->editProfile());
$router->post('/profile', fn($params) => $alumniController->editProfile());

$router->get('/work-experience', fn($params) => $alumniController->workExperience());
$router->get('/work-experience/add', fn($params) => $alumniController->addWorkExperience());
$router->post('/work-experience/add', fn($params) => $alumniController->addWorkExperience());
$router->get('/work-experience/edit/{id}', fn($params) => $alumniController->editWorkExperience($params['id']));
$router->post('/work-experience/edit/{id}', fn($params) => $alumniController->editWorkExperience($params['id']));
$router->post('/work-experience/delete/{id}', fn($params) => $alumniController->deleteWorkExperience($params['id']));

$router->get('/contact-info', fn($params) => $alumniController->contactInfo());
$router->get('/contact-info/add', fn($params) => $alumniController->addContactInfo());
$router->post('/contact-info/add', fn($params) => $alumniController->addContactInfo());
$router->post('/contact-info/delete/{id}', fn($params) => $alumniController->deleteContactInfo($params['id']));

// Home route
$router->get('/', function($params) {
    if (AuthMiddleware::isLoggedIn()) {
        header('Location: /dashboard');
    } else {
        header('Location: /login');
    }
    exit;
});

// Dispatch the request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo $router->dispatch($method, $path);
