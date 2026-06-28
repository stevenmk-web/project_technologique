<?php
// public/dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /projet_technologique/public/index.php');
    exit();
}

require_once dirname(__DIR__) . '/config/db.php';
$current_user_id = $_SESSION['user_id'];

try {
    $stmt_check_active = $pdo->prepare("SELECT id FROM tentatives WHERE utilisateur_id = ? AND statut = 'en_cours' LIMIT 1");
    $stmt_check_active->execute([$current_user_id]);
    $tentative_active = $stmt_check_active->fetch();

    $stmt_user = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
    $stmt_user->execute([$current_user_id]);
    $user = $stmt_user->fetch();

    $stmt_historique = $pdo->prepare("
        SELECT r.score_qi, r.bonnes_reponses, r.total_questions, t.date_fin_reelle 
        FROM resultats r
        JOIN tentatives t ON r.tentative_id = t.id
        WHERE t.utilisateur_id = ? AND t.statut = 'termine'
        ORDER BY t.date_fin_reelle DESC
    ");
    $stmt_historique->execute([$current_user_id]);
    $historique_personnel = $stmt_historique->fetchAll();

    $total_tentatives = count($historique_personnel);
    $meilleur_score = $total_tentatives > 0 ? max(array_column($historique_personnel, 'score_qi')) : 0;

    $stmt_classement = $pdo->query("
        SELECT u.nom, MAX(r.score_qi) as max_qi, MAX(t.date_fin_reelle) as date_perf
        FROM resultats r
        JOIN tentatives t ON r.tentative_id = t.id
        JOIN utilisateurs u ON t.utilisateur_id = u.id
        WHERE t.statut = 'termine'
        GROUP BY u.id
        ORDER BY max_qi DESC
        LIMIT 10
    ");
    $classement_general = $stmt_classement->fetchAll();

} catch (PDOException $e) {
    die("Erreur d'affichage : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laugh Tale</title>
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
<body class="min-h-full bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-slate-100 antialiased font-sans">

<!-- Navbar Sombre Fluide -->
    <nav class="sticky top-0 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/60 px-8 py-4 flex justify-between items-center shadow-sm z-50">
        <div class="flex items-center gap-3">
            
            <!-- Logo Vectoriel Autonome des Mugiwara (Chapeau de Paille) -->
            <div class="w-10 h-10 bg-slate-950 border border-slate-800 rounded-xl flex items-center justify-center p-1 shadow-md shadow-amber-500/5 shrink-0">
                <svg viewBox="0 0 64 64" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <!-- Les os croisés en arrière-plan -->
                    <path d="M12 12L22 22M52 12L42 22M12 52L22 42M52 52L42 42" stroke="#E2E8F0" stroke-width="4" stroke-linecap="round" />
                    
                    <!-- Le Crâne -->
                    <path d="M32 16C22 16 20 23 20 31C20 39 24 43 32 43C40 43 44 39 44 31C44 23 40 16 32 16Z" fill="#FFFFFF" />
                    <path d="M26 43H38V49H26V43Z" fill="#FFFFFF" />
                    
                    <!-- Les Yeux -->
                    <circle cx="27" cy="28" r="3.5" fill="#020617" />
                    <circle cx="37" cy="28" r="3.5" fill="#020617" />
                    
                    <!-- Le Nez -->
                    <path d="M31 32L33 32L32 35Z" fill="#020617" />
                    
                    <!-- Les Dents -->
                    <path d="M30 43V49M34 43V49" stroke="#020617" stroke-width="1.5" />

                    <!-- Le Chapeau de Paille -->
                    <path d="M10 24C18 16 46 16 54 24C58 24 60 27 58 29C50 29 46 27 32 27C18 27 14 29 6 29C4 27 6 24 10 24Z" fill="#F59E0B" />
                    <!-- Le Ruban Rouge -->
                    <path d="M16 23.5C24 20 40 20 48 23.5C47 25.5 43 26 32 26C21 26 17 25.5 16 23.5Z" fill="#EF4444" />
                </svg>
            </div>

            <!-- Titre de la plateforme -->
            <span class="font-black text-xl tracking-tight bg-gradient-to-r from-amber-400 via-yellow-200 to-cyan-400 bg-clip-text text-transparent uppercase font-serif">Laugh Tale</span>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></div>
                <span class="text-sm font-bold text-slate-400">
                    Bienvenue, 
                    <strong class="text-lg font-black tracking-tight bg-gradient-to-r from-amber-400 via-yellow-200 to-cyan-400 bg-clip-text text-transparent uppercase font-serif pl-1">
                        <?php echo htmlspecialchars($user['nom'] ?? 'Abonné'); ?>
                    </strong>
                </span>
            </div>
            <a href="/projet_technologique/server/logout.php" class="text-xs font-bold text-slate-400 bg-slate-800 hover:bg-rose-950/60 hover:text-rose-400 px-4 py-2.5 rounded-xl border border-slate-700/50 transition-all duration-300">Déconnexion</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8 space-y-8">
        
        <!-- Bannière Log Pose -->
        <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-cyan-950/40 to-slate-900 p-8 md:p-10 rounded-3xl border border-cyan-500/20 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2 z-10">
                <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">Prêt à décoder le Log Pose ?</h2>
                <p class="text-slate-400 text-sm max-w-xl">Un test algorithmique standardisé de 10 questions conçu pour évaluer votre logique pure, votre rapidité et vos compétences analytiques.</p>
            </div>
            <div class="z-10 shrink-0">
                <?php if ($tentative_active): ?>
                    <?php $_SESSION['active_tentative_id'] = $tentative_active['id']; ?>
                    <a href="/projet_technologique/public/quiz.php" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-orange-500/20 transition-all duration-300 transform hover:-translate-y-0.5 inline-block whitespace-nowrap">
                        ▶️ Reprendre le test
                    </a>
                <?php else: ?>
                    <a href="/projet_technologique/server/start_quiz.php" class="bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-600 hover:to-indigo-700 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-cyan-500/20 transition-all duration-300 transform hover:-translate-y-0.5 inline-block whitespace-nowrap">
                        🎯 Lancer l'Évaluation
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-900/80 backdrop-blur-md p-6 rounded-3xl border border-slate-800 shadow-sm flex items-center gap-5">
                <div class="p-4 bg-amber-500/10 text-amber-400 rounded-2xl text-3xl border border-amber-500/20">🏆</div>
                <div class="space-y-0.5">
                    <span class="text-xs text-slate-500 uppercase tracking-widest font-black">Record Personnel</span>
                    <h3 class="text-3xl font-black text-white"><?php echo $meilleur_score > 0 ? $meilleur_score . " <span class='text-lg font-bold text-slate-500'>QI</span>" : "Aucun test"; ?></h3>
                </div>
            </div>
            <div class="bg-slate-900/80 backdrop-blur-md p-6 rounded-3xl border border-slate-800 shadow-sm flex items-center gap-5">
                <div class="p-4 bg-cyan-500/10 text-cyan-400 rounded-2xl text-3xl border border-cyan-500/20">⚡</div>
                <div class="space-y-0.5">
                    <span class="text-xs text-slate-500 uppercase tracking-widest font-black">Sessions Finalisées</span>
                    <h3 class="text-3xl font-black text-white"><?php echo $total_tentatives; ?> <span class='text-lg font-bold text-slate-500'>test(s)</span></h3>
                </div>
            </div>
        </div>

        <!-- Tableaux -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Historique -->
            <div class="lg:col-span-7 bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="font-black text-white text-lg tracking-tight">Vos Dernières Performances</h3>
                    <span class="text-xs font-bold text-cyan-400 bg-cyan-950/40 border border-cyan-500/20 px-3 py-1 rounded-full">Historique</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950 text-slate-500 uppercase text-[10px] tracking-widest font-black border-b border-slate-800">
                                <th class="p-4 pl-6">Date de complétion</th>
                                <th class="p-4">Score Estimé</th>
                                <th class="p-4 pr-6">Précision</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <?php if (empty($historique_personnel)): ?>
                                <tr>
                                    <td colspan="3" class="p-12 text-center text-slate-500 text-sm font-medium">Aucun test enregistré pour le moment.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historique_personnel as $row): ?>
                                    <tr class="hover:bg-slate-800/30 transition-all duration-200 group">
                                        <td class="p-4 pl-6 text-sm text-slate-400 group-hover:text-white transition-colors"><?php echo date('d/m/Y à H:i', strtotime($row['date_fin_reelle'])); ?></td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-sm font-black bg-cyan-950/40 text-cyan-400 border border-cyan-500/20">
                                                🧠 <?php echo $row['score_qi']; ?>
                                            </span>
                                        </td>
                                        <td class="p-4 pr-6 text-sm font-bold text-slate-300"><?php echo $row['bonnes_reponses'] . ' / ' . $row['total_questions']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Classement -->
            <div class="lg:col-span-5 bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="font-black text-white text-lg tracking-tight">Top des Rois des Pirates</h3>
                    <span class="text-xs font-bold text-amber-400 bg-amber-950/40 border border-amber-500/20 px-3 py-1 rounded-full">Top 10</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-950 text-slate-500 uppercase text-[10px] tracking-widest font-black border-b border-slate-800">
                                <th class="p-4 text-center w-16">Rang</th>
                                <th class="p-4">Joueur</th>
                                <th class="p-4 pr-6 text-right">Meilleur QI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <?php if (empty($classement_general)): ?>
                                <tr>
                                    <td colspan="3" class="p-12 text-center text-slate-500 text-sm font-medium">Aucun pirate dans le classement.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classement_general as $index => $leader): ?>
                                    <tr class="hover:bg-cyan-950/10 transition-all duration-200 <?php echo ($index === 0) ? 'bg-amber-950/20' : ''; ?>">
                                        <td class="p-4 text-center font-black text-sm">
                                            <?php 
                                            if ($index === 0) echo '<span class="text-xl">🥇</span>';
                                            elseif ($index === 1) echo '<span class="text-xl">🥈</span>';
                                            elseif ($index === 2) echo '<span class="text-xl">🥉</span>';
                                            else echo '<span class="bg-slate-800 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-400">' . ($index + 1) . '</span>';
                                            ?>
                                        </td>
                                        <td class="p-4 text-sm font-bold text-slate-200"><?php echo htmlspecialchars($leader['nom']); ?></td>
                                        <td class="p-4 pr-6 text-sm font-black text-cyan-400 text-right"><?php echo $leader['max_qi']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</body>
</html>