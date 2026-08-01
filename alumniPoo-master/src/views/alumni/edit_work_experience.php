<?php
$title = 'Modifier une expérience - Alumni CNDA';
$content = '
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
        <h1 class="text-2xl font-bold text-white">✏️ Modifier l\'expérience professionnelle</h1>
    </div>

    <div class="p-6">
        ' . (!empty($errors) ? '<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"><ul class="list-disc list-inside text-red-700 text-sm space-y-1">' . implode('', array_map(function($field, $msgs) {
            return '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars(is_array($msgs) ? $msgs[0] : $msgs) . '</li>';
        }, array_keys($errors), $errors)) . '</ul></div>' : '') . '

        <form method="POST" action="/work-experience/edit/' . $experience->id . '" class="space-y-5">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="sCompany" class="block text-sm font-medium text-gray-700 mb-2">Entreprise *</label>
                    <input type="text" id="sCompany" name="sCompany" value="' . htmlspecialchars($experience->sCompany ?? '') . '" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="iDivision" class="block text-sm font-medium text-gray-700 mb-2">Division</label>
                    <select id="iDivision" name="iDivision" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">-- Sélectionnez une division --</option>
                        ' . implode('', array_map(function($div) use ($experience) {
                            $selected = (string) $div->id === (string) ($experience->iDivision ?? '') ? ' selected' : '';
                            return '<option value="' . $div->id . '"' . $selected . '>' . htmlspecialchars($div->sLabel ?? '') . '</option>';
                        }, $divisions)) . '
                    </select>
                </div>

                <div>
                    <label for="iCity" class="block text-sm font-medium text-gray-700 mb-2">Ville (sélectionnée)</label>
                    <select id="iCity" name="iCity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <option value="">-- Sélectionnez une ville --</option>
                        ' . implode('', array_map(function($city) use ($experience) {
                            $selected = (string) $city->id === (string) ($experience->iCity ?? '') ? ' selected' : '';
                            return '<option value="' . $city->id . '"' . $selected . '>' . htmlspecialchars($city->sLabel ?? '') . '</option>';
                        }, $cities)) . '
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="sCity" class="block text-sm font-medium text-gray-700 mb-2">Autre ville</label>
                    <input type="text" id="sCity" name="sCity" value="' . htmlspecialchars($experience->sCity ?? '') . '" placeholder="Ou entrez manuellement une ville" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="dStart" class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                    <input type="date" id="dStart" name="dStart" value="' . htmlspecialchars($experience->dStart ?? '') . '" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="iEnd" class="block text-sm font-medium text-gray-700 mb-2">Année de fin</label>
                    <input type="number" id="iEnd" name="iEnd" min="1900" max="2100" value="' . htmlspecialchars($experience->iEnd ?? '') . '" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <div class="md:col-span-2">
                    <label for="sDescription" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="sDescription" name="sDescription" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">' . htmlspecialchars($experience->sDescription ?? '') . '</textarea>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition transform hover:scale-105 duration-200">
                    Enregistrer
                </button>
                <a href="/work-experience" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
