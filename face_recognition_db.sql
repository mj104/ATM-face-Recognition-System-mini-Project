-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 07:10 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `face_recognition_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `card_no` varchar(50) NOT NULL,
  `type` enum('withdraw','deposit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `card_no`, `type`, `amount`, `balance`, `created_at`) VALUES
(6, '12345678', 'withdraw', '1000.00', '9000.00', '2025-03-24 16:10:55'),
(7, '12345678', 'deposit', '1000000.00', '1009000.00', '2025-03-24 16:11:03'),
(8, '12345678', 'withdraw', '50000.00', '959000.00', '2025-05-07 07:04:00'),
(9, '12345678', 'deposit', '10.00', '959010.00', '2025-05-07 07:04:09'),
(10, '123456789', 'withdraw', '1000.00', '9000.00', '2025-05-07 07:07:02'),
(11, '123456789', 'deposit', '500.00', '9500.00', '2025-05-07 07:07:10'),
(12, '123456789', 'withdraw', '500.00', '9000.00', '2025-05-07 07:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `account_no` varchar(20) NOT NULL,
  `initial_amount` decimal(10,2) NOT NULL,
  `card_no` varchar(16) NOT NULL,
  `pin` varchar(6) NOT NULL,
  `face_image` text DEFAULT NULL,
  `face_descriptor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `account_no`, `initial_amount`, `card_no`, `pin`, `face_image`, `face_descriptor`) VALUES
(15, 'Mayank pratap Singh', '63908079567', '1234567890', '959010.00', '12345678', '1234', 'images/67e183c52ec0c.png', NULL),
(16, 'Mayank pratap Singh', '63908079567', '23456789', '9000.00', '123456789', '1111', 'images/681b066ba03e5.png', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_no` (`card_no`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_no` (`account_no`),
  ADD UNIQUE KEY `card_no` (`card_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`card_no`) REFERENCES `users` (`card_no`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
