-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 05:21 PM
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
-- Table structure for table `iss_comments`
--

CREATE TABLE `iss_comments` (
  `id` int(11) NOT NULL,
  `per_id` int(11) NOT NULL,
  `iss_id` int(11) NOT NULL,
  `short_comment` varchar(255) NOT NULL,
  `long_comment` text NOT NULL,
  `posted_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iss_comments`
--

INSERT INTO `iss_comments` (`id`, `per_id`, `iss_id`, `short_comment`, `long_comment`, `posted_date`) VALUES
(1, 3, 1, 'ddd', 'ddd', '2025-04-16'),
(2, 3, 1, 'fssf', 'sfgfsgfs', '2025-04-16'),
(3, 4, 1, 'xdgxdg', 'gxdgdxfx', '2025-04-16'),
(4, 4, 3, 'yyytutyyt', 'yuyu', '2025-04-16');

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

-- --------------------------------------------------------

--
-- Table structure for table `iss_persons`
--

CREATE TABLE `iss_persons` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pwd_hash` varchar(255) NOT NULL,
  `pwd_salt` varchar(255) NOT NULL,
  `admin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iss_persons`
--

INSERT INTO `iss_persons` (`id`, `fname`, `lname`, `mobile`, `email`, `pwd_hash`, `pwd_salt`, `admin`) VALUES
(2, 'apple', 'appleee', '000-000-0000', 'apple@apple.com', '5eb07a0c9cf76dc1d21fd0c8561116fe', '40fc59dc0706e1faea0fec1619d82789', 'n'),
(3, 'Dylan', 'Wiza', '000-000-0001', 'wizadylan@gmail.com', '73ab5fc818cb24f15ed5e9ec7f3d3794', 'e94985cb2cd77a48ca0660c076b96159', 'y'),
(4, 'ss', 'dzssdz', '000-000-0005', 'dszsz@fgdfd.com', '5cb078e342d8e1bff84e0252f4bb5560', '72d4b39e894da3ca0174c1e4433e6fb7', 'n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `iss_comments`
--
ALTER TABLE `iss_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `iss_issues`
--
ALTER TABLE `iss_issues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `iss_persons`
--
ALTER TABLE `iss_persons`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `iss_comments`
--
ALTER TABLE `iss_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `iss_issues`
--
ALTER TABLE `iss_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `iss_persons`
--
ALTER TABLE `iss_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
