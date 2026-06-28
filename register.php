<?php
// server/register.php

// 1. Démarrer la session pour pouvoir stocker des messages d'erreur ou de succès
session_start();

// 2. Inclure le fichier de connexion à la base de données
// On utilise dirname(__DIR__) pour s'assurer que le chemin vers config/db.php reste correct
require_once dirname(__DIR__) . '/config/db.php';

// 3. Vérifier que le formulaire a bien été soumis en méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Récupérer et nettoyer les données (trim supprime les espaces inutiles en début/fin)
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

    // 5. Validation de base des champs
    if (empty($nom) || empty($email) || empty($mot_de_passe)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header('Location: ../public/index.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "L'adresse email n'est pas valide.";
        header('Location: ../public/index.php');
        exit();
    }

    try {
        // 6. Vérifier si l'email existe déjà dans la table `utilisateurs`
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Cette adresse email est déjà enregistrée.";
            header('Location: ../public/index.php');
            exit();
        }

        // 7. Sécuriser le mot de passe (Hachage BCRYPT fort)
        $password_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        // 8. Insérer le nouvel utilisateur dans la base de données
        $insert = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
        $insert->execute([$nom, $email, $password_hash]);

        // 9. Succès ! On redirige avec un message positif
        $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        header('Location: ../public/index.php');
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur SQL, on stocke un message générique pour ne pas afficher d'infos sensibles
        $_SESSION['error'] = "Une erreur technique est survenue. Veuillez réessayer.";
        header('Location: ../public/index.php');
        exit();
    }

} else {
    // Si quelqu'un essaie d'accéder au fichier directement sans passer par le formulaire
    header('Location: ../public/index.php');
    exit();
}