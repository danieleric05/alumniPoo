<?php
$title = 'Informations de contact - Alumni CNDA';
$content = '
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">📱 Informations de contact</h1>
        <a href="/contact-info/add" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-2 px-6 rounded-lg transition transform hover:scale-105 duration-200">
            + Ajouter une information
        </a>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        ' . (empty($contactInfos) ? '<div class="p-8 text-center">
            <p class="text-gray-500 text-lg">Aucune information de contact enregistrée.</p>
            <p class="text-gray-400 mt-2">Ajoutez votre première information de contact.</p>
        </div>' : '<div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Valeur</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Date d\'ajout</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ' . implode('', array_map(function($info) {
                        return '<tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900 font-medium">' . htmlspecialchars($info['iType'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . htmlspecialchars($info['sValue'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . (!empty($info['dCreation']) ? date('d/m/Y H:i', strtotime($info['dCreation'])) : 'N/A') . '</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="/contact-info/delete/' . $info['id'] . '" onsubmit="return confirm(\'Êtes-vous sûr?\');" class="inline">
                                    ' . \Formulair\Core\Csrf::field() . '
                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-1 px-3 rounded text-xs transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>';
                    }, $contactInfos)) . '
                </tbody>
            </table>
        </div>') . '
        ' . \Formulair\Core\Paginator::renderNav($paginator, '/contact-info') . '
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
