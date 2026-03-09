<?php
$title = 'Tableau de bord - Alumni CNDA';
$content = '
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-600">
        <h1 class="text-4xl font-bold text-gray-900">Bienvenue, ' . htmlspecialchars($user->FirstName ?? '') . ' ' . htmlspecialchars($user->LastName ?? '') . '! 👋</h1>
        <p class="text-gray-600 mt-2">Gérez votre profil et vos informations d\'alumni ci-dessous.</p>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Card 1: Profil -->
        <a href="/profile" class="group">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform group-hover:scale-105 transition duration-300">
                <div class="text-3xl mb-3">👤</div>
                <h3 class="text-xl font-bold mb-2">Profil</h3>
                <p class="text-indigo-100 text-sm">Mettez à jour vos informations personnelles</p>
                <div class="mt-4 inline-block bg-white text-indigo-600 px-3 py-1 rounded-full text-xs font-semibold group-hover:bg-indigo-50">
                    Voir →
                </div>
            </div>
        </a>

        <!-- Card 2: Expérience -->
        <a href="/work-experience" class="group">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform group-hover:scale-105 transition duration-300">
                <div class="text-3xl mb-3">💼</div>
                <h3 class="text-xl font-bold mb-2">Expérience</h3>
                <p class="text-purple-100 text-sm">Ajoutez votre historique professionnel</p>
                <div class="mt-4 inline-block bg-white text-purple-600 px-3 py-1 rounded-full text-xs font-semibold group-hover:bg-purple-50">
                    Voir →
                </div>
            </div>
        </a>

        <!-- Card 3: Contacts -->
        <a href="/contact-info" class="group">
            <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg p-6 text-white transform group-hover:scale-105 transition duration-300">
                <div class="text-3xl mb-3">📱</div>
                <h3 class="text-xl font-bold mb-2">Contacts</h3>
                <p class="text-rose-100 text-sm">Gérez vos informations de contact</p>
                <div class="mt-4 inline-block bg-white text-rose-600 px-3 py-1 rounded-full text-xs font-semibold group-hover:bg-rose-50">
                    Voir →
                </div>
            </div>
        </a>
    </div>

    <!-- Profile Summary -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4">
            <h2 class="text-xl font-bold text-white">📋 Votre Profil</h2>
        </div>
        <div class="p-6 grid md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Nom d\'utilisateur</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">' . htmlspecialchars($user->sLogin ?? '') . '</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm font-semibold">Nom complet</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">' . htmlspecialchars($user->FirstName ?? '') . ' ' . htmlspecialchars($user->LastName ?? '') . '</p>
            </div>
            ' . ($user->dCreation ? '<div>
                <p class="text-gray-600 text-sm font-semibold">Date d\'inscription</p>
                <p class="text-xl font-bold text-gray-900 mt-1">' . date('d/m/Y H:i', strtotime($user->dCreation)) . '</p>
            </div>' : '') . '
            ' . ($user->YearWouldGraduateIn ? '<div>
                <p class="text-gray-600 text-sm font-semibold">Année de graduation</p>
                <p class="text-xl font-bold text-gray-900 mt-1">' . htmlspecialchars($user->YearWouldGraduateIn) . '</p>
            </div>' : '') . '
        </div>
    </div>
</div>
';
include __DIR__ . '/../layouts/base.php';
?>
