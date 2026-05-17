<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT-Manage | Gestion des Employés</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
        }
        body {
            background-color: #030712;
            color: #f3f4f6;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <!-- Background Cinematic Glows -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-900/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-900/20 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 glass border-b border-white/10 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2 text-2xl font-bold tracking-tighter">
                <span class="bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">IT-MANAGE</span>
            </div>
            <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-400">
                <a href="#" class="hover:text-blue-400 transition">Dashboard</a>
                <a href="#" class="hover:text-blue-400 transition">Projets</a>
                <a href="#" class="hover:text-blue-400 transition">RH Space</a>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-sm font-medium hover:text-white transition">Connexion</button>
                <button class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded-lg text-sm font-semibold transition glow">
                    Commencer
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-20 pb-16 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <span class="inline-block px-4 py-1.5 mb-6 text-xs font-semibold tracking-widest text-blue-400 uppercase bg-blue-400/10 rounded-full border border-blue-400/20">
                Propulsé par Laravel & React
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 tracking-tight">
                Gérez vos talents <br> <span class="text-blue-500 text-glow">avec précision.</span>
            </h1>
            <p class="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                Centralisez les compétences, suivez les projets IT et optimisez les performances de vos équipes dans une interface ultra-moderne.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <button class="px-8 py-4 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition">
                    Découvrir les fonctionnalités
                </button>
                <button class="px-8 py-4 glass border border-white/10 font-bold rounded-xl hover:bg-white/5 transition">
                    Voir la démo
                </button>
            </div>
        </div>
    </header>

    <!-- Simple Dashboard Preview -->
    <section class="px-6 mb-24">
        <div class="max-w-5xl mx-auto glass p-4 rounded-2xl border border-white/10 shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white/5 p-6 rounded-xl border border-white/5">
                    <p class="text-gray-400 text-sm">Employés Actifs</p>
                    <h3 class="text-3xl font-bold">124</h3>
                    <div class="mt-2 text-xs text-green-400 font-medium">+12% ce mois</div>
                </div>
                <div class="bg-white/5 p-6 rounded-xl border border-white/5">
                    <p class="text-gray-400 text-sm">Projets en cours</p>
                    <h3 class="text-3xl font-bold">18</h3>
                    <div class="mt-2 text-xs text-blue-400 font-medium">4 en phase finale</div>
                </div>
                <div class="bg-white/5 p-6 rounded-xl border border-white/5">
                    <p class="text-gray-400 text-sm">Score Performance</p>
                    <h3 class="text-3xl font-bold">4.8/5</h3>
                    <div class="mt-2 text-xs text-purple-400 font-medium">Excellent</div>
                </div>
            </div>
        </div>
    </section>

     <section class="max-w-7xl mx-auto px-6 pb-24">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
             <div class="p-8 rounded-2xl glass hover:border-blue-500/50 transition duration-500 group">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-users text-blue-400 text-xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Gestion RH</h4>
                <p class="text-gray-400 text-sm leading-relaxed">Suivi complet des profils, contrats et rôles (Admin, Manager, RH).</p>
            </div>

             <div class="p-8 rounded-2xl glass hover:border-indigo-500/50 transition duration-500 group">
                <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-code text-indigo-400 text-xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Compétences</h4>
                <p class="text-gray-400 text-sm leading-relaxed">Cartographie des skills techniques et niveaux d'expertise.</p>
            </div>

             <div class="p-8 rounded-2xl glass hover:border-purple-500/50 transition duration-500 group">
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-tasks text-purple-400 text-xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Affectations</h4>
                <p class="text-gray-400 text-sm leading-relaxed">Assignez les bonnes personnes aux bons projets IT sans effort.</p>
            </div>

             <div class="p-8 rounded-2xl glass hover:border-emerald-500/50 transition duration-500 group">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
                    <i class="fas fa-chart-line text-emerald-400 text-xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-3">Performance</h4>
                <p class="text-gray-400 text-sm leading-relaxed">Évaluations continues et feedback en temps réel via notifications.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">
            <p>© 2026 IT-MANAGE Ecosystem. Tous droits réservés.</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-white transition">Documentation API</a>
                <a href="#" class="hover:text-white transition">Support</a>
                <a href="#" class="hover:text-white transition">Privacy</a>
            </div>
        </div>
    </footer>

</body>
</html>