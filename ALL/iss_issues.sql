-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 05:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cis355`
--

-- --------------------------------------------------------

--
-- Table structure for table `iss_issues`
--

CREATE TABLE `iss_issues` (
  `id` int(11) NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `long_description` text NOT NULL,
  `open_date` date NOT NULL,
  `close_date` date DEFAULT NULL,
  `priority` varchar(255) NOT NULL,
  `org` varchar(255) NOT NULL,
  `project` varchar(255) NOT NULL,
  `per_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iss_issues`
--

INSERT INTO `iss_issues` (`id`, `short_description`, `long_description`, `open_date`, `close_date`, `priority`, `org`, `project`, `per_id`) VALUES
(2, 'fxfx', 'xfxfxf', '2025-04-09', '2025-04-10', 'Low', '', '', 4),
(3, 'fdxdxdxfrrrrrrrrrrrrrrrrrrrrr', 'rrrrrrrrrrrrrrrrrrrrrrrrrrr', '2025-04-18', '2025-04-17', 'Medium', '', '', 4),
(4, 'aaa', 'aaa', '2025-04-08', '2025-04-17', 'Medium', '', '', 3),
(5, 'fdxdxdxfrrrrrrrrrrrrrrrrrrrrr', 'hfg', '2025-04-02', NULL, 'High', '', '', 3),
(6, 'qq', 'qq', '2025-04-24', NULL, 'Medium', '', '', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `iss_issues`
--
ALTER TABLE `iss_issues`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `iss_issues`
--
ALTER TABLE `iss_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
