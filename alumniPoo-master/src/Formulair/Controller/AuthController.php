<?php

namespace Formulair\Controller;

use Formulair\Service\AuthService;
use RedBeanPHP\Facade as R;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(AuthService $authService = null)
    {
        parent::__construct();
        $this->authService = $authService ?? new AuthService();
    }

    public function login(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }

        return $this->view('auth/login');
    }

    private function handleLogin(): string
    {
        $sLogin = $this->getInput('sLogin');
        $sPassword = $this->getInput('sPassword');

        $validator = $this->validate(
            ['sLogin' => $sLogin, 'sPassword' => $sPassword],
            [
                'sLogin' => 'required|min:3',
                'sPassword' => 'required|min:6',
            ]
        );

        if (!$validator->validate([])) {
            return $this->view('auth/login', [
                'errors' => $validator->getErrors(),
                'sLogin' => $sLogin,
            ]);
        }

        $user = $this->authService->authenticate($sLogin, $sPassword);

        if ($user === null) {
            return $this->view('auth/login', [
                'error' => 'Invalid credentials',
                'sLogin' => $sLogin,
            ]);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_login'] = $user->sLogin;

        $this->logger->info("User logged in: {$user->sLogin}");
        $this->redirect('/dashboard');
        return '';
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->logger->info("User logged out");
        $this->redirect('/login');
    }

    public function register(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleRegister();
        }

        return $this->view('auth/register');
    }

    private function handleRegister(): string
    {
        $data = $this->getInput();

        $validator = $this->validate($data, [
            'sLogin' => 'required|min:3|max:50',
            'sPassword' => 'required|min:6',
            'FirstName' => 'required|alpha|max:100',
            'LastName' => 'required|alpha|max:100',
        ]);

        if (!$validator->validate([])) {
            return $this->view('auth/register', [
                'errors' => $validator->getErrors(),
                'data' => $data,
            ]);
        }

        if ($this->authService->userExists($data['sLogin'])) {
            return $this->view('auth/register', [
                'error' => 'Username already exists',
                'data' => $data,
            ]);
        }

        $user = $this->authService->register(
            $data['sLogin'],
            $data['sPassword'],
            $data['FirstName'],
            $data['LastName']
        );

        $this->logger->info("New user registered: {$user->sLogin}");

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_login'] = $user->sLogin;

        $this->redirect('/dashboard');
        return '';
    }
}
