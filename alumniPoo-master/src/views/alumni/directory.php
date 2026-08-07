<?php
$title = 'Annuaire des membres - Alumni CNDA';
$content = '
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">📇 Annuaire des membres</h1>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        ' . (empty($members) ? '<div class="p-8 text-center">
            <p class="text-gray-500 text-lg">Aucun membre à jour de cotisation pour le moment.</p>
        </div>' : '<div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-slate-900 to-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Nom</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Promotion</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    ' . implode('', array_map(function($m) use ($currentUserId) {
                        $promotion = ($m->iYearStart || $m->iYearEnd)
                            ? htmlspecialchars($m->iYearStart ?? '?') . ' - ' . htmlspecialchars($m->iYearEnd ?? '?')
                            : 'N/A';
                        $isSelf = (int) $m->id === $currentUserId;

                        return '<tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">' . htmlspecialchars($m->FirstName . ' ' . $m->LastName) . ($isSelf ? ' <span class="text-gray-400 text-xs italic">(vous)</span>' : '') . '</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">' . $promotion . '</td>
                            <td class="px-6 py-4">
                                <a href="/directory/' . $m->id . '" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold py-1 px-3 rounded text-xs transition">
                                    Voir le profil →
                                </a>
                            </td>
                        </tr>';
                    }, $members)) . '
                </tbody>
            </table>
        </div>') . '
        ' . \Formulair\Core\Paginator::renderNav($paginator, '/directory') . '
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
