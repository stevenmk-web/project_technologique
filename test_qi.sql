-- Base de données : test_qi
-- Application : Test de QI
-- Groupe : Sokona Niakaté, Aissata Sissoko, Stiven Makela Muamba, Mohamadou Awalou

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS test_qi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE test_qi;

-- --------------------------------------------------------
-- Table : categories
-- --------------------------------------------------------
DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
  id INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (nom) VALUES
('Logique'),
('Culture générale'),
('Mathématiques');

-- --------------------------------------------------------
-- Table : utilisateurs
-- --------------------------------------------------------
DROP TABLE IF EXISTS utilisateurs;
CREATE TABLE utilisateurs (
  id INT NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  email VARCHAR(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  mot_de_passe VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  date_inscription TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table : questions
-- --------------------------------------------------------
DROP TABLE IF EXISTS questions;
CREATE TABLE questions (
  id INT NOT NULL AUTO_INCREMENT,
  categorie_id INT NOT NULL,
  texte_question TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (id),
  KEY categorie_id (categorie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO questions (categorie_id, texte_question) VALUES
(1, 'Quelle est la suite logique : 2, 4, 8, 16, ?'),
(1, 'Si A > B et B > C, alors qui est le plus grand ?'),
(1, 'Un train part à 8h et arrive à 11h30. Combien de temps dure le trajet ?'),
(1, 'Quel nombre vient après 3, 6, 12, 24 ?'),
(1, 'Marie a 3 sœurs. Chaque sœur a 1 frère. Combien y a-t-il d enfants en tout ?'),
(2, 'Quelle est la capitale de la France ?'),
(2, 'En quelle année a eu lieu la Révolution française ?'),
(2, 'Combien de continents y a-t-il sur Terre ?'),
(2, 'Qui a peint la Joconde ?'),
(2, 'Quelle est la planète la plus proche du Soleil ?'),
(3, 'Combien font 15% de 200 ?'),
(3, 'Quel est le carré de 12 ?'),
(3, 'Si x + 5 = 12, quelle est la valeur de x ?'),
(3, 'Combien font 7 × 8 ?'),
(3, 'Quel est le résultat de 144 ÷ 12 ?');

-- --------------------------------------------------------
-- Table : reponses
-- --------------------------------------------------------
DROP TABLE IF EXISTS reponses;
CREATE TABLE reponses (
  id INT NOT NULL AUTO_INCREMENT,
  question_id INT NOT NULL,
  texte_reponse TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
  est_correcte TINYINT(1) DEFAULT 0,
  PRIMARY KEY (id),
  KEY question_id (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO reponses (question_id, texte_reponse, est_correcte) VALUES
-- Q1
(1, '24', 0), (1, '32', 1), (1, '18', 0), (1, '64', 0),
-- Q2
(2, 'B', 0), (2, 'C', 0), (2, 'A', 1), (2, 'Impossible à dire', 0),
-- Q3
(3, '2h', 0), (3, '3h30', 1), (3, '3h', 0), (3, '4h', 0),
-- Q4
(4, '36', 0), (4, '48', 1), (4, '42', 0), (4, '30', 0),
-- Q5
(5, '4', 0), (5, '5', 0), (5, '6', 0), (5, '7', 1),
-- Q6
(6, 'Lyon', 0), (6, 'Paris', 1), (6, 'Marseille', 0), (6, 'Bordeaux', 0),
-- Q7
(7, '1789', 1), (7, '1799', 0), (7, '1776', 0), (7, '1804', 0),
-- Q8
(8, '5', 0), (8, '6', 0), (8, '7', 1), (8, '8', 0),
-- Q9
(9, 'Raphaël', 0), (9, 'Michel-Ange', 0), (9, 'Léonard de Vinci', 1), (9, 'Picasso', 0),
-- Q10
(10, 'Venus', 0), (10, 'Mercure', 1), (10, 'Mars', 0), (10, 'Terre', 0),
-- Q11
(11, '25', 0), (11, '30', 1), (11, '20', 0), (11, '35', 0),
-- Q12
(12, '124', 0), (12, '144', 1), (12, '132', 0), (12, '148', 0),
-- Q13
(13, '5', 0), (13, '7', 1), (13, '9', 0), (13, '12', 0),
-- Q14
(14, '54', 0), (14, '56', 1), (14, '48', 0), (14, '63', 0),
-- Q15
(15, '10', 0), (15, '12', 1), (15, '14', 0), (15, '11', 0);

-- --------------------------------------------------------
-- Table : tentatives
-- --------------------------------------------------------
DROP TABLE IF EXISTS tentatives;
CREATE TABLE tentatives (
  id INT NOT NULL AUTO_INCREMENT,
  utilisateur_id INT NOT NULL,
  categorie_id INT NOT NULL,
  date_debut TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  date_fin_prevue TIMESTAMP NULL DEFAULT NULL,
  date_fin_reelle TIMESTAMP NULL DEFAULT NULL,
  statut VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT 'en_cours',
  PRIMARY KEY (id),
  KEY utilisateur_id (utilisateur_id),
  KEY categorie_id (categorie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table : resultats
-- --------------------------------------------------------
DROP TABLE IF EXISTS resultats;
CREATE TABLE resultats (
  id INT NOT NULL AUTO_INCREMENT,
  tentative_id INT NOT NULL,
  score_qi INT NOT NULL,
  bonnes_reponses INT NOT NULL,
  total_questions INT NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY tentative_id (tentative_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table : historique_choix
-- --------------------------------------------------------
DROP TABLE IF EXISTS historique_choix;
CREATE TABLE historique_choix (
  id INT NOT NULL AUTO_INCREMENT,
  tentative_id INT NOT NULL,
  question_id INT NOT NULL,
  reponse_choisie_id INT NOT NULL,
  PRIMARY KEY (id),
  KEY tentative_id (tentative_id),
  KEY question_id (question_id),
  KEY reponse_choisie_id (reponse_choisie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Contraintes (clés étrangères)
-- --------------------------------------------------------
ALTER TABLE questions
  ADD CONSTRAINT questions_ibfk_1 FOREIGN KEY (categorie_id) REFERENCES categories (id) ON DELETE CASCADE;

ALTER TABLE reponses
  ADD CONSTRAINT reponses_ibfk_1 FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE;

ALTER TABLE tentatives
  ADD CONSTRAINT tentatives_ibfk_1 FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE,
  ADD CONSTRAINT tentatives_ibfk_2 FOREIGN KEY (categorie_id) REFERENCES categories (id) ON DELETE CASCADE;

ALTER TABLE resultats
  ADD CONSTRAINT resultats_ibfk_1 FOREIGN KEY (tentative_id) REFERENCES tentatives (id) ON DELETE CASCADE;

ALTER TABLE historique_choix
  ADD CONSTRAINT historique_choix_ibfk_1 FOREIGN KEY (tentative_id) REFERENCES tentatives (id) ON DELETE CASCADE,
  ADD CONSTRAINT historique_choix_ibfk_2 FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE,
  ADD CONSTRAINT historique_choix_ibfk_3 FOREIGN KEY (reponse_choisie_id) REFERENCES reponses (id) ON DELETE CASCADE;

COMMIT;
