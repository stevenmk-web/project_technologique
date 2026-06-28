<?php
// server/start_quiz.php
session_start();

// Protection : si l'utilisateur n'est pas connecté, retour à l'accueil
if (!isset($_SESSION['user_id'])) {
    header('Location: /projet_technologique/public/index.php');
    exit();
}

require_once __DIR__ . '/../config/db.php';

$user_id = intval($_SESSION['user_id']);
$categorie_id = 1; // Notre catégorie "Test de QI Général" insérée en BDD

try {
    $date_debut = date('Y-m-d H:i:s');
    // On ajoute exactement 15 minutes pour le compte à rebours
    $date_fin_prevue = date('Y-m-d H:i:s', strtotime('+15 minutes')); 

    // 1. Insérer la nouvelle tentative en cours
    $stmt = $pdo->prepare("
        INSERT INTO tentatives (utilisateur_id, categorie_id, date_debut, date_fin_prevue, statut) 
        VALUES (?, ?, ?, ?, 'en_cours')
    ");
    $stmt->execute([$user_id, $categorie_id, $date_debut, $date_fin_prevue]);
    
    // 2. Récupérer l'ID unique de cette tentative
    $nouvelle_tentative_id = $pdo->lastInsertId();

    // 3. Stocker cet ID en session pour que quiz.php sache quelle tentative traiter
    $_SESSION['active_tentative_id'] = $nouvelle_tentative_id;

    // 4. Redirection vers la page du quiz
    header('Location: /projet_technologique/public/quiz.php');
    exit();

} catch (PDOException $e) {
    die("Erreur technique lors du démarrage du quiz : " . $e->getMessage());
}