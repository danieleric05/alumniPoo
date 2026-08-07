<?php
$title = htmlspecialchars(($member->FirstName ?? '') . ' ' . ($member->LastName ?? '')) . ' - Annuaire - Alumni CNDA';
$content = '
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">' . htmlspecialchars($member->FirstName ?? '') . ' ' . htmlspecialchars($member->LastName ?? '') . '</h1>
        <a href="/directory" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
            ← Retour à l\'annuaire
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
            <h2 class="text-xl font-bold text-white">📋 Profil</h2>
        </div>
        <div class="p-6 grid md:grid-cols-2 gap-6">
            ' . ($member->iYearStart || $member->iYearEnd ? '<div>
                <p class="text-gray-600 text-sm font-semibold">Promotion</p>
                <p class="text-xl font-bold text-gray-900 mt-1">' . htmlspecialchars($member->iYearStart ?? '?') . ' - ' . htmlspecialchars($member->iYearEnd ?? '?') . '</p>
            </div>' : '') . '
            ' . ($member->YearWouldGraduateIn ? '<div>
                <p class="text-gray-600 text-sm font-semibold">Année de graduation</p>
                <p class="text-xl font-bold text-gray-900 mt-1">' . htmlspecialchars($member->YearWouldGraduateIn) . '</p>
            </div>' : '') . '
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
            <h2 class="text-xl font-bold text-white">💼 Expérience professionnelle</h2>
        </div>
        ' . (empty($experiences) ? '<div class="p-8 text-center">
            <p class="text-gray-500 text-lg">Aucune expérience professionnelle renseignée.</p>
        </div>' : '<div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Entreprise</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Ville</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Début</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Fin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ' . implode('', array_map(function($exp) {
                        return '<tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900 font-medium">' . htmlspecialchars($exp['sCompany'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . htmlspecialchars($exp['sCity'] ?? '') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . (!empty($exp['dStart']) ? date('d/m/Y', strtotime($exp['dStart'])) : 'N/A') . '</td>
                            <td class="px-6 py-4 text-gray-600">' . htmlspecialchars($exp['iEnd'] ?? 'Toujours') . '</td>
                        </tr>';
                    }, $experiences)) . '
                </tbody>
            </table>
        </div>') . '
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
