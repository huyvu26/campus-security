-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 12:14 PM
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
-- Database: `campus_security`
--

-- --------------------------------------------------------

--
-- Table structure for table `duty`
--

CREATE TABLE `duty` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `duty_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `duty`
--

INSERT INTO `duty` (`id`, `staff_id`, `duty_date`, `start_time`, `end_time`, `place`, `assigned_by`) VALUES
(19, 5, '2025-05-21', '07:00:00', '16:00:00', 'Building B08', NULL),
(20, 5, '2025-05-26', '07:00:00', '16:00:00', 'Gate B', NULL),
(21, 5, '2025-05-28', '07:00:00', '17:00:00', 'Gate A', NULL),
(22, 8, '2025-05-29', '12:00:00', '13:00:00', 'B08', NULL),
(23, 6, '2025-06-02', '06:00:00', '16:00:00', 'Gate A', NULL),
(24, 5, '2025-06-02', '07:00:00', '17:00:00', 'Building B03', NULL),
(25, 5, '2025-06-03', '07:00:00', '17:00:00', 'Building B08', NULL),
(26, 5, '2025-06-04', '07:00:00', '17:00:00', 'Building B10', NULL),
(27, 9, '2025-06-04', '07:00:00', '17:00:00', 'Building B08', NULL),
(28, 6, '2025-06-06', '07:00:00', '17:00:00', 'Canteen', NULL),
(29, 5, '2025-06-13', '07:00:00', '17:00:00', 'Main Gate', NULL),
(30, 5, '2025-06-14', '07:00:00', '16:30:00', 'Building B11', NULL),
(31, 5, '2025-06-15', '07:00:00', '17:00:00', 'Canteen', NULL),
(32, 6, '2025-06-12', '07:00:00', '16:00:00', 'Parking Lot', NULL),
(33, 7, '2025-06-11', '07:00:00', '17:00:00', 'Canteen', NULL),
(34, 8, '2025-06-11', '07:00:00', '17:00:00', 'Side Gate', NULL),
(35, 9, '2025-06-11', '07:00:00', '17:00:00', 'Library', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_request`
--

CREATE TABLE `leave_request` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `leave_date` date DEFAULT NULL,
  `type` enum('Leave','Overtime') DEFAULT NULL,
  `status` enum('Pending','Approved','Declined') DEFAULT 'Pending',
  `reason` text DEFAULT NULL,
  `action_by_manager_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_request`
--

INSERT INTO `leave_request` (`id`, `staff_id`, `leave_date`, `type`, `status`, `reason`, `action_by_manager_id`) VALUES
(34, 6, '2025-06-07', 'Leave', 'Approved', 'Sickness', NULL),
(35, 5, '2025-06-05', 'Leave', 'Approved', 'Sickness', NULL),
(36, 5, '2025-06-06', 'Leave', 'Approved', 'Sickness', NULL),
(43, 5, '2025-06-16', 'Overtime', 'Approved', '', NULL),
(44, 5, '2025-06-12', 'Overtime', 'Approved', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int(11) NOT NULL,
  `identity_number` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id`, `identity_number`, `name`, `email`, `password`, `gender`, `dob`) VALUES
(2, '001', 'Huy Vu', 'huyvu@gmail.com', '$2y$10$d83hAdhA7jmlP4qV6YXfFOKhT3rR9RJHDuERrDxjvNEiIiH0LAkhy', 'male', '2004-01-26'),
(3, '007', 'Vo Le', 'vole@gmail.com', '$2y$10$W80XupZuKrCzojsDsPjXvO6g3f6gq8Mvs.0XgzlGPS.KeN0LQ0pT.', 'male', '1988-11-04');

-- --------------------------------------------------------

--
-- Table structure for table `security_staff`
--

CREATE TABLE `security_staff` (
  `id` int(11) NOT NULL,
  `identity_number` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_staff`
--

INSERT INTO `security_staff` (`id`, `identity_number`, `name`, `email`, `password`, `gender`, `dob`, `manager_id`) VALUES
(5, '002', 'Anh Pham', 'anhpham@gmail.com', '$2y$10$Q2Z.wpRW6kF.oKfVh8UM2uT4QFCLiPXuqN7PJLCi4SOXDYW0eLPiK', 'male', '2001-01-01', 2),
(6, '003', 'Thu Nguyen', 'thunguyen@gmail.com', '$2y$10$Z4cNiCNSTEH6LF8.9.QK0en3L8i7t8qIGmbvwjm8qAptvpHT7Fd.e', 'female', '2004-03-29', 2),
(7, '004', 'Tien  Phan', 'tienphan@gmail.com', '$2y$10$9wqueRe5MWjE.vNMYMvhsO4lcUEZk8MA0E9hCyge5BMjMgqmTmXUi', 'male', '1999-12-31', 3),
(8, '005', 'Ngoc Ha', 'ngocha@gmail.com', '$2y$10$tIHWI0.iP08q/ZRQRYmGsu3QoQ9zIRcfYKT4l6Dm9CkKnzcSJuVQq', 'male', '2025-05-29', 3),
(9, '006', 'Dai Nguyen', 'dainguyen@gmail.com', '$2y$10$5vRyQcDasTyQFT6YrouKHe7iHl9ajPtha2EySuLis38bEhczTFKkq', 'male', '1990-06-04', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `duty`
--
ALTER TABLE `duty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `fk_duty_assigned_by` (`assigned_by`);

--
-- Indexes for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `fk_leave_action_by` (`action_by_manager_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `identity_number` (`identity_number`);

--
-- Indexes for table `security_staff`
--
ALTER TABLE `security_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `identity_number` (`identity_number`),
  ADD KEY `fk_staff_manager` (`manager_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `duty`
--
ALTER TABLE `duty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `leave_request`
--
ALTER TABLE `leave_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `security_staff`
--
ALTER TABLE `security_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `duty`
--
ALTER TABLE `duty`
  ADD CONSTRAINT `duty_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `security_staff` (`id`),
  ADD CONSTRAINT `fk_duty_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `manager` (`id`);

--
-- Constraints for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD CONSTRAINT `fk_leave_action_by` FOREIGN KEY (`action_by_manager_id`) REFERENCES `manager` (`id`),
  ADD CONSTRAINT `leave_request_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `security_staff` (`id`);

--
-- Constraints for table `security_staff`
--
ALTER TABLE `security_staff`
  ADD CONSTRAINT `fk_staff_manager` FOREIGN KEY (`manager_id`) REFERENCES `manager` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
