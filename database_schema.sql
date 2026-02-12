-- =====================================================
-- ToxTrak - Appointment Management System
-- Database Schema
-- Author: Shakeel Khalid
-- =====================================================

-- Create database (if needed)
-- CREATE DATABASE IF NOT EXISTS toxtrak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE toxtrak;

-- =====================================================
-- Table: clients
-- Description: Stores client/user information
-- =====================================================
CREATE TABLE IF NOT EXISTS `clients` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `gender` ENUM('male', 'female', 'other') NOT NULL,
    `date_of_birth` DATE NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `emergency_phone_number` VARCHAR(20) NOT NULL,
    `address` TEXT DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `clients_email_unique` (`email`),
    KEY `idx_clients_name` (`first_name`, `last_name`),
    KEY `idx_clients_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: appointments
-- Description: Stores main appointment records
-- =====================================================
CREATE TABLE IF NOT EXISTS `appointments` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` INT(11) UNSIGNED NOT NULL,
    `appointment_type` ENUM('auto', 'manual') NOT NULL DEFAULT 'auto',
    `total_appointments` INT(11) NOT NULL,
    `total_appointment_weeks` INT(11) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_appointments_client` (`client_id`),
    KEY `idx_appointments_type` (`appointment_type`),
    KEY `idx_appointments_created` (`created_at`),
    CONSTRAINT `fk_appointments_client` 
        FOREIGN KEY (`client_id`) 
        REFERENCES `clients` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: appointment_details
-- Description: Stores weekly appointment schedules
-- =====================================================
CREATE TABLE IF NOT EXISTS `appointment_details` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `appointment_id` INT(11) UNSIGNED NOT NULL,
    `week_start_date` DATE NOT NULL,
    `week_end_date` DATE NOT NULL,
    `monday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `tuesday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `wednesday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `thursday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `friday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `saturday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `sunday_appointment` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_appointment_details_appointment` (`appointment_id`),
    KEY `idx_appointment_details_dates` (`week_start_date`, `week_end_date`),
    CONSTRAINT `fk_appointment_details_appointment` 
        FOREIGN KEY (`appointment_id`) 
        REFERENCES `appointments` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample Data (Optional - for testing)
-- =====================================================

-- Insert sample clients
INSERT INTO `clients` (
    `first_name`, 
    `last_name`, 
    `email`, 
    `gender`, 
    `date_of_birth`, 
    `phone_number`, 
    `emergency_phone_number`, 
    `address`, 
    `note`
) VALUES 
(
    'John', 
    'Doe', 
    'john.doe@example.com', 
    'male', 
    '1990-05-15', 
    '+1-555-0101', 
    '+1-555-0102', 
    '123 Main Street, New York, NY 10001', 
    'Regular client, prefers morning appointments'
),
(
    'Jane', 
    'Smith', 
    'jane.smith@example.com', 
    'female', 
    '1985-08-22', 
    '+1-555-0201', 
    '+1-555-0202', 
    '456 Oak Avenue, Los Angeles, CA 90001', 
    'New client, referred by Dr. Johnson'
),
(
    'Michael', 
    'Johnson', 
    'michael.j@example.com', 
    'male', 
    '1992-03-10', 
    '+1-555-0301', 
    '+1-555-0302', 
    NULL, 
    NULL
);

-- =====================================================
-- Indexes Summary
-- =====================================================
-- clients:
--   - PRIMARY KEY (id)
--   - UNIQUE (email)
--   - INDEX (first_name, last_name)
--   - INDEX (email)
--
-- appointments:
--   - PRIMARY KEY (id)
--   - INDEX (client_id)
--   - INDEX (appointment_type)
--   - INDEX (created_at)
--   - FOREIGN KEY (client_id) -> clients(id)
--
-- appointment_details:
--   - PRIMARY KEY (id)
--   - INDEX (appointment_id)
--   - INDEX (week_start_date, week_end_date)
--   - FOREIGN KEY (appointment_id) -> appointments(id)

-- =====================================================
-- Foreign Key Constraints
-- =====================================================
-- ON DELETE CASCADE: When a client is deleted, all their appointments 
--                    and appointment details are automatically deleted
--
-- ON UPDATE CASCADE: When a client/appointment ID is updated, 
--                    the references are automatically updated

-- =====================================================
-- Notes
-- =====================================================
-- 1. All tables use InnoDB engine for transaction support
-- 2. UTF8MB4 charset for full Unicode support (including emojis)
-- 3. Timestamps automatically track creation and modification times
-- 4. TINYINT(1) used for boolean values (0 = false, 1 = true)
-- 5. ENUM types for gender and appointment_type ensure data integrity
-- 6. Indexes added for frequently queried columns to improve performance
-- 7. Unique constraint on email prevents duplicate client registrations
-- 8. Cascade delete ensures referential integrity
