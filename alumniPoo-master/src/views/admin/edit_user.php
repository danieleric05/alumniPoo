<?php
$title = 'Modifier un utilisateur - Alumni CNDA';
$content = '
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
        <h1 class="text-2xl font-bold text-white">✏️ Modifier le profil de ' . htmlspecialchars($user->sLogin ?? '') . '</h1>
    </div>

    <div class="p-6">
        ' . (!empty($errors) ? '<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"><ul class="list-disc list-inside text-red-700 text-sm space-y-1">' . implode('', array_map(function($field, $msgs) {
            return '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars(is_array($msgs) ? $msgs[0] : $msgs) . '</li>';
        }, array_keys($errors), $errors)) . '</ul></div>' : '') . '

        <form method="POST" action="/admin/users/' . $user->id . '/edit" class="space-y-5">
            ' . \Formulair\Core\Csrf::field() . '
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="FirstName" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                    <input type="text" id="FirstName" name="FirstName" value="' . htmlspecialchars($user->FirstName ?? '') . '" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="LastName" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" id="LastName" name="LastName" value="' . htmlspecialchars($user->LastName ?? '') . '" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <div class="flex items-center gap-3">
                        <select id="iYearStart" name="iYearStart" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="">Début</option>
                            ' . implode('', array_map(function($y) use ($user) {
                                $selected = (int) ($user->iYearStart ?? 0) === $y ? ' selected' : '';
                                return '<option value="' . $y . '"' . $selected . '>' . $y . '</option>';
                            }, range(1990, 2000))) . '
                        </select>
                        <span class="text-gray-500">-</span>
                        <select id="iYearEnd" name="iYearEnd" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="">Fin</option>
                            ' . implode('', array_map(function($y) use ($user) {
                                $selected = (int) ($user->iYearEnd ?? 0) === $y ? ' selected' : '';
                                return '<option value="' . $y . '"' . $selected . '>' . $y . '</option>';
                            }, range(1990, 2000))) . '
                        </select>
                    </div>
                </div>

                <div>
                    <label for="YearWouldGraduateIn" class="block text-sm font-medium text-gray-700 mb-2">Année de graduation</label>
                    <input type="number" id="YearWouldGraduateIn" name="YearWouldGraduateIn" min="1900" max="2100" value="' . htmlspecialchars($user->YearWouldGraduateIn ?? '') . '" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition transform hover:scale-105 duration-200">
                    Enregistrer
                </button>
                <a href="/admin/users" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
