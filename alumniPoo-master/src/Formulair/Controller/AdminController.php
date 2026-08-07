<?php

namespace Formulair\Controller;

use Formulair\Service\AuthService;
use Formulair\Service\AlumniService;
use Formulair\Service\PermissionService;
use Formulair\Middleware\AuthMiddleware;

class AdminController extends BaseController
{
    private AuthService $authService;
    private AlumniService $alumniService;
    private PermissionService $permissionService;

    public function __construct(AuthService $authService = null, AlumniService $alumniService = null, PermissionService $permissionService = null)
    {
        parent::__construct();
        $this->authService = $authService ?? new AuthService();
        $this->alumniService = $alumniService ?? new AlumniService();
        $this->permissionService = $permissionService ?? new PermissionService();
    }

    public function users(): string
    {
        AuthMiddleware::requirePermission('users.view');

        return $this->view('admin/users', [
            'users' => $this->authService->getAllUsers(),
            'roleTemplates' => $this->permissionService->getAllRoleTemplates(),
            'currentUserId' => (int) $_SESSION['user_id'],
        ]);
    }

    public function updateRoleTemplate(string $id): void
    {
        AuthMiddleware::requirePermission('roles.manage');
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId !== (int) $_SESSION['user_id']) {
            $templateId = (int) $this->getInput('iRoleTemplate');
            $this->permissionService->assignTemplateToUser($userId, $templateId);
            $this->logger->info("User $userId assigned to role template $templateId by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/users');
    }

    public function updateStatus(string $id): void
    {
        AuthMiddleware::requirePermission('users.manage_status');
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
        AuthMiddleware::requirePermission('users.manage_membership');
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
        AuthMiddleware::requirePermission('users.edit');
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
        AuthMiddleware::requirePermission('users.delete');
        $userId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId !== (int) $_SESSION['user_id']) {
            $this->authService->deleteUser($userId);
            $this->logger->info("User $userId deleted by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/users');
    }

    public function roles(): string
    {
        AuthMiddleware::requirePermission('roles.manage');

        $templates = $this->permissionService->getAllRoleTemplates();
        $templatePermissions = [];

        foreach ($templates as $template) {
            $templatePermissions[$template->id] = $this->permissionService->getTemplatePermissionKeys((int) $template->id);
        }

        return $this->view('admin/roles', [
            'permissions' => $this->permissionService->getAllPermissions(),
            'templates' => $templates,
            'templatePermissions' => $templatePermissions,
            'protectedKeys' => PermissionService::PROTECTED_TEMPLATE_KEYS,
        ]);
    }

    public function createRoleTemplate(): void
    {
        AuthMiddleware::requirePermission('roles.manage');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validator = $this->validate($this->getInput(), [
                'sKey' => 'required|alphanumeric|max:191',
                'sLabel' => 'required|max:191',
            ]);

            if (empty($validator->getErrors())) {
                $permissionIds = array_map('intval', (array) $this->getInput('permissions', []));
                $this->permissionService->createRoleTemplate(
                    $this->getInput('sKey'),
                    $this->getInput('sLabel'),
                    $permissionIds
                );
                $this->logger->info("Role template {$this->getInput('sKey')} created by admin {$_SESSION['user_id']}");
            }
        }

        $this->redirect('/admin/roles');
    }

    public function updateRoleTemplatePermissions(string $id): void
    {
        AuthMiddleware::requirePermission('roles.manage');
        $templateId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $permissionIds = array_map('intval', (array) $this->getInput('permissions', []));
            $this->permissionService->updateRoleTemplatePermissions($templateId, $permissionIds);
            $this->logger->info("Role template $templateId permissions updated by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/roles');
    }

    public function deleteRoleTemplate(string $id): void
    {
        AuthMiddleware::requirePermission('roles.manage');
        $templateId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->permissionService->deleteRoleTemplate($templateId);
            $this->logger->info("Role template $templateId delete attempted by admin {$_SESSION['user_id']}");
        }

        $this->redirect('/admin/roles');
    }
}
