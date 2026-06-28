<?php
// public/quiz.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /projet_technologique/public/index.php');
    exit();
}

require_once dirname(__DIR__) . '/config/db.php';

$tentative_id = isset($_SESSION['active_tentative_id']) ? intval($_SESSION['active_tentative_id']) : 0;
$categorie_id = 1;

if ($tentative_id === 0) {
    header('Location: /projet_technologique/public/dashboard.php');
    exit();
}

try {
    $stmt_tentative = $pdo->prepare("SELECT date_fin_prevue, statut FROM tentatives WHERE id = ?");
    $stmt_tentative->execute([$tentative_id]);
    $tentative = $stmt_tentative->fetch();

    if ($tentative && $tentative['statut'] !== 'en_cours') {
        header('Location: /projet_technologique/public/dashboard.php');
        exit();
    }

    $maintenant = time();
    $date_fin_timestamp = strtotime($tentative['date_fin_prevue'] ?? date('Y-m-d H:i:s', strtotime('+15 minutes')));
    $temps_restant_secondes = $date_fin_timestamp - $maintenant;

    if ($temps_restant_secondes <= 0) {
        // AJOUT DE CES 2 LIGNES UNIQUEMENT : On ferme la vieille tentative pour libérer le joueur
        $stmt_close = $pdo->prepare("UPDATE tentatives SET statut = 'termine' WHERE id = ?");
        $stmt_close->execute([$tentative_id]);

        header('Location: /projet_technologique/public/dashboard.php');
        exit();
    }

    $stmt_questions = $pdo->prepare("SELECT id, texte_question FROM questions WHERE categorie_id = ?");
    $stmt_questions->execute([$categorie_id]);
    $questions = $stmt_questions->fetchAll();

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laugh Tale - Épreuve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Effet d'activation de la réponse cochée */
        input[type="radio"]:checked ~ div {
            border-color: #06b6d4;
            background-color: rgba(6, 182, 212, 0.05);
        }
        body { cursor: default; }
        input, textarea { cursor: text; }
        button, a, label { cursor: pointer; }
    </style>
</head>
<body class="min-h-full bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-slate-100 font-sans antialiased flex flex-col justify-between select-none">

    <div id="prep-screen" class="max-w-4xl mx-auto px-6 py-12 my-auto space-y-10 text-center transition-all duration-500 opacity-100 transform scale-100">
        <div class="w-20 h-20 bg-slate-900 border border-slate-800 rounded-3xl flex items-center justify-center p-3 shadow-xl shadow-amber-500/5 mx-auto">
            <svg viewBox="0 0 64 64" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12L22 22M52 12L42 22M12 52L22 42M52 52L42 42" stroke="#E2E8F0" stroke-width="4" stroke-linecap="round" />
                <path d="M32 16C22 16 20 23 20 31C20 39 24 43 32 43C40 43 44 39 44 31C44 23 40 16 32 16Z" fill="#FFFFFF" />
                <path d="M26 43H38V49H26V43Z" fill="#FFFFFF" />
                <circle cx="27" cy="28" r="3.5" fill="#020617" />
                <circle cx="37" cy="28" r="3.5" fill="#020617" />
                <path d="M31 32L33 32L32 35Z" fill="#020617" />
                <path d="M30 43V49M34 43V49" stroke="#020617" stroke-width="1.5" />
                <path d="M10 24C18 16 46 16 54 24C58 24 60 27 58 29C50 29 46 27 32 27C18 27 14 29 6 29C4 27 6 24 10 24Z" fill="#F59E0B" />
                <path d="M16 23.5C24 20 40 20 48 23.5C47 25.5 43 26 32 26C21 26 17 25.5 16 23.5Z" fill="#EF4444" />
            </svg>
        </div>

        <div class="space-y-3">
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white font-serif">
                Vous êtes sur le point de commencer l'épreuve de <span class="bg-gradient-to-r from-amber-400 via-yellow-200 to-cyan-400 bg-clip-text text-transparent uppercase">Laugh Tale ©</span>
            </h1>
            <p class="text-sm text-slate-400 font-medium">Préparez votre esprit à analyser les structures du Log Pose</p>
        </div>

        <div class="max-w-xl mx-auto text-left space-y-4 pt-4">
            <div class="flex items-center gap-4 bg-slate-900/50 p-4 rounded-2xl border border-slate-800/60">
                <span class="w-8 h-8 rounded-xl bg-cyan-950 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-black text-sm shrink-0">1</span>
                <p class="text-sm font-semibold text-slate-300">Vous disposez de <span class="text-cyan-400 font-bold">15 minutes</span> pour répondre aux 10 questions logiques.</p>
            </div>
            <div class="flex items-center gap-4 bg-slate-900/50 p-4 rounded-2xl border border-slate-800/60">
                <span class="w-8 h-8 rounded-xl bg-cyan-950 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-black text-sm shrink-0">2</span>
                <p class="text-sm font-semibold text-slate-300">Le test comprend plusieurs structures relationnelles et matrices analytiques.</p>
            </div>
            <div class="flex items-center gap-4 bg-slate-900/50 p-4 rounded-2xl border border-slate-800/60">
                <span class="w-8 h-8 rounded-xl bg-cyan-950 text-cyan-400 border border-cyan-500/20 flex items-center justify-center font-black text-sm shrink-0">3</span>
                <p class="text-sm font-semibold text-slate-300">Prenez votre temps, restez concentré et bonne traversée !</p>
            </div>
        </div>

        <div class="pt-4 space-y-4">
            <button onclick="declencherChargement()" class="w-full max-w-xl bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-600 hover:to-indigo-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-cyan-500/10 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-98">
                Lancer l'expédition 🏴‍☠️
            </button>
            <div>
                <a href="/projet_technologique/public/dashboard.php" class="text-xs font-bold text-slate-500 hover:text-slate-300 transition-colors">‹ Retour au Dashboard</a>
            </div>
        </div>
    </div>

    <div id="loading-screen" class="hidden max-w-md mx-auto text-center space-y-6 my-auto transition-all duration-300 opacity-0 transform scale-95">
        <div class="w-12 h-12 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin mx-auto shadow-md shadow-cyan-500/20"></div>
        <div class="space-y-1.5">
            <h3 class="text-md font-black text-white uppercase tracking-widest font-serif">Synchronisation du Log Pose...</h3>
            <p class="text-xs text-cyan-400/70 font-semibold animate-pulse">Calcul de la trajectoire vers la prochaine île</p>
        </div>
    </div>

    <div id="quiz-screen" class="hidden w-full opacity-0 transition-all duration-500 transform translate-y-4">
        <div class="fixed top-0 left-0 w-full bg-slate-900/80 backdrop-blur-md border-b border-slate-800/60 shadow-sm z-50 px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="text-xl">🏴‍☠️</span>
                <h1 class="text-md font-black text-white uppercase tracking-wider font-serif">Épreuve de Laugh Tale</h1>
            </div>
            <div id="timer-box" class="bg-slate-950 text-cyan-400 border border-cyan-500/20 font-mono font-black text-sm px-5 py-2.5 rounded-xl shadow-md">
                ⏳ <span id="timer-display">--:--</span>
            </div>
        </div>

        <main class="pt-28 pb-16 px-4 max-w-3xl mx-auto">
            <form action="/projet_technologique/submit_quiz.php" method="POST" id="quizForm" class="space-y-8" onsubmit="return validerFormulaire();">
                
                <input type="hidden" name="tentative_id" value="<?php echo $tentative_id; ?>">
                <input type="hidden" name="triche_detectee" id="tricheInput" value="0">

                <?php foreach ($questions as $index => $question): ?>
                    <div class="bg-slate-900/80 backdrop-blur-md p-6 md:p-8 rounded-3xl shadow-sm border border-slate-800 space-y-6">
                        <div class="flex items-start gap-4">
                            <span class="bg-cyan-950/60 text-cyan-400 border border-cyan-500/20 font-black text-xs px-3 py-1.5 rounded-xl uppercase tracking-wider shrink-0 mt-0.5">
                                Q. <?php echo $index + 1; ?>
                            </span>
                            <h2 class="text-lg font-black text-white leading-snug">
                                <?php echo htmlspecialchars($question['texte_question']); ?>
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <?php
                            $stmt_reponses = $pdo->prepare("SELECT id, texte_reponse FROM reponses WHERE question_id = ?");
                            $stmt_reponses->execute([$question['id']]);
                            $reponses = $stmt_reponses->fetchAll();
                            
                            foreach ($reponses as $reponse): 
                            ?>
                                <label class="relative flex items-center justify-between p-4 rounded-2xl border border-slate-800 hover:border-cyan-500/40 hover:bg-slate-800/20 hover:shadow-[0_0_15px_rgba(6,182,212,0.08)] cursor-pointer transition-all duration-300 group">
                                    <div class="flex items-center gap-4 z-10">
                                        <input type="radio" 
                                               name="reponses[<?php echo $question['id']; ?>]" 
                                               value="<?php echo $reponse['id']; ?>" 
                                               class="w-4 h-4 text-cyan-500 border-slate-700 bg-slate-950 focus:ring-cyan-500">
                                        <span class="text-slate-300 font-medium group-hover:text-white transition-colors duration-200"><?php echo htmlspecialchars($reponse['texte_reponse']); ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-600 hover:to-indigo-700 text-white font-bold px-10 py-4 rounded-2xl shadow-lg shadow-cyan-500/10 transition-all duration-300 transform active:scale-95">
                        Clôturer l'évaluation
                    </button>
                </div>
            </form>
        </main>
    </div>

    <footer class="w-full text-center py-6 text-xs text-slate-600 font-medium">
        © 2026 Laugh Tale — Projet Technologique Universitaire.
    </footer>

    <script>
        let timeLeft = <?php echo $temps_restant_secondes; ?>;
        let countdown;
        
        const prepScreen = document.getElementById('prep-screen');
        const loadingScreen = document.getElementById('loading-screen');
        const quizScreen = document.getElementById('quiz-screen');
        const timerDisplay = document.getElementById('timer-display');
        const timerBox = document.getElementById('timer-box');
        const form = document.getElementById('quizForm');

        // Logique d'animation transitionnelle (Idée 2)
        function declencherChargement() {
            // Étape A : Dissimulation fluide de l'accueil
            prepScreen.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                prepScreen.classList.add('hidden');
                loadingScreen.classList.remove('hidden');
                
                // Étape B : Apparition de l'animation de synchronisation
                setTimeout(() => {
                    loadingScreen.classList.remove('opacity-0', 'scale-95');
                    loadingScreen.classList.add('opacity-100', 'scale-100');
                }, 50);
                
                // Étape C : Attente de 1,5 seconde, puis affichage du test
                setTimeout(() => {
                    loadingScreen.classList.add('opacity-0', 'scale-95');
                    
                    setTimeout(() => {
                        loadingScreen.classList.add('hidden');
                        quizScreen.classList.remove('hidden');
                        
                        setTimeout(() => {
                            quizScreen.classList.remove('opacity-0', 'translate-y-4');
                            quizScreen.classList.add('opacity-100', 'translate-y-0');
                            
                            // Lancement final du chrono
                            updateTimer();
                            countdown = setInterval(updateTimer, 1000);
                        }, 50);
                    }, 300);
                }, 1500); // Durée de l'animation Log Pose
                
            }, 300);
        }

        function updateTimer() {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerDisplay.textContent = "00:00";
                form.submit();
                return;
            }
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
            if (timeLeft < 60) {
                timerBox.className = "bg-rose-950 text-rose-400 border border-rose-500/30 font-mono font-black text-sm px-5 py-2.5 rounded-xl shadow-md animate-pulse";
            }
            timeLeft--;
        }

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                document.getElementById('tricheInput').value = "1";
            }
        });

        function validerFormulaire() {
            if (timeLeft <= 0) return true;
            const reponsesCochees = document.querySelectorAll('input[type="radio"]:checked');
            if (reponsesCochees.length === 0) {
                alert("⚠️ Vous devez répondre au moins à une question !");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>