-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 19 juin 2025 à 13:18
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `g10a_users_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `temperatures`
--

CREATE TABLE `temperatures` (
  `id` int(11) NOT NULL,
  `valeurs` float DEFAULT NULL,
  `date_temp` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `temperatures`
--

INSERT INTO `temperatures` (`id`, `valeurs`, `date_temp`) VALUES
(1, 15, '2025-06-18'),
(2, 25, '2025-06-18'),
(3, 22, '2025-06-18'),
(4, 18, '2025-06-18'),
(5, 19, '2025-06-18'),
(6, 10, '2025-06-18');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `role`, `verification_token`, `verified_at`, `is_verified`) VALUES
(1, 'Kam', 'Jade', 'test@mail.com', '$2y$10$Gm3Z566tRLrCMQc.DjswaONRm5tSmropsbeQf6Flx2emR0Xg3.ITG', 'user', NULL, NULL, 0),
(2, 'L', 'Eloise', 'user1@gmail.com', '$2y$10$3cTKqhTRsSulkkOhDVK5AOtbBOf3OVY34IiDFWhV5H4sYh/7LF5B.', 'user', NULL, NULL, 0),
(3, 'D', 'Khaleb', 'user2@gmail.com', '$2y$10$.OydwcDV0klbZ9PrUPH4ue961j4QIv9PQxLZ1IBeBiIqzdGivOR8q', 'user', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `vitesse_moteur`
--

CREATE TABLE `vitesse_moteur` (
  `id` int(11) NOT NULL,
  `vitesse_rmp` float DEFAULT NULL,
  `date_vitesse` date DEFAULT curdate(),
  `heure` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vitesse_moteur`
--

INSERT INTO `vitesse_moteur` (`id`, `vitesse_rmp`, `date_vitesse`, `heure`) VALUES
(1, 100, '2025-06-18', '15:13:42'),
(2, 200, '2025-06-18', '11:30:00');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `temperatures`
--
ALTER TABLE `temperatures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Index pour la table `vitesse_moteur`
--
ALTER TABLE `vitesse_moteur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `temperatures`
--
ALTER TABLE `temperatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vitesse_moteur`
--
ALTER TABLE `vitesse_moteur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
