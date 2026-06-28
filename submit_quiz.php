<?php
// server/submit_quiz.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /projet_technologique/public/index.php');
    exit();
}

require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $tentative_id = isset($_POST['tentative_id']) ? intval($_POST['tentative_id']) : 0;
    $reponses_utilisateurs = isset($_POST['reponses']) ? $_POST['reponses'] : []; 
    $triche_detectee = isset($_POST['triche_detectee']) ? intval($_POST['triche_detectee']) : 0;

    if ($tentative_id === 0) {
        header('Location: /projet_technologique/public/dashboard.php');
        exit();
    }

    try {
        // 1. Récupérer les infos de la tentative (colonne : categorie_id)
        $stmt_tentative = $pdo->prepare("SELECT date_fin_prevue, statut, categorie_id FROM tentatives WHERE id = ?");
        $stmt_tentative->execute([$tentative_id]);
        $tentative = $stmt_tentative->fetch();

        if (!$tentative || $tentative['statut'] !== 'en_cours') {
            header('Location: /projet_technologique/public/dashboard.php');
            exit();
        }

        $maintenant = date('Y-m-d H:i:s');

        // 2. Sécurité anti-triche ou dépassement de temps
        if ($triche_detectee === 1 || strtotime($maintenant) > (strtotime($tentative['date_fin_prevue']) + 10)) {
            $update_triche = $pdo->prepare("UPDATE tentatives SET statut = 'abandon', date_fin_reelle = ? WHERE id = ?");
            $update_triche->execute([$maintenant, $tentative_id]);

            $insert_resultat = $pdo->prepare("INSERT INTO resultats (tentative_id, score_qi, bonnes_reponses, total_questions) VALUES (?, 70, 0, 10)");
            $insert_resultat->execute([$tentative_id]);

            header('Location: /projet_technologique/public/dashboard.php');
            exit();
        }

        // 3. Récupérer le nombre total réel de questions (colonne : categorie_id)
        $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE categorie_id = ?");
        $stmt_total->execute([$tentative['categorie_id']]);
        $vrai_total_questions = intval($stmt_total->fetchColumn());
        if ($vrai_total_questions === 0) $vrai_total_questions = 10;

        // 4. Calcul des scores et historique
        $bonnes_reponses_count = 0;
        $stmt_check = $pdo->prepare("SELECT est_correcte FROM reponses WHERE id = ? AND question_id = ?");
        $stmt_hist = $pdo->prepare("INSERT INTO historique_choix (tentative_id, question_id, reponse_choisie_id) VALUES (?, ?, ?)");

        foreach ($reponses_utilisateurs as $question_id => $reponse_id) {
            $question_id = intval($question_id);
            $reponse_id = intval($reponse_id);

            $stmt_hist->execute([$tentative_id, $question_id, $reponse_id]);

            $stmt_check->execute([$reponse_id, $question_id]);
            $reponse_info = $stmt_check->fetch();

            if ($reponse_info && intval($reponse_info['est_correcte']) === 1) {
                $bonnes_reponses_count++;
            }
        }

        // 5. Algorithme de calcul du QI
        $score_qi = 70 + (($bonnes_reponses_count / $vrai_total_questions) * 60);

        // 6. Sauvegarder les résultats
        $insert_res = $pdo->prepare("INSERT INTO resultats (tentative_id, score_qi, bonnes_reponses, total_questions) VALUES (?, ?, ?, ?)");
        $insert_res->execute([$tentative_id, intval($score_qi), $bonnes_reponses_count, $vrai_total_questions]);

        // 7. Clôturer la tentative
        $update_tentative = $pdo->prepare("UPDATE tentatives SET statut = 'termine', date_fin_reelle = ? WHERE id = ?");
        $update_tentative->execute([$maintenant, $tentative_id]);

        header('Location: /projet_technologique/public/dashboard.php');
        exit();

    } catch (PDOException $e) {
        die("Erreur lors de la validation : " . $e->getMessage());
    }
}