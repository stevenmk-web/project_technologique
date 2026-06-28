<?php
// config/db.php

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'test_qi';
$username = 'root'; // Par défaut sur XAMPP / WAMP / MAMP
$password = '';     // Par défaut vide sur XAMPP (sur MAMP ou Mac, parfois 'root')

try {
    // Création de la connexion PDO avec des options de sécurité
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            // Active le mode d'erreur sous forme d'exceptions pour pouvoir les attraper
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Retourne les résultats sous forme de tableau associatif (clé => valeur)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Désactive la simulation des requêtes préparées pour plus de sécurité contre les injections SQL
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // Si la connexion échoue, on arrête le script et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}