<?php
$title = 'Administration - Alumni CNDA';
$content = '
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">🛡️ Gestion des utilisateurs</h1>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Template de droits</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Statut</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Cotisation</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ' . implode('', array_map(function($u) use ($currentUserId, $roleTemplates) {
                        $isSelf = (int) $u->id === $currentUserId;
                        $isActive = (int) $u->iStatus === 1;
                        $canManageRoles = \Formulair\Middleware\AuthMiddleware::hasPermission('roles.manage');

                        $currentTemplateLabel = 'Aucun';
                        foreach ($roleTemplates as $template) {
                            if ((int) $template->id === (int) $u->iRoleTemplate) {
                                $currentTemplateLabel = $template->sLabel;
                                break;
                            }
                        }

                        $templateBadge = '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">' . htmlspecialchars($currentTemplateLabel) . '</span>';

                        $statusBadge = $isActive
                            ? '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Actif</span>'
                            : '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Désactivé</span>';

                        $isMembershipActive = !empty($u->dCotisationValidUntil) && $u->dCotisationValidUntil >= date('Y-m-d');
                        $membershipBadge = !empty($u->dCotisationValidUntil)
                            ? ($isMembershipActive
                                ? '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">À jour jusqu\'au ' . date('d/m/Y', strtotime($u->dCotisationValidUntil)) . '</span>'
                                : '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Expirée le ' . date('d/m/Y', strtotime($u->dCotisationValidUntil)) . '</span>')
                            : '<span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Non renseignée</span>';

                        $membershipForm = \Formulair\Middleware\AuthMiddleware::hasPermission('users.manage_membership') ? '
                            <form method="POST" action="/admin/users/' . $u->id . '/membership" class="flex items-center gap-2 mt-2">
                                ' . \Formulair\Core\Csrf::field() . '
                                <input type="date" name="dCotisationValidUntil" value="' . htmlspecialchars($u->dCotisationValidUntil ?? '') . '" class="text-xs border border-gray-300 rounded px-2 py-1">
                                <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold py-1 px-3 rounded text-xs transition">
                                    Mettre à jour
                                </button>
                            </form>
                        ' : '';

                        $templateCell = $templateBadge;
                        if (!$isSelf && $canManageRoles) {
                            $templateCell .= '
                                <form method="POST" action="/admin/users/' . $u->id . '/role" class="flex items-center gap-2 mt-2">
                                    ' . \Formulair\Core\Csrf::field() . '
                                    <select name="iRoleTemplate" class="text-xs border border-gray-300 rounded px-2 py-1">
                                        ' . implode('', array_map(function($template) use ($u) {
                                            $selected = (int) $template->id === (int) $u->iRoleTemplate ? ' selected' : '';
                                            return '<option value="' . $template->id . '"' . $selected . '>' . htmlspecialchars($template->sLabel) . '</option>';
                                        }, $roleTemplates)) . '
                                    </select>
                                    <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold py-1 px-3 rounded text-xs transition">
                                        Assigner
                                    </button>
                                </form>
                            ';
                        }

                        $actionButtons = '';
                        if (!$isSelf) {
                            if (\Formulair\Middleware\AuthMiddleware::hasPermission('users.manage_status')) {
                                $actionButtons .= '
                                    <form method="POST" action="/admin/users/' . $u->id . '/status" class="inline">
                                        ' . \Formulair\Core\Csrf::field() . '
                                        <input type="hidden" name="iStatus" value="' . ($isActive ? '0' : '1') . '">
                                        <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold py-1 px-3 rounded text-xs transition">
                                            ' . ($isActive ? 'Désactiver' : 'Activer') . '
                                        </button>
                                    </form>
                                ';
                            }
                            if (\Formulair\Middleware\AuthMiddleware::hasPermission('users.edit')) {
                                $actionButtons .= '
                                    <a href="/admin/users/' . $u->id . '/edit" class="bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-1 px-3 rounded text-xs transition">
                                        Modifier
                                    </a>
                                ';
                            }
                            if (\Formulair\Middleware\AuthMiddleware::hasPermission('users.delete')) {
                                $actionButtons .= '
                                    <form method="POST" action="/admin/users/' . $u->id . '/delete" onsubmit="return confirm(\'Supprimer définitivement ce compte et toutes ses données ?\');" class="inline">
                                        ' . \Formulair\Core\Csrf::field() . '
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-1 px-3 rounded text-xs transition">
                                            Supprimer
                                        </button>
                                    </form>
                                ';
                            }
                        }

                        $actions = $isSelf
                            ? '<span class="text-gray-400 text-xs italic">Votre compte</span>'
                            : ($actionButtons !== '' ? '<div class="flex flex-wrap items-center gap-2">' . $actionButtons . '</div>' : '<span class="text-gray-400 text-xs italic">Aucune action disponible</span>');

                        return '<tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">' . htmlspecialchars($u->FirstName . ' ' . $u->LastName) . '</div>
                                <div class="text-gray-500 text-sm">' . htmlspecialchars($u->sLogin) . '</div>
                            </td>
                            <td class="px-6 py-4">' . $templateCell . '</td>
                            <td class="px-6 py-4">' . $statusBadge . '</td>
                            <td class="px-6 py-4">' . $membershipBadge . $membershipForm . '</td>
                            <td class="px-6 py-4">' . $actions . '</td>
                        </tr>';
                    }, $users)) . '
                </tbody>
            </table>
        </div>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
