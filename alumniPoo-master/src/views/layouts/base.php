<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Alumni CNDA' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-primary {
            @apply bg-gradient-to-br from-indigo-600 to-purple-700;
        }

        .gradient-text {
            @apply bg-gradient-to-r from-indigo-600 to-purple-700 bg-clip-text text-transparent;
        }
    </style>
</head>
<body class="<?= (!empty($hideNav) ? 'bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500' : 'bg-gradient-to-br from-indigo-50 via-white to-purple-50') ?> min-h-screen flex flex-col">
    <?php if (!empty($hideNav)): ?>
        <div class="flex-1 flex items-center justify-center p-4">
            <?php echo $content ?? ''; ?>
        </div>
    <?php else: ?>
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-slate-900 to-slate-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 gradient-primary rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">A</span>
                        </div>
                        <span class="text-white font-bold text-lg hidden sm:inline">Alumni CNDA</span>
                    </div>

                    <?php if (\Formulair\Middleware\AuthMiddleware::isLoggedIn()): ?>
                        <div class="flex items-center gap-1 sm:gap-4 text-white text-sm sm:text-base">
                            <a href="/dashboard" class="hover:text-indigo-400 transition px-2 sm:px-3 py-2 rounded-md hover:bg-slate-700">
                                Tableau de bord
                            </a>
                            <a href="/profile" class="hover:text-indigo-400 transition px-2 sm:px-3 py-2 rounded-md hover:bg-slate-700">
                                Profil
                            </a>
                            <a href="/work-experience" class="hover:text-indigo-400 transition px-2 sm:px-3 py-2 rounded-md hover:bg-slate-700">
                                Expérience
                            </a>
                            <a href="/contact-info" class="hover:text-indigo-400 transition px-2 sm:px-3 py-2 rounded-md hover:bg-slate-700">
                                Contacts
                            </a>
                            <a href="/directory" class="hover:text-indigo-400 transition px-2 sm:px-3 py-2 rounded-md hover:bg-slate-700">
                                Annuaire
                            </a>
                            <?php if (\Formulair\Middleware\AuthMiddleware::isAdmin()): ?>
                            <a href="/admin/users" class="hover:text-indigo-400 transition px-2 sm:px-3 py-2 rounded-md hover:bg-slate-700">
                                Administration
                            </a>
                            <?php endif; ?>
                            <a href="/logout" class="bg-red-600 hover:bg-red-700 transition px-2 sm:px-3 py-2 rounded-md">
                                Déconnexion
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
            <?php echo $content ?? ''; ?>
        </div>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-8 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; 2025 Alumni CNDA. Tous droits réservés.</p>
            </div>
        </footer>
    <?php endif; ?>
</body>
</html>
