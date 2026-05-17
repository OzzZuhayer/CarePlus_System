-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 01:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+06:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_wt`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_message` text NOT NULL,
  `appointment_status` enum('Pending','Confirmed','Completed','Cancelled','No-Show') NOT NULL DEFAULT 'Pending',
  `appointment_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization_id` int(11) NOT NULL,
  `doctor_bio` text DEFAULT NULL,
  `doctor_fee` decimal(10,2) NOT NULL,
  `doctor_photo` varchar(255) NOT NULL,
  `doctor_availability` varchar(100) NOT NULL,
  `doctor_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `specialization_id`, `doctor_bio`, `doctor_fee`, `doctor_photo`, `doctor_availability`, `doctor_created_at`) VALUES
(1, 2, 1, 'Dr. Zero is an example doctor', 500.00, 'Asset/Public/Uploads/Doctors/default.png', 'Monday,Wednesday,Friday', '2026-05-15 20:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `specialization_id` int(11) NOT NULL,
  `specialization_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`specialization_id`, `specialization_name`) VALUES
(1, 'Cardiology'),
(5, 'Dermatology'),
(6, 'General Medicine'),
(3, 'Neurology'),
(2, 'Orthopedics'),
(4, 'Pediatrics');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(60) NOT NULL,
  `user_email` varchar(60) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` enum('Patient','Doctor','Admin') NOT NULL,
  `user_dob` date DEFAULT NULL,
  `user_bg` varchar(10) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `user_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `user_created_at` timestamp(1) NOT NULL DEFAULT current_timestamp(1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role`, `user_dob`, `user_bg`, `user_phone`, `user_is_active`, `user_created_at`) VALUES
(1, 'Admin Zero', 'admin@example.com', '$2y$10$w96ajN1.np4lJdoge8DEouKDM7yqxOHEhoxElqcLXnUxy5XXhWsZW', 'Admin', '0000-00-00', '', '', 1, '2026-05-14 07:32:48.0'),
(2, 'Dr. Zero', 'doctor@example.com', '$2y$10$w96ajN1.np4lJdoge8DEouKDM7yqxOHEhoxElqcLXnUxy5XXhWsZW', 'Doctor', '0000-00-00', '', '', 1, '2026-05-15 20:00:05.3'),
(3, 'Patient Zero', 'patient@example.com', '$2y$10$w96ajN1.np4lJdoge8DEouKDM7yqxOHEhoxElqcLXnUxy5XXhWsZW', 'Patient', '1995-08-20', 'B+', '01700000003', 1, '2026-05-15 20:00:05.3');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_user_id` (`patient_id`),
  ADD KEY `doctor_doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `doctor_specialization_id` (`specialization_id`),
  ADD KEY `doctor_user_id` (`user_id`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`specialization_id`),
  ADD UNIQUE KEY `specialization_name` (`specialization_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `specialization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `doctor_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_user_id` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctor_specialization_id` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`specialization_id`),
  ADD CONSTRAINT `doctor_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
