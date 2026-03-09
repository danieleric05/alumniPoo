<?php
$title = 'Connexion - Alumni CNDA';
$hideNav = true;
$content = '
<div class="w-full max-w-md mx-auto">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-white text-2xl font-bold">A</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Alumni CNDA</h1>
            <p class="text-gray-600 mt-2">Accédez à votre profil</p>
        </div>

        ' . (isset($error) ? '<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">' . htmlspecialchars($error) . '</div>' : '') . '

        ' . (!empty($errors) ? '<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg"><ul class="list-disc list-inside text-red-700 text-sm space-y-1">' . implode('', array_map(function($field, $msgs) {
            return '<li><strong>' . htmlspecialchars($field) . ':</strong> ' . htmlspecialchars(is_array($msgs) ? $msgs[0] : $msgs) . '</li>';
        }, array_keys($errors), $errors)) . '</ul></div>' : '') . '

        <form method="POST" action="/login" class="space-y-5">
            <div>
                <label for="sLogin" class="block text-sm font-medium text-gray-700 mb-2">Nom d\'utilisateur</label>
                <input type="text" id="sLogin" name="sLogin" value="' . htmlspecialchars($sLogin ?? '') . '" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="sPassword" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                <input type="password" id="sPassword" name="sPassword" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-4 rounded-lg transition transform hover:scale-105 duration-200">
                Se connecter
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Pas encore inscrit?</span>
            </div>
        </div>

        <a href="/register" class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
            Créer un compte
        </a>
    </div>

    <!-- Footer Info -->
    <p class="text-center text-slate-600 mt-8 text-sm">
        Identifiants de test: <strong>admin</strong> / <strong>admin123</strong>
    </p>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
