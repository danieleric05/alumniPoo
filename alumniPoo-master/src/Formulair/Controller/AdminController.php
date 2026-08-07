<?php

namespace Formulair\Controller;

use Formulair\Service\AuthService;
use Formulair\Service\AlumniService;
use Formulair\Middleware\AuthMiddleware;

class AdminController extends BaseController
{
    private AuthService $authService;
    private AlumniService $alumniService;

    public function __construct(AuthService $authService = null, AlumniService $alumniService = null)
    {
        parent::__construct();
        $this->authService = $authService ?? new AuthService();
        $this->alumniService = $alumniService ?? new AlumniService();
    }

    public function users(): string
    {
        AuthMiddleware::requireAdmin();

        return $this->view('admin/users', [
            'users' => $this->authService->getAllUsers(),
            'currentUserId' => (int) $_SESSION['user_id'],
        ]);
    }

    public function updateRole(string $id): void
    {
        AuthMiddleware::requireAdmin();
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId !== (int) $_SESSION['user_id']) {
            $role = (int) $this->getInput('iRole') === AuthService::ROLE_ADMIN
                ? AuthService::ROLE_ADMIN
                : AuthService::ROLE_MEMBER;

            $this->authService->updateUserRole($userId, $role);
            $this->logger->info("User $userId role set to $role by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/users');
    }

    public function updateStatus(string $id): void
    {
        AuthMiddleware::requireAdmin();
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId !== (int) $_SESSION['user_id']) {
            $status = (int) $this->getInput('iStatus') === 1 ? 1 : 0;

            $this->authService->updateUserStatus($userId, $status);
            $this->logger->info("User $userId status set to $status by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/users');
    }

    public function updateMembership(string $id): void
    {
        AuthMiddleware::requireAdmin();
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = $this->validate($this->getInput(), ['dCotisationValidUntil' => 'date']);

            if (empty($validator->getErrors())) {
                $date = $this->getInput('dCotisationValidUntil');
                $this->alumniService->updateMembershipExpiry($userId, $date !== '' ? $date : null);
                $this->logger->info("User $userId membership expiry updated by admin {$_SESSION['user_id']}");
            }
        }

        $this->redirect('/admin/users');
    }

    public function editUser(string $id): string
    {
        AuthMiddleware::requireAdmin();
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEditUser($userId);
        }

        $user = $this->alumniService->getUserProfile($userId);

        if ($user === null || !$user->id) {
            http_response_code(404);
            $this->redirect('/admin/users');
            return '';
        }

        return $this->view('admin/edit_user', ['user' => $user]);
    }

    private function handleEditUser(int $userId): string
    {
        $data = $this->getInput();

        $validator = $this->validate($data, [
            'FirstName' => 'required|alpha|max:191',
            'LastName' => 'required|alpha|max:191',
            'iCity' => 'numeric',
            'YearWouldGraduateIn' => 'numeric|min:1900|max:2100',
            'iYearStart' => 'numeric|min:1990|max:2000',
            'iYearEnd' => 'numeric|min:1990|max:2000',
        ]);

        if (!empty($validator->getErrors())) {
            $user = $this->alumniService->getUserProfile($userId);

            if ($user === null || !$user->id) {
                http_response_code(404);
                $this->redirect('/admin/users');
                return '';
            }

            return $this->view('admin/edit_user', [
                'user' => $user,
                'errors' => $validator->getErrors(),
            ]);
        }

        $user = $this->alumniService->updateUserProfile($userId, $data);
        $this->logger->info("User {$user->sLogin} profile updated by admin {$_SESSION['user_id']}");

        $this->redirect('/admin/users');
        return '';
    }

    public function deleteUser(string $id): void
    {
        AuthMiddleware::requireAdmin();
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId !== (int) $_SESSION['user_id']) {
            $this->authService->deleteUser($userId);
            $this->logger->info("User $userId deleted by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/users');
    }
}
