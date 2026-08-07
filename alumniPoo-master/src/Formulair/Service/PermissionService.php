<?php

namespace Formulair\Service;

use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class PermissionService
{
    public const PROTECTED_TEMPLATE_KEYS = ['admin', 'membre'];

    public function getAllPermissions(): array
    {
        return R::findAll('permission', 'ORDER BY s_key');
    }

    public function getAllRoleTemplates(): array
    {
        return R::findAll('role_template', 'ORDER BY s_label');
    }

    public function getTemplateByKey(string $key): ?OODBBean
    {
        return R::findOne('role_template', 's_key = ?', [$key]);
    }

    public function getTemplatePermissionKeys(int $templateId): array
    {
        return R::getCol(
            'SELECT permission.s_key
             FROM role_template_permission
             JOIN permission ON permission.id = role_template_permission.i_permission
             WHERE role_template_permission.i_role_template = ?',
            [$templateId]
        );
    }

    public function userHasPermission(int $userId, string $key): bool
    {
        $count = R::getCell(
            'SELECT COUNT(*)
             FROM users
             JOIN role_template_permission ON role_template_permission.i_role_template = users.i_role_template
             JOIN permission ON permission.id = role_template_permission.i_permission
             WHERE users.id = ? AND permission.s_key = ?',
            [$userId, $key]
        );

        return (int) $count > 0;
    }

    public function assignTemplateToUser(int $userId, int $templateId): ?OODBBean
    {
        $user = R::load('users', $userId);
        $template = R::load('role_template', $templateId);

        if (!$user->id || !$template->id) {
            return null;
        }

        $user->iRoleTemplate = $templateId;
        R::store($user);

        return $user;
    }

    public function createRoleTemplate(string $key, string $label, array $permissionIds): OODBBean
    {
        $template = R::dispense('role_template');
        $template->sKey = $key;
        $template->sLabel = $label;
        R::store($template);

        $this->updateRoleTemplatePermissions((int) $template->id, $permissionIds);

        return $template;
    }

    public function updateRoleTemplatePermissions(int $templateId, array $permissionIds): void
    {
        R::exec('DELETE FROM role_template_permission WHERE i_role_template = ?', [$templateId]);

        foreach ($permissionIds as $permissionId) {
            $link = R::dispense('role_template_permission');
            $link->iRoleTemplate = $templateId;
            $link->iPermission = (int) $permissionId;
            R::store($link);
        }
    }

    public function deleteRoleTemplate(int $templateId): bool
    {
        $template = R::load('role_template', $templateId);

        if (!$template->id || in_array($template->sKey, self::PROTECTED_TEMPLATE_KEYS, true)) {
            return false;
        }

        $usersAssigned = R::count('users', 'i_role_template = ?', [$templateId]);

        if ($usersAssigned > 0) {
            return false;
        }

        R::exec('DELETE FROM role_template_permission WHERE i_role_template = ?', [$templateId]);
        R::trash($template);

        return true;
    }
}
