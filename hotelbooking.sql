-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2026 at 07:48 PM
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
-- Database: `hotelbooking`
--
CREATE DATABASE IF NOT EXISTS `hotelbooking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hotelbooking`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Booked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_price`, `booking_date`, `status`) VALUES
(4, 2, 1, '2025-06-11', '2025-06-14', 10500.00, '2025-06-20 02:04:22', 'Completed'),
(5, 2, 6, '2025-06-20', '2025-06-21', 1500.00, '2025-06-20 02:12:00', 'Completed'),
(6, 7, 2, '2024-04-01', '2024-04-03', 2400.00, '2025-06-21 13:15:51', 'Completed'),
(7, 8, 4, '2024-04-10', '2024-04-13', 3000.00, '2025-06-21 13:15:51', 'Completed'),
(8, 3, 6, '2024-03-15', '2024-03-18', 6000.00, '2025-06-21 13:15:51', 'Completed'),
(9, 4, 8, '2024-02-05', '2024-02-07', 2000.00, '2025-06-21 13:15:51', 'Completed'),
(10, 5, 9, '2024-05-01', '2024-05-03', 4400.00, '2025-06-21 13:15:51', 'Completed'),
(11, 6, 11, '2024-01-10', '2024-01-12', 3200.00, '2025-06-21 13:15:51', 'Completed'),
(12, 7, 12, '2024-03-01', '2024-03-04', 4800.00, '2025-06-21 13:15:51', 'Completed'),
(13, 8, 14, '2024-01-25', '2024-01-27', 2800.00, '2025-06-21 13:15:51', 'Completed'),
(14, 3, 15, '2024-02-10', '2024-02-13', 4200.00, '2025-06-21 13:15:51', 'Completed'),
(15, 4, 17, '2024-06-01', '2024-06-03', 2600.00, '2025-06-21 13:15:51', 'Completed'),
(16, 5, 19, '2024-04-12', '2024-04-14', 2600.00, '2025-06-21 13:15:51', 'Completed'),
(17, 6, 20, '2024-05-20', '2024-05-22', 1800.00, '2025-06-21 13:15:51', 'Completed'),
(18, 7, 22, '2024-02-14', '2024-02-16', 2800.00, '2025-06-21 13:15:51', 'Completed'),
(19, 8, 24, '2024-03-03', '2024-03-06', 6000.00, '2025-06-21 13:15:51', 'Completed'),
(20, 3, 25, '2024-04-11', '2024-04-12', 800.00, '2025-06-21 13:15:51', 'Completed'),
(21, 4, 27, '2024-01-01', '2024-01-02', 1400.00, '2025-06-21 13:15:51', 'Completed'),
(22, 5, 1, '2024-05-05', '2024-05-07', 2400.00, '2025-06-21 13:15:51', 'Completed'),
(23, 2, 12, '2025-06-23', '2025-06-26', 6000.00, '2025-06-21 21:37:28', 'Completed'),
(24, 2, 4, '2025-06-23', '2025-06-25', 8400.00, '2025-06-22 21:40:53', 'Completed'),
(25, 9, 1, '2025-06-24', '2025-06-25', 3500.00, '2025-06-23 10:55:47', 'Booked'),
(26, 10, 1, '2025-06-25', '2025-06-30', 17500.00, '2025-06-23 11:32:36', 'Booked'),
(27, 2, 1, '2026-01-08', '2026-01-10', 7000.00, '2026-01-06 12:18:42', 'Booked');

-- --------------------------------------------------------

--
-- Table structure for table `contactmessages`
--

CREATE TABLE `contactmessages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `rating` float DEFAULT 0,
  `main_image_url` text DEFAULT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `name`, `location`, `description`, `rating`, `main_image_url`, `password`) VALUES
