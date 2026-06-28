<?php
// public/index.php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: /projet_technologique/public/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laugh Tale - Test de QI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* Force le curseur flèche partout sur le texte statique */
    body {
        cursor: default;
    }
    /* Conserve le curseur de saisie uniquement là où on écrit ou sélectionne */
    input, textarea, [contenteditable="true"] {
        cursor: text;
    }
    /* Conserve le curseur main cliquable sur les boutons et liens */
    button, a, label, select, [onclick] {
        cursor: pointer;
    }
</style>

</head>
<body class="min-h-full bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-slate-100 antialiased font-sans flex flex-col justify-between">

    <!-- HEADER / NAVBAR -->
<!-- HEADER / NAVBAR -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            
            <!-- Logo Vectoriel Autonome des Mugiwara (Chapeau de Paille) -->
            <div class="w-10 h-10 bg-slate-900 border border-slate-800 rounded-xl flex items-center justify-center p-1 shadow-md shadow-amber-500/5 shrink-0">
                <svg viewBox="0 0 64 64" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <!-- Les os croisés en arrière-plan (Blanc cassé) -->
                    <path d="M12 12L22 22M52 12L42 22M12 52L22 42M52 52L42 42" stroke="#E2E8F0" stroke-width="4" stroke-linecap="round" />
                    
                    <!-- Le Crâne (Blanc) -->
                    <path d="M32 16C22 16 20 23 20 31C20 39 24 43 32 43C40 43 44 39 44 31C44 23 40 16 32 16Z" fill="#FFFFFF" />
                    <path d="M26 43H38V49H26V43Z" fill="#FFFFFF" /> <!-- Mâchoire -->
                    
                    <!-- Les Yeux (Noir profond) -->
                    <circle cx="27" cy="28" r="3.5" fill="#020617" />
                    <circle cx="37" cy="28" r="3.5" fill="#020617" />
                    
                    <!-- Le Nez (Noir profond) -->
                    <path d="M31 32L33 32L32 35Z" fill="#020617" />
                    
                    <!-- Les Dents (Lignes de la mâchoire) -->
                    <path d="M30 43V49M34 43V49" stroke="#020617" stroke-width="1.5" />

                    <!-- Le Chapeau de Paille (Jaune Paille / Or) -->
                    <path d="M10 24C18 16 46 16 54 24C58 24 60 27 58 29C50 29 46 27 32 27C18 27 14 29 6 29C4 27 6 24 10 24Z" fill="#F59E0B" />
                    <!-- Le Ruban Rouge de Luffy -->
                    <path d="M16 23.5C24 20 40 20 48 23.5C47 25.5 43 26 32 26C21 26 17 25.5 16 23.5Z" fill="#EF4444" />
                </svg>
            </div>

            <!-- Titre de ton site -->
            <span class="font-black text-xl tracking-tight bg-gradient-to-r from-amber-400 via-yellow-200 to-cyan-400 bg-clip-text text-transparent uppercase font-serif">Laugh Tale</span>
        </div>
        
        <button onclick="ouvrirAuthentification()" class="text-sm font-bold text-cyan-400 bg-cyan-950/40 hover:bg-cyan-950/80 px-5 py-2.5 rounded-xl border border-cyan-500/20 transition-all">
            Espace Membre
        </button>
    </header>

    <!-- ZONE 1 : LANDING PAGE CLANDESTINE -->
    <main id="landing-zone" class="max-w-4xl mx-auto px-4 text-center space-y-8 my-auto transition-all duration-500">
        <div class="space-y-4">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-black bg-cyan-950/60 text-cyan-400 border border-cyan-500/30 uppercase tracking-widest shadow-sm">
                🏴‍☠️ Évaluation Logique Log Pose v1.0
            </span>
            <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-none">
                Mesurez votre potentiel <br>
                <span class="bg-gradient-to-r from-cyan-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent">intellectuel et analytique</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-400 max-w-xl mx-auto font-medium">
                Un test de 10 questions chronométrées basé sur des structures relationnelles pour mesurer votre logique pure et vous mesurer au classement global.
            </p>
        </div>

        <div>
            <button onclick="ouvrirAuthentification()" class="bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-600 hover:to-indigo-700 text-white font-black text-lg px-10 py-5 rounded-2xl shadow-xl shadow-cyan-500/20 transition-all duration-300 transform hover:-translate-y-1 active:scale-95">
                ⚡ Commencer le test de QI
            </button>
            <p class="text-xs text-slate-500 mt-3 font-medium">Gratuit • Sauvegarde des scores instantanée</p>
        </div>
    </main>

    <!-- ZONE 2 : UNIQUE BLOCK AVEC ONGLES INTERACTIFS (TABS) -->
    <div id="auth-zone" class="hidden max-w-md mx-auto w-full px-4 my-auto transition-all duration-500">
        <div class="bg-slate-900/80 backdrop-blur-md py-8 px-6 sm:px-10 rounded-3xl shadow-2xl shadow-black/50 border border-slate-800">
            
            <button onclick="retourLanding()" class="mb-6 inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white transition-colors">
                ← Retour à l'accueil
            </button>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-rose-950/40 border border-rose-800/50 rounded-2xl flex items-center gap-3 text-rose-400 text-sm font-semibold">
                    <span>⚠️</span> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
                <script>document.addEventListener("DOMContentLoaded", function() { ouvrirAuthentification(); });</script>
            <?php endif; ?>

            <!-- Barre d'onglets Sombre -->
            <div class="flex p-1 bg-slate-950 rounded-xl mb-6 border border-slate-800/60">
                <button id="tab-login" onclick="switchForm('login')" class="w-1/2 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 bg-slate-800 text-white shadow-sm">
                    Connexion
                </button>
                <button id="tab-register" onclick="switchForm('register')" class="w-1/2 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 text-slate-400 hover:text-white">
                    Inscription
                </button>
            </div>

            <!-- FORMULAIRE CONNEXION D'ORIGINE -->
            <div id="form-login-block" class="space-y-6">
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">Connexion</h2>
                    <p class="text-xs text-slate-400 mt-1 font-medium">Ravi de vous revoir ! Connectez-vous à votre espace.</p>
                </div>
                <form action="/projet_technologique/server/login.php" method="POST" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email</label>
                        <input type="email" name="email" required placeholder="exemple@mail.com" class="w-full px-4 py-3 rounded-xl border border-slate-800 bg-slate-950 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mot de passe</label>
                        <input type="password" name="mot_de_passe" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-800 bg-slate-950 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-600 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-md transition-all">Se connecter</button>
                </form>
            </div>

            <!-- FORMULAIRE INSCRIPTION D'ORIGINE -->
            <div id="form-register-block" class="hidden space-y-6">
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight">Créer un compte</h2>
                    <p class="text-xs text-slate-400 mt-1 font-medium">Rejoignez la plateforme et défiez le classement.</p>
                </div>
                <form action="/projet_technologique/server/register.php" method="POST" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nom complet / Pseudo</label>
                        <input type="text" name="nom" required placeholder="Ex: Jean Dupont" class="w-full px-4 py-3 rounded-xl border border-slate-800 bg-slate-950 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email</label>
                        <input type="email" name="email" required placeholder="exemple@mail.com" class="w-full px-4 py-3 rounded-xl border border-slate-800 bg-slate-950 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mot de passe</label>
                        <input type="password" name="mot_de_passe" required placeholder="Minimum 6 caractères" class="w-full px-4 py-3 rounded-xl border border-slate-800 bg-slate-950 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3.5 rounded-xl shadow-md transition-all">Créer mon compte</button>
                </form>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
    <footer class="w-full text-center py-6 text-xs text-slate-600 font-medium">
        © 2026 Laugh Tale — Projet Technologique Universitaire. Tous droits réservés.
    </footer>

    <script>
        const landingZone = document.getElementById('landing-zone');
        const authZone = document.getElementById('auth-zone');
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');
        const formLoginBlock = document.getElementById('form-login-block');
        const formRegisterBlock = document.getElementById('form-register-block');

        function ouvrirAuthentification() {
            landingZone.classList.add('hidden');
            authZone.classList.remove('hidden');
        }

        function retourLanding() {
            authZone.classList.add('hidden');
            landingZone.classList.remove('hidden');
        }

        // Gestion de la bascule d'onglets
        function switchForm(type) {
            if(type === 'login') {
                tabLogin.classList.add('bg-slate-800', 'text-white');
                tabLogin.classList.remove('text-slate-400');
                tabRegister.classList.remove('bg-slate-800', 'text-white');
                tabRegister.classList.add('text-slate-400');
                
                formLoginBlock.classList.remove('hidden');
                formRegisterBlock.classList.add('hidden');
            } else {
                tabRegister.classList.add('bg-slate-800', 'text-white');
                tabRegister.classList.remove('text-slate-400');
                tabLogin.classList.remove('bg-slate-800', 'text-white');
                tabLogin.classList.add('text-slate-400');
                
                formRegisterBlock.classList.remove('hidden');
                formLoginBlock.classList.add('hidden');
            }
        }
    </script>
</body>
</html>