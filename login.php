<?php
// server/login.php

// 1. Démarrer la session pour connecter l'utilisateur
session_start();

// 2. Inclure la connexion à la base de données (Chemin propre depuis le dossier server/)
require_once __DIR__ . '/../config/db.php';

// 3. Vérifier que le formulaire de connexion a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Récupérer et nettoyer les saisies
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

    // 5. Validation rapide des champs
    if (empty($email) || empty($mot_de_passe)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header('Location: /projet_technologique/public/index.php');
        exit();
    }

    try {
        // 6. Chercher l'utilisateur avec son email
        $stmt = $pdo->prepare("SELECT id, nom, email, mot_de_passe FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 7. Vérifier si l'utilisateur existe et si le mot de passe correspond au hash
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            
            // Sécurité : On régénère l'ID de session pour éviter la fixation de session
            session_regenerate_id(true);

            // 8. Stocker les variables de session (L'utilisateur est connecté !)
            $_SESSION['user_id'] = intval($user['id']);
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];

            // Redirection absolue vers l'espace personnel
            header('Location: /projet_technologique/public/dashboard.php');
            exit();
            
        } else {
            $_SESSION['error'] = "Identifiants incorrects.";
            header('Location: /projet_technologique/public/index.php');
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Une erreur technique est survenue.";
        header('Location: /projet_technologique/public/index.php');
        exit();
    }

} else {
    header('Location: /projet_technologique/public/index.php');
    exit();
}