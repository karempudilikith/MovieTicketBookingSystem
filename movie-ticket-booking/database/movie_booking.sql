-- Movie Ticket Booking Management System Database Script
-- This script creates the database and all required tables with sample data.

-- 1. Create Database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `movie_booking_db_new`;
USE `movie_booking_db_new`;

-- Automatically clean up existing tables if they exist before creating new ones
-- This prevents issues if you import this file multiple times on top of an existing database
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `shows`;
DROP TABLE IF EXISTS `movies`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;


-- 2. Create Users Table
-- Stores customer and administrator profiles
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'user', -- 'admin' or 'user'
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Movies Table
-- Stores details about movies registered in the system
CREATE TABLE IF NOT EXISTS `movies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `genre` VARCHAR(100) NOT NULL,
  `language` VARCHAR(50) NOT NULL,
  `duration` INT NOT NULL, -- Duration in minutes
  `release_date` DATE NOT NULL,
  `description` TEXT NOT NULL,
  `poster` VARCHAR(255) NOT NULL, -- Filename of poster (stored in images/ folder)
  `ticket_price` DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create Shows Table
-- Maps movies to theatres, show dates, and times
CREATE TABLE IF NOT EXISTS `shows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `movie_id` INT NOT NULL,
  `theatre_name` VARCHAR(100) NOT NULL,
  `show_time` TIME NOT NULL,
  `show_date` DATE NOT NULL,
  FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Create Bookings Table
-- Stores seat booking records
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `show_id` INT NOT NULL,
  `seats` VARCHAR(255) NOT NULL, -- Comma-separated seats (e.g. 'A1,A2,A3')
  `total_price` DECIMAL(10,2) NOT NULL,
  `booking_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Booked', -- 'Booked', 'Cancelled'
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Insert Default Users
-- Default Admin: admin@gmail.com / admin123
-- Default User: user@gmail.com / user123
-- Hashed passwords are created using PHP's standard password_hash() with PASSWORD_DEFAULT.
-- 'admin123' hash: $2y$10$oX1y85Q.fWbB5P09HUXjDu.5Q7gE4QO5yM9s6ZkE3JbZt41.rZtN.
-- 'user123' hash: $2y$10$Mee.695xW4l5VwV5v4u1GeaL61uYg1k.K/ZkG9z1Q3H.l9N9pP6E.
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'System Administrator', 'admin@gmail.com', '$2y$10$oX1y85Q.fWbB5P09HUXjDu.5Q7gE4QO5yM9s6ZkE3JbZt41.rZtN.', 'admin'),
(2, 'John Doe', 'user@gmail.com', '$2y$10$Mee.695xW4l5VwV5v4u1GeaL61uYg1k.K/ZkG9z1Q3H.l9N9pP6E.', 'user');

-- 7. Insert Sample Movies
INSERT INTO `movies` (`id`, `title`, `genre`, `language`, `duration`, `release_date`, `description`, `poster`, `ticket_price`) VALUES
(1, 'Interstellar', 'Sci-Fi, Adventure', 'English', 169, '2014-11-07', 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.', 'interstellar.jpg', 150.00),
(2, 'Inception', 'Action, Sci-Fi', 'English', 148, '2010-07-16', 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 'inception.jpg', 120.00),
(3, 'The Dark Knight', 'Action, Crime, Drama', 'English', 152, '2008-07-18', 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 'dark_knight.jpg', 180.00);

-- 8. Insert Sample Shows
INSERT INTO `shows` (`id`, `movie_id`, `theatre_name`, `show_time`, `show_date`) VALUES
(1, 1, 'Screen 1 - PVR Cinemas', '14:30:00', CURDATE()),
(2, 1, 'Screen 1 - PVR Cinemas', '18:00:00', CURDATE()),
(3, 2, 'IMAX - Cinepolis', '15:00:00', CURDATE()),
(4, 2, 'IMAX - Cinepolis', '21:00:00', CURDATE() + INTERVAL 1 DAY),
(5, 3, 'Screen 2 - INOX', '11:00:00', CURDATE()),
(6, 3, 'Screen 2 - INOX', '19:30:00', CURDATE() + INTERVAL 1 DAY);
