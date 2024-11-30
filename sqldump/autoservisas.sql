-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 30, 2024 at 09:00 PM
-- Server version: 5.7.44
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autoservisas`
--

-- --------------------------------------------------------

--
-- Table structure for table `Automobolis`
--

CREATE TABLE `Automobolis` (
  `id` int(11) NOT NULL,
  `metai_nuo` int(11) NOT NULL,
  `metai_iki` int(11) NOT NULL,
  `marke` varchar(20) COLLATE utf8_lithuanian_ci NOT NULL,
  `modelis` varchar(20) COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Automobolis`
--

INSERT INTO `Automobolis` (`id`, `metai_nuo`, `metai_iki`, `marke`, `modelis`) VALUES
(1, 2004, 2012, 'Škoda', 'Octavia');

-- --------------------------------------------------------

--
-- Table structure for table `DUK`
--

CREATE TABLE `DUK` (
  `duk_id` int(11) NOT NULL,
  `klausimas` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `atsakymas` text COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `DUK`
--

INSERT INTO `DUK` (`duk_id`, `klausimas`, `atsakymas`) VALUES
(1, 'Ar galima atsiskaityti kortele?', 'Ne, atsiskaitymas tik grynaisiais, kadangi slepiame mokesčius.'),
(2, 'Ar taisymui taikomas garantinis laikotarpis?', 'Kiekvienam taisymui taikomas 3 mėnesių garantinis laikotarpis, tačiau negarantuojame, kad jūsų automobilis išvažiuos su visomis dalimis, kuriomis atvažiavo pas mus.');

-- --------------------------------------------------------

--
-- Table structure for table `MeistrasPaslaugos`
--

CREATE TABLE `MeistrasPaslaugos` (
  `id` int(11) NOT NULL,
  `meistro_id` int(11) NOT NULL,
  `paslaugos_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MeistrasPaslaugos`
--

INSERT INTO `MeistrasPaslaugos` (`id`, `meistro_id`, `paslaugos_id`) VALUES
(1, 2, 1),
(2, 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Naudotojai`
--

CREATE TABLE `Naudotojai` (
  `naudotojo_id` int(11) NOT NULL,
  `vardas` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `el_pastas` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `slaptazodis` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `telefono_numeris` varchar(20) COLLATE utf8_lithuanian_ci DEFAULT NULL,
  `vaidmuo` enum('klientas','meistras','vadybininkas') COLLATE utf8_lithuanian_ci NOT NULL,
  `aprasymas` text COLLATE utf8_lithuanian_ci NOT NULL,
  `nuotrauka` text COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Naudotojai`
--

INSERT INTO `Naudotojai` (`naudotojo_id`, `vardas`, `el_pastas`, `slaptazodis`, `telefono_numeris`, `vaidmuo`, `aprasymas`, `nuotrauka`) VALUES
(1, 'Jonas', 'jonas@example.com', '$2y$10$b3lI6q6gvEcczBJ249juV.y7cois8bcz.R2fjh6ocQ2STLizlPe2e', '+37060000000', 'klientas', '', ''),
(2, 'Jonas', 'jonas1@example.com', '$2y$10$GH6gPO8/LUOlt3FTIYxcAeTzdHA3u16XqfL01jlYZYvexQKSDmAWS', '+37060000000', 'meistras', 'Pats geriausias meistrelis', 'https://www.shutterstock.com/image-photo/young-african-american-mechanic-working-600nw-2099017543.jpg'),
(4, 'Petras', 'Petras@meistas.lt', 'Password123!', '+37065793412', 'meistras', '', 'https://www.shutterstock.com/shutterstock/photos/1711144648/display_1500/stock-photo-portrait-shot-of-a-handsome-mechanic-working-on-a-vehicle-in-a-car-service-professional-repairman-1711144648.jpg'),
(13, 'Rokas', 'rokas@admin.com', '$2y$10$GH6gPO8/LUOlt3FTIYxcAeTzdHA3u16XqfL01jlYZYvexQKSDmAWS', '+37065750867', 'klientas', '', ''),
(14, 'Vadybininkas', 'vadybininkas@administracija.lt', '$2y$10$CLY9E6z0UznDWz5gnmMfZ.rERdB7JhiT0APz1rcumICLZZu9QbdXa', '+37058137412', 'vadybininkas', '', ''),
(15, 'Meistrelis', 'meistrelis@meistriukas.com', '$2y$10$RVQd0E9rC/F3yp/E3jFb9uZTuCeirKZ8XgLTBTimh1fqMgkGoRESW', '+35192857', 'meistras', '', 'https://s1.dmcdn.net/v/APIOe1RmGx23XEPO-/x1080'),
(16, 'Pranas', 'pranelis@gmail.com', '$2y$10$9mtLwKL9RagL3Fpn/s0V0uOvfqZr4/09GElYrbNdgY/IKCje2GjYm', '+37065969999', 'meistras', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `Paslaugos`
--

CREATE TABLE `Paslaugos` (
  `paslaugos_id` int(11) NOT NULL,
  `paslaugos_pavadinimas` varchar(255) COLLATE utf8_lithuanian_ci NOT NULL,
  `aprasymas` text COLLATE utf8_lithuanian_ci,
  `kaina` decimal(10,2) NOT NULL,
  `nuotrauka` text COLLATE utf8_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Paslaugos`
--

INSERT INTO `Paslaugos` (`paslaugos_id`, `paslaugos_pavadinimas`, `aprasymas`, `kaina`, `nuotrauka`) VALUES
(1, 'Diagnostika', 'Standartinė automobilio diagnostika', 50.00, 'https://www.ivi-rmainnovation.com/wp-content/uploads/2020/05/Diagnostic-test_II-ok.jpg'),
(2, 'EGR atjungimas', 'Atjungsim egr ir bus ramus gyvenimas', 99.00, 'https://www.autura.lt/uploads/news/5eb402eb25679207566587.jpg'),
(3, 'Variklio perrinkimas', 'Bim bam parenkam surenkam nurenkam ir yra', 1000.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRwxlRi1P9dhS4fP2Ru4uvqWdFuohiXgt2CTA&s'),
(4, 'Automobilio išrinkimas dalimis', 'Renkam, renkam ir išrenkam', 1500.00, 'https://i0.wp.com/www.mendmotor.com/wp-content/uploads/2024/01/Parts-Of-Car-Diagram-with-Name.webp'),
(5, 'Vairo keitimas', 'Pakeisim vairą pagal jūsų poreikius', 50.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `Prieinamumas`
--

CREATE TABLE `Prieinamumas` (
  `prieinamumo_id` int(11) NOT NULL,
  `meistro_id` int(11) NOT NULL,
  `diena` date NOT NULL,
  `laikas` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Prieinamumas`
--

INSERT INTO `Prieinamumas` (`prieinamumo_id`, `meistro_id`, `diena`, `laikas`) VALUES
(1, 2, '2024-11-09', '11:00:00'),
(2, 2, '2024-11-22', '14:00:00'),
(3, 2, '2024-11-15', '11:00:00'),
(4, 2, '2024-11-15', '11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `Ratings`
--

CREATE TABLE `Ratings` (
  `rating_id` int(11) NOT NULL,
  `meistro_id` int(11) NOT NULL,
  `kliento_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Ratings`
--

INSERT INTO `Ratings` (`rating_id`, `meistro_id`, `kliento_id`, `rating`) VALUES
(1, 2, 14, 2),
(2, 15, 14, 1),
(3, 2, 13, 5);

-- --------------------------------------------------------

--
-- Table structure for table `Rezervacijos`
--

CREATE TABLE `Rezervacijos` (
  `rezervacijos_id` int(11) NOT NULL,
  `kliento_id` int(11) NOT NULL,
  `meistro_id` int(11) NOT NULL,
  `paslaugos_id` int(11) NOT NULL,
  `rezervacijos_data` date NOT NULL,
  `rezervacijos_laikas` time NOT NULL,
  `automobilis_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_lithuanian_ci;

--
-- Dumping data for table `Rezervacijos`
--

INSERT INTO `Rezervacijos` (`rezervacijos_id`, `kliento_id`, `meistro_id`, `paslaugos_id`, `rezervacijos_data`, `rezervacijos_laikas`, `automobilis_id`) VALUES
(1, 1, 2, 1, '2024-11-09', '12:00:00', 1),
(9, 1, 2, 1, '2024-11-09', '13:00:00', 1),
(16, 1, 4, 1, '2024-11-16', '11:00:00', 1),
(17, 13, 2, 1, '2024-11-14', '10:00:00', 1),
(18, 13, 4, 1, '2024-11-23', '11:00:00', 1),
(19, 2, 4, 1, '2024-11-22', '12:00:00', 1),
(20, 2, 2, 1, '2024-11-16', '11:00:00', 1),
(22, 2, 2, 1, '2024-11-16', '12:00:00', 1),
(23, 14, 2, 2, '2024-11-08', '11:00:00', 1),
(24, 2, 15, 2, '2024-11-08', '10:00:00', 1),
(25, 14, 2, 2, '2024-11-01', '11:00:00', 1),
(27, 14, 2, 5, '2024-11-06', '09:00:00', 1),
(28, 14, 15, 5, '2024-11-01', '11:00:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Automobolis`
--
ALTER TABLE `Automobolis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `DUK`
--
ALTER TABLE `DUK`
  ADD PRIMARY KEY (`duk_id`);

--
-- Indexes for table `MeistrasPaslaugos`
--
ALTER TABLE `MeistrasPaslaugos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meistro_id` (`meistro_id`),
  ADD KEY `paslaugos_id` (`paslaugos_id`);

--
-- Indexes for table `Naudotojai`
--
ALTER TABLE `Naudotojai`
  ADD PRIMARY KEY (`naudotojo_id`),
  ADD UNIQUE KEY `el_pastas` (`el_pastas`);

--
-- Indexes for table `Paslaugos`
--
ALTER TABLE `Paslaugos`
  ADD PRIMARY KEY (`paslaugos_id`);

--
-- Indexes for table `Prieinamumas`
--
ALTER TABLE `Prieinamumas`
  ADD PRIMARY KEY (`prieinamumo_id`),
  ADD KEY `meistro_id` (`meistro_id`);

--
-- Indexes for table `Ratings`
--
ALTER TABLE `Ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `meistro_id` (`meistro_id`),
  ADD KEY `kliento_id` (`kliento_id`);

--
-- Indexes for table `Rezervacijos`
--
ALTER TABLE `Rezervacijos`
  ADD PRIMARY KEY (`rezervacijos_id`),
  ADD KEY `kliento_id` (`kliento_id`),
  ADD KEY `meistro_id` (`meistro_id`),
  ADD KEY `paslaugos_id` (`paslaugos_id`),
  ADD KEY `Rezervacijos_ibfk_4` (`automobilis_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Automobolis`
--
ALTER TABLE `Automobolis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `DUK`
--
ALTER TABLE `DUK`
  MODIFY `duk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `MeistrasPaslaugos`
--
ALTER TABLE `MeistrasPaslaugos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Naudotojai`
--
ALTER TABLE `Naudotojai`
  MODIFY `naudotojo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Paslaugos`
--
ALTER TABLE `Paslaugos`
  MODIFY `paslaugos_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Prieinamumas`
--
ALTER TABLE `Prieinamumas`
  MODIFY `prieinamumo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Ratings`
--
ALTER TABLE `Ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Rezervacijos`
--
ALTER TABLE `Rezervacijos`
  MODIFY `rezervacijos_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `MeistrasPaslaugos`
--
ALTER TABLE `MeistrasPaslaugos`
  ADD CONSTRAINT `meistro_id` FOREIGN KEY (`meistro_id`) REFERENCES `Naudotojai` (`naudotojo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `paslaugos_id` FOREIGN KEY (`paslaugos_id`) REFERENCES `Paslaugos` (`paslaugos_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Prieinamumas`
--
ALTER TABLE `Prieinamumas`
  ADD CONSTRAINT `Prieinamumas_ibfk_1` FOREIGN KEY (`meistro_id`) REFERENCES `Naudotojai` (`naudotojo_id`);

--
-- Constraints for table `Ratings`
--
ALTER TABLE `Ratings`
  ADD CONSTRAINT `Ratings_ibfk_1` FOREIGN KEY (`meistro_id`) REFERENCES `Naudotojai` (`naudotojo_id`),
  ADD CONSTRAINT `Ratings_ibfk_2` FOREIGN KEY (`kliento_id`) REFERENCES `Naudotojai` (`naudotojo_id`);

--
-- Constraints for table `Rezervacijos`
--
ALTER TABLE `Rezervacijos`
  ADD CONSTRAINT `Rezervacijos_ibfk_1` FOREIGN KEY (`kliento_id`) REFERENCES `Naudotojai` (`naudotojo_id`),
  ADD CONSTRAINT `Rezervacijos_ibfk_2` FOREIGN KEY (`meistro_id`) REFERENCES `Naudotojai` (`naudotojo_id`),
  ADD CONSTRAINT `Rezervacijos_ibfk_3` FOREIGN KEY (`paslaugos_id`) REFERENCES `Paslaugos` (`paslaugos_id`),
  ADD CONSTRAINT `Rezervacijos_ibfk_4` FOREIGN KEY (`automobilis_id`) REFERENCES `Automobolis` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
