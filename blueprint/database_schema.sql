-- CosmicLib MySQL Database Blueprint
-- Target: MySQL 8.0+ / MariaDB 10.4+
-- Bahasa Kolom: English

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users & RBAC
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) UNIQUE NOT NULL, -- e.g. 'admin', 'librarian', 'student', 'teacher'
    `label` VARCHAR(100) NOT NULL, -- e.g. 'Administrator', 'Pustakawan'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `role_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Library Core
CREATE TABLE `members` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `member_number` VARCHAR(50) UNIQUE NOT NULL, -- NISN for students, NIP for teachers
    `type` ENUM('student', 'teacher', 'staff') NOT NULL,
    `phone` VARCHAR(20) NULL,
    `class_name` VARCHAR(50) NULL, -- Class for students (e.g. 'X IPA 1')
    `status` ENUM('active', 'suspended', 'inactive') DEFAULT 'active',
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `books` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `isbn` VARCHAR(20) UNIQUE NULL,
    `author` VARCHAR(255) NOT NULL,
    `publisher` VARCHAR(255) NOT NULL,
    `publish_year` INT UNSIGNED NOT NULL,
    `ddc_classification` VARCHAR(20) NULL, -- Dewey Decimal Classification (e.g., 300, 800)
    `description` TEXT NULL,
    `cover_image` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `book_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `book_id` BIGINT UNSIGNED NOT NULL,
    `barcode_id` VARCHAR(100) UNIQUE NOT NULL, -- Individual asset tag barcode
    `shelf_location` VARCHAR(100) NULL, -- e.g., 'A1', 'B3'
    `status` ENUM('available', 'borrowed', 'damaged', 'lost') DEFAULT 'available',
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Circulation & Fines
CREATE TABLE `borrow_records` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `book_item_id` BIGINT UNSIGNED NOT NULL,
    `librarian_out_id` BIGINT UNSIGNED NOT NULL, -- User ID of the librarian who checked it out
    `librarian_in_id` BIGINT UNSIGNED NULL, -- User ID of the librarian who checked it in
    `borrow_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `return_date` DATE NULL,
    `status` ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed',
    FOREIGN KEY (`member_id`) REFERENCES `members`(`id`),
    FOREIGN KEY (`book_item_id`) REFERENCES `book_items`(`id`),
    FOREIGN KEY (`librarian_out_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`librarian_in_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fines` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `borrow_record_id` BIGINT UNSIGNED NOT NULL,
    `fine_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `status` ENUM('unpaid', 'partially_paid', 'paid') DEFAULT 'unpaid',
    `payment_date` DATE NULL,
    FOREIGN KEY (`borrow_record_id`) REFERENCES `borrow_records`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Settings
CREATE TABLE `settings` (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT NULL,
    `type` VARCHAR(50) DEFAULT 'string'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
