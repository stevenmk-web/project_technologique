<?php
// server/logout.php
session_start();

// Vider toutes les variables de session
$_SESSION = array();

// Détruire le cookie de session dans le navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session côté serveur
session_destroy();

// Rediriger vers la page d'accueil/connexion
header("Location: ../public/index.php");
exit();