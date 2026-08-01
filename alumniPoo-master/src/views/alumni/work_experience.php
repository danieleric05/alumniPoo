<?php
$title = 'Expérience professionnelle - Alumni CNDA';
$content = '
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">💼 Expérience professionnelle</h1>
        <a href="/work-experience/add" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-2 px-6 rounded-lg transition transform hover:scale-105 duration-200">
            + Ajouter une expérience
        </a>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        ' . (empty($workExperiences) ? '<div class="p-8 text-center">
            <p class="text-gray-500 text-lg">Aucune expérience professionnelle enregistrée.</p>
            <p class="text-gray-400 mt-2">Commencez par ajouter votre première expérience.</p>
        </div>' : '<div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Entreprise</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Division</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Ville</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Début</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Fin</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ' . implode('', array_map(function($exp) {
                        return '<tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900 font-medium">' . htmlspecialchars($exp['sCompany'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . htmlspecialchars($exp['iDivision'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . htmlspecialchars($exp['sCity'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . (!empty($exp['dStart']) ? date('d/m/Y', strtotime($exp['dStart'])) : 'N/A') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . htmlspecialchars($exp['iEnd'] ?? 'Toujours') . '</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="/work-experience/edit/' . $exp['id'] . '" class="bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold py-1 px-3 rounded text-xs transition">
                                        Éditer
                                    </a>
                                    <form method="POST" action="/work-experience/delete/' . $exp['id'] . '" onsubmit="return confirm(\'Êtes-vous sûr?\');" class="inline">
                                        ' . \Formulair\Core\Csrf::field() . '
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-1 px-3 rounded text-xs transition">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>';
                    }, $workExperiences)) . '
                </tbody>
            </table>
        </div>') . '
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