(1, 'Sunset Resort', 'Goa, Goa', 'A relaxing beachside resort in Goa.', 4.5, 'images (1).jpg', '123456'),
(2, 'Urban Stay', 'Mumbai, Maharastra', 'A modern stay in the heart of Mumbai.', 4, 'images.jpg', '123456'),
(3, 'Grand Palace Hotel', 'New Delhi, Delhi', 'Luxury hotel with world-class amenities.', 4.8, 'hotel_img.jpg', '123456'),
(4, 'Hotel Sun Flora', 'Bengluru, Karnataka', 'Luxury hotel with world-class amenities.', 4, 'photo-1561501900-3701fa6a0864.jpg', '123456'),
(6, 'Hotel Green Leaf', 'Manali, Himachal Pradesh', 'A cozy budget hotel nestled in the hills of Manali with mountain view rooms and basic amenities.', 4.2, 'manali_green.jpg', '123456'),
(7, 'Seaside Stay', 'Goa, Goa', 'Affordable beachside resort just 500m from Calangute Beach. Ideal for family and friends.', 4, 'sea_side.jpg', '123456'),
(8, 'Royal Comfort Inn', 'Jaipur, Rajasthan', 'Modern hotel in the heart of Jaipur offering traditional Rajasthani hospitality with all standard comforts.', 3.9, 'royal_jaipur.jpg', '123456'),
(9, 'Cityscape Lodge', 'Pune, Maharashtra', 'Clean and simple accommodation near the Pune Railway Station, perfect for short city stays.', 3.8, 'pune.jpg', '123456'),
(10, 'Tranquil Nest', 'Munnar, Kerala', 'Surrounded by lush tea gardens, Tranquil Nest provides a peaceful and affordable getaway.', 4.3, 'munnar.jpg', '123456'),
(11, 'Metro View Residency', 'New Delhi, Delhi', 'Budget hotel with metro access, ideal for travelers wanting to explore Delhi without spending much.', 3.7, 'hotel-metro-view.jpg', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_guests` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `available_count` int(11) DEFAULT 0,
  `status` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `hotel_id`, `room_type`, `price_per_night`, `max_guests`, `description`, `available_count`, `status`) VALUES
(1, 1, 'Deluxe Room', 3500.00, 2, 'Spacious deluxe room with sea view.', 2, 'Available'),
(2, 1, 'Suite', 5500.00, 4, 'Luxury suite with ocean-facing balcony.', 3, 'Available'),
(3, 2, 'Standard Room', 3000.00, 2, 'Comfortable room with city view.', 8, 'Unavailable'),
(4, 2, 'Executive Room', 4200.00, 3, 'Room with workspace and mini-bar.', 3, 'Available'),
(5, 3, 'Luxury Suite', 7000.00, 4, 'Elegant suite with private spa access.', 2, 'Unavailable'),
(6, 4, 'Standard Room', 1500.00, 2, 'Elegant suite with private spa access.', 9, 'Unavailable'),
(7, 6, 'Standard Room', 1200.00, 2, 'Basic room with double bed and mountain view.', 5, 'Unavailable'),
(8, 6, 'Deluxe Room', 1800.00, 3, 'Spacious room with balcony and heater for winters.', 3, 'Available'),
(9, 6, 'Family Room', 2400.00, 4, 'Perfect for families, includes two double beds.', 2, 'Available'),
(10, 7, 'Standard AC Room', 1500.00, 2, 'Air-conditioned room near the beach with complimentary breakfast.', 6, 'Unavailable'),
(11, 7, 'Non-AC Room', 1000.00, 2, 'Budget-friendly room with basic amenities.', 4, 'Available'),
(12, 7, 'Sea View Room', 2000.00, 3, 'Beach-facing room with a small balcony.', 1, 'Available'),
(13, 7, 'Family Suite', 2500.00, 4, 'Two-room suite ideal for families with kids.', 2, 'Unavailable'),
(14, 8, 'Standard Room', 1100.00, 2, 'Modern decor with comfortable bedding and Wi-Fi.', 5, 'Available'),
(15, 8, 'Deluxe Room', 1600.00, 3, 'Includes mini fridge and city view.', 3, 'Available'),
(16, 8, 'Suite Room', 2200.00, 4, 'Spacious suite with sofa seating and work desk.', 1, 'Unavailable'),
(17, 9, 'Budget Room', 900.00, 2, 'Ideal for solo travelers or short stays.', 6, 'Available'),
(18, 9, 'Standard Room', 1200.00, 2, 'Clean and functional room with TV and Wi-Fi.', 4, 'Available'),
(19, 9, 'Twin Room', 1400.00, 2, 'Two single beds, suitable for business travelers.', 3, 'Unavailable'),
(20, 9, 'Family Room', 2000.00, 4, 'Includes one double and two single beds.', 2, 'Available'),
(21, 10, 'Garden View Room', 1300.00, 2, 'Relaxing view of garden, includes breakfast.', 4, 'Available'),
(22, 10, 'Deluxe Room', 1700.00, 3, 'Balcony room with tea estate view.', 3, 'Available'),
(23, 10, 'Cottage', 2200.00, 4, 'Standalone cottage perfect for privacy.', 1, 'Unavailable'),
(24, 11, 'Single Room', 800.00, 1, 'Compact room for solo business travelers.', 5, 'Available'),
(25, 11, 'Double Room', 1200.00, 2, 'Best value with close metro access.', 6, 'Available'),
(26, 11, 'Family Room', 1800.00, 4, 'Spacious room with TV, Wi-Fi and wardrobe.', 3, 'Unavailable'),
(27, 11, 'AC Room', 1400.00, 2, 'Comfortable air-conditioned room with attached bath.', 4, 'Unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `profile_picture` text DEFAULT 'user.jpeg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `created_at`, `profile_picture`) VALUES
(1, 'admin', 'admin@gmail.com', '123456', '9876543210', '2025-06-20 00:55:49', 'user.jpeg'),;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `contactmessages`
--
ALTER TABLE `contactmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `contactmessages`
--
ALTER TABLE `contactmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `contactmessages`
--
ALTER TABLE `contactmessages`
  ADD CONSTRAINT `contactmessages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
