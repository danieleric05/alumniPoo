<?php
$title = 'Droits - Alumni CNDA';
$content = '
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">🔑 Templates de droits</h1>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        ' . implode('', array_map(function($template) use ($permissions, $templatePermissions, $protectedKeys) {
            $isProtected = in_array($template->sKey, $protectedKeys, true);
            $activeKeys = $templatePermissions[$template->id] ?? [];

            $checkboxes = implode('', array_map(function($permission) use ($activeKeys) {
                $checked = in_array($permission->sKey, $activeKeys, true) ? ' checked' : '';
                return '
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="permissions[]" value="' . $permission->id . '"' . $checked . '>
                        ' . htmlspecialchars($permission->sLabel) . '
                    </label>
                ';
            }, $permissions));

            $deleteButton = $isProtected ? '<span class="text-gray-400 text-xs italic">Template protégé</span>' : '
                <form method="POST" action="/admin/roles/' . $template->id . '/delete" onsubmit="return confirm(\'Supprimer ce template de droits ?\');" class="inline">
                    ' . \Formulair\Core\Csrf::field() . '
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-1 px-3 rounded text-xs transition">
                        Supprimer
                    </button>
                </form>
            ';

            return '
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white">' . htmlspecialchars($template->sLabel) . '</h2>
                        <span class="text-xs text-slate-300">' . htmlspecialchars($template->sKey) . '</span>
                    </div>
                    <form method="POST" action="/admin/roles/' . $template->id . '" class="p-6 space-y-3">
                        ' . \Formulair\Core\Csrf::field() . '
                        ' . $checkboxes . '
                        <div class="flex items-center justify-between pt-3">
                            <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold py-1 px-3 rounded text-xs transition">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                    <div class="px-6 pb-6">' . $deleteButton . '</div>
                </div>
            ';
        }, $templates)) . '
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
            <h2 class="text-lg font-bold text-white">➕ Créer un template</h2>
        </div>
        <form method="POST" action="/admin/roles" class="p-6 space-y-4">
            ' . \Formulair\Core\Csrf::field() . '
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="sKey" class="block text-sm font-medium text-gray-700 mb-2">Clé technique</label>
                    <input type="text" id="sKey" name="sKey" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="tresorier">
                </div>
                <div>
                    <label for="sLabel" class="block text-sm font-medium text-gray-700 mb-2">Libellé</label>
                    <input type="text" id="sLabel" name="sLabel" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" placeholder="Trésorier">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-2">
                ' . implode('', array_map(function($permission) {
                    return '
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="permissions[]" value="' . $permission->id . '">
                            ' . htmlspecialchars($permission->sLabel) . '
                        </label>
                    ';
                }, $permissions)) . '
            </div>
            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition">
                Créer le template
            </button>
        </form>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
