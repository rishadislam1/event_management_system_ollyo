-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 07:32 PM
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
-- Database: `event_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendees`
--

CREATE TABLE `attendees` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`id`, `event_id`, `user_id`, `registered_at`) VALUES
(1, 67, 12, '2025-02-01 05:26:55'),
(2, 59, 12, '2025-02-01 05:28:08'),
(3, 59, 12, '2025-02-01 05:28:47'),
(4, 58, 12, '2025-02-01 05:34:06'),
(5, 59, 12, '2025-02-01 05:34:16');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `max_capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `max_capacity`, `created_at`, `username`) VALUES
(1, 'Music Concert', 'A lively music event with various artists.', 500, '2025-01-31 06:00:00', 'rishad'),
(2, 'Tech Conference', 'A conference on the latest tech trends.', 300, '2025-01-30 04:00:00', 'ollyo'),
(3, 'Art Exhibition', 'An exhibition showcasing modern art.', 150, '2025-01-29 03:30:00', 'rishadislam'),
(4, 'Food Festival', 'A festival celebrating food from different cultures.', 200, '2025-01-28 12:00:00', 'rishad'),
(5, 'Charity Run', 'A charity run to raise funds for the community.', 1000, '2025-01-27 01:00:00', 'ollyo'),
(6, 'Business Workshop', 'A workshop on business strategies and growth.', 50, '2025-01-26 08:00:00', 'rishadislam'),
(7, 'Wedding Reception', 'A grand celebration of a wedding.', 300, '2025-01-25 14:00:00', 'ollyo'),
(8, 'Tech Hackathon', 'A competitive event for tech enthusiasts.', 150, '2025-01-24 02:30:00', 'rishad'),
(9, 'Yoga Retreat', 'A relaxing yoga retreat in the countryside.', 100, '2025-01-23 03:00:00', 'rishadislam'),
(10, 'Film Festival', 'A film festival featuring indie movies.', 200, '2025-01-22 04:30:00', 'ollyo'),
(11, 'Comedy Show', 'A stand-up comedy event featuring top comedians.', 250, '2025-01-21 13:00:00', 'rishadislam'),
(12, 'Food Cooking Class', 'A cooking class focused on gourmet cuisine.', 20, '2025-01-20 09:00:00', 'ollyo'),
(13, 'Startup Pitch', 'A pitching event for startup businesses.', 100, '2025-01-19 07:00:00', 'rishad'),
(14, 'Fashion Show', 'A glamorous fashion event showcasing new designers.', 500, '2025-01-18 10:00:00', 'rishadislam'),
(15, 'Book Launch', 'The launch of a new bestselling novel.', 100, '2025-01-17 05:00:00', 'ollyo'),
(16, 'Networking Event', 'A networking event for professionals and entrepreneurs.', 200, '2025-01-16 11:30:00', 'rishad'),
(17, 'Craft Fair', 'A fair displaying handcrafted items and goods.', 150, '2025-01-15 04:00:00', 'rishadislam'),
(18, 'Poetry Night', 'An open-mic poetry night for writers and enthusiasts.', 50, '2025-01-14 12:00:00', 'ollyo'),
(19, 'Music Festival', 'A large music festival featuring multiple genres.', 5000, '2025-01-13 10:00:00', 'rishad'),
(20, 'Cultural Parade', 'A parade celebrating cultural diversity and traditions.', 800, '2025-01-12 08:00:00', 'rishadislam'),
(21, 'Gastronomy Event', 'A celebration of the finest culinary delights.', 100, '2025-01-11 06:00:00', 'ollyo'),
(22, 'Tech Expo', 'An exhibition of the latest gadgets and technologies.', 400, '2025-01-10 04:30:00', 'rishadislam'),
(23, 'Business Summit', 'A summit bringing together business leaders and innovators.', 350, '2025-01-09 07:00:00', 'rishad'),
(24, 'Photography Workshop', 'A workshop on advanced photography techniques.', 30, '2025-01-08 03:00:00', 'ollyo'),
(25, 'Startup Networking', 'A networking event for aspiring entrepreneurs.', 200, '2025-01-07 10:00:00', 'rishadislam'),
(26, 'Charity Gala', 'A formal event to raise funds for a good cause.', 250, '2025-01-06 13:30:00', 'ollyo'),
(27, 'Coding Bootcamp', 'A bootcamp for learning software development in 6 weeks.', 25, '2025-01-05 02:00:00', 'rishad'),
(28, 'Adventure Trip', 'An adventurous group trip to the mountains.', 20, '2025-01-04 01:00:00', 'rishadislam'),
(29, 'Public Speaking Course', 'A course on becoming a confident public speaker.', 50, '2025-01-03 04:00:00', 'ollyo'),
(30, 'Film Screening', 'A special screening of a popular film.', 200, '2025-01-02 13:00:00', 'rishadislam'),
(31, 'Craft Workshop', 'A hands-on workshop teaching various crafting techniques.', 15, '2025-01-01 06:00:00', 'rishad'),
(32, 'Innovation Talk', 'A talk on new innovations in the tech industry.', 100, '2024-12-31 09:00:00', 'ollyo'),
(33, 'Cooking Competition', 'A competitive cooking event with multiple chefs.', 50, '2024-12-30 08:00:00', 'rishadislam'),
(34, 'Startup Expo', 'An expo showcasing startup businesses and their products.', 300, '2024-12-29 03:00:00', 'rishad'),
(35, 'Environmental Rally', 'A rally to raise awareness about environmental issues.', 500, '2024-12-28 05:00:00', 'rishadislam'),
(36, 'Health Conference', 'A conference focusing on health and wellness trends.', 250, '2024-12-27 04:00:00', 'ollyo'),
(37, 'Pet Adoption Event', 'An event promoting pet adoption and welfare.', 100, '2024-12-26 07:00:00', 'rishad'),
(38, 'Tech Bootcamp', 'A bootcamp for learning the latest tech skills.', 20, '2024-12-25 03:30:00', 'ollyo'),
(39, 'Motivational Talk', 'A talk by a motivational speaker to inspire attendees.', 150, '2024-12-24 10:00:00', 'rishadislam'),
(40, 'Christmas Party', 'A holiday party to celebrate Christmas.', 400, '2024-12-23 12:00:00', 'rishad'),
(41, 'Fitness Challenge', 'A fitness competition to push personal limits.', 50, '2024-12-22 01:00:00', 'ollyo'),
(42, 'Charity Auction', 'An auction event to raise money for charity.', 100, '2024-12-21 09:00:00', 'rishadislam'),
(43, 'TEDx Talk', 'A TEDx event with several engaging speakers.', 250, '2024-12-20 13:30:00', 'ollyo'),
(44, 'Creative Writing Workshop', 'A workshop to improve creative writing skills.', 30, '2024-12-19 04:00:00', 'rishad'),
(45, 'Startup Accelerator', 'An accelerator program for new startups.', 50, '2024-12-18 06:00:00', 'rishadislam'),
(46, 'AI Conference', 'A conference discussing artificial intelligence and its applications.', 500, '2024-12-17 03:00:00', 'ollyo'),
(47, 'Gaming Tournament', 'A competitive gaming event with various games.', 200, '2024-12-16 11:00:00', 'rishad'),
(48, 'Wine Tasting', 'An event where guests can sample different wines.', 80, '2024-12-15 12:00:00', 'rishadislam'),
(49, 'E-Sports Championship', 'An e-sports competition for professional gamers.', 1000, '2024-12-14 08:00:00', 'ollyo'),
(50, 'Photography Contest', 'A photography competition with a focus on landscapes.', 200, '2024-12-13 04:00:00', 'rishad'),
(51, 'Fashion Design Workshop', 'A workshop on the fundamentals of fashion design.', 25, '2024-12-12 03:30:00', 'rishadislam'),
(52, 'test', 'test', 10, '2025-01-31 16:27:15', 'rishadislam'),
(54, 'Forth Event', 'Test 4', 2, '2025-01-31 16:51:51', 'rishadislam'),
(55, 'Fifth Evevnt', 'Ollyo2', 50, '2025-01-31 16:52:23', 'rishadislam'),
(56, 'Six Event', 'Ollyo3', 23, '2025-01-31 16:52:59', 'rishadislam'),
(58, 'Six Event', 'Ollyo4', 23, '2025-01-31 16:53:47', 'rishadislam'),
(59, 'Six Event', 'Ollyo1', 23, '2025-01-31 16:53:58', 'rishadislam'),
(67, 'New Event', 'Ollyo', 1, '2025-02-01 05:16:10', 'rishadislam');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ip_address` text DEFAULT NULL,
  `attempt_time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(12, 'rishadislam', 'rishad.islam158@gmail.com', '$2y$10$GoDGKtrJmCvM3jKKK4Wslua/KJwQedq1qDJjX0yHgc73uyXxBDILa', '2025-01-31 06:16:44', 'admin'),
(13, 'ollyo', 'info@ollyo.com', '$2y$10$fFMfKCwcddarAEOrK2G9we6.scfDjn23PIOY3NKGjfezyF9KGJlaa', '2025-02-01 15:51:00', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendees`
--
ALTER TABLE `attendees`
  ADD CONSTRAINT `attendees_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `attendees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
