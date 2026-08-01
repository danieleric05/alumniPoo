<?php
$title = 'Ajouter une information de contact - Alumni CNDA';
$content = '
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
        <h1 class="text-2xl font-bold text-white">➕ Ajouter une information de contact</h1>
    </div>

    <div class="p-6">
        ' . (!empty($errors) ? '<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"><ul class="list-disc list-inside text-red-700 text-sm space-y-1">' . implode('', array_map(function($field, $msgs) {
            return '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars(is_array($msgs) ? $msgs[0] : $msgs) . '</li>';
        }, array_keys($errors), $errors)) . '</ul></div>' : '') . '

        <form method="POST" action="/contact-info/add" class="space-y-5">
            ' . \Formulair\Core\Csrf::field() . '
            <div>
                <label for="iType" class="block text-sm font-medium text-gray-700 mb-2">Type de contact *</label>
                <select id="iType" name="iType" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">-- Sélectionnez un type --</option>
                    ' . implode('', array_map(function($type) {
                        return '<option value="' . $type->id . '">' . htmlspecialchars($type->sLabel ?? '') . '</option>';
                    }, $types)) . '
                </select>
            </div>

            <div>
                <label for="sValue" class="block text-sm font-medium text-gray-700 mb-2">Valeur *</label>
                <input type="text" id="sValue" name="sValue" value="' . htmlspecialchars($data['sValue'] ?? '') . '" required placeholder="ex: email@example.com ou +33 6 12 34 56 78" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition transform hover:scale-105 duration-200">
                    Enregistrer
                </button>
                <a href="/contact-info" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
