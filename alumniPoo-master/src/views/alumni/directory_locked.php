<?php
$title = 'Annuaire des membres - Alumni CNDA';
$content = '
<div class="bg-white rounded-xl shadow-md overflow-hidden max-w-2xl mx-auto">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
        <h1 class="text-xl font-bold text-white">📇 Annuaire des membres</h1>
    </div>
    <div class="p-8 text-center space-y-4">
        <div class="text-4xl">🔒</div>
        <p class="text-gray-700 text-lg font-semibold">Votre cotisation n\'est pas à jour</p>
        <p class="text-gray-500">
            L\'accès à l\'annuaire des membres est réservé aux alumni à jour de leur cotisation.
            Contactez l\'association pour la renouveler.
        </p>
        <a href="/dashboard" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-semibold py-2 px-6 rounded-lg transition">
            Retour au tableau de bord
        </a>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
