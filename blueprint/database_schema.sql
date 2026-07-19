-- ============================================================
-- CosmicLib Engine — Master Database Blueprint
-- ============================================================
-- Target   : MySQL 8.0+ / MariaDB 10.4+
-- Charset  : utf8mb4_unicode_ci
-- Language : English (columns, tables) / Bahasa Indonesia (UI labels)
-- Version  : 1.1.0
-- Updated  : 2026-07-19
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. USERS & RBAC
-- ============================================================

CREATE TABLE `users` (
    `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`              VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL,
    `email_verified_at` TIMESTAMP NULL,
    `password`          VARCHAR(255) NOT NULL,
    `avatar`            VARCHAR(255) NULL,
    `phone`             VARCHAR(20) NULL,
    `locale`            VARCHAR(10) DEFAULT 'id',
    `timezone`          VARCHAR(50) DEFAULT 'Asia/Jakarta',
    `is_active`         BOOLEAN DEFAULT TRUE,
    `last_login_at`     TIMESTAMP NULL,
    `last_login_ip`     VARCHAR(45) NULL,
    `remember_token`    VARCHAR(100) NULL,
    `two_factor_secret` TEXT NULL,
    `two_factor_confirmed_at` TIMESTAMP NULL,
    `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`        TIMESTAMP NULL,
    UNIQUE INDEX `users_email_unique` (`email`),
    INDEX `users_is_active_index` (`is_active`),
    INDEX `users_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(50) NOT NULL,         -- e.g. 'admin', 'librarian', 'student', 'teacher'
    `label`       VARCHAR(100) NOT NULL,        -- e.g. 'Administrator', 'Pustakawan'
    `description` VARCHAR(255) NULL,
    `is_system`   BOOLEAN DEFAULT FALSE,        -- system roles cannot be deleted
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,        -- e.g. 'library.books.view'
    `label`       VARCHAR(255) NOT NULL,        -- e.g. 'Lihat Buku'
    `module`      VARCHAR(50) NOT NULL,         -- e.g. 'library', 'system'
    `group`       VARCHAR(50) NULL,             -- e.g. 'books', 'circulation'
    `description` VARCHAR(255) NULL,
    `is_system`   BOOLEAN DEFAULT FALSE,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE INDEX `permissions_name_unique` (`name`),
    INDEX `permissions_module_index` (`module`),
    INDEX `permissions_group_index` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
    `role_id`       BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `role_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. MODULES
-- ============================================================

CREATE TABLE `modules` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,        -- e.g. 'Library', 'CMS'
    `slug`        VARCHAR(100) NOT NULL,        -- e.g. 'library', 'cms'
    `description` VARCHAR(500) NULL,
    `version`     VARCHAR(20) DEFAULT '1.0.0',
    `path`        VARCHAR(255) NULL,            -- filesystem path
    `is_active`   BOOLEAN DEFAULT TRUE,
    `is_system`   BOOLEAN DEFAULT FALSE,        -- system modules cannot be disabled
    `priority`    INT DEFAULT 0,                -- boot order
    `settings`    JSON NULL,                    -- module-specific settings
    `installed_at` TIMESTAMP NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `modules_slug_unique` (`slug`),
    INDEX `modules_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. MENUS
-- ============================================================

CREATE TABLE `menus` (
    `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id`     BIGINT UNSIGNED NULL,
    `module`        VARCHAR(50) NULL,           -- source module slug
    `title`         VARCHAR(100) NOT NULL,      -- display label (Bahasa Indonesia)
    `icon`          VARCHAR(50) NULL,           -- icon class e.g. 'bi-book'
    `route`         VARCHAR(255) NULL,          -- named route
    `url`           VARCHAR(255) NULL,          -- fallback URL if no named route
    `permission`    VARCHAR(100) NULL,          -- required permission name
    `position`      INT DEFAULT 0,             -- sort order
    `is_active`     BOOLEAN DEFAULT TRUE,
    `badge_type`    VARCHAR(20) NULL,           -- 'count', 'label', null
    `badge_value`   VARCHAR(50) NULL,
    `target`        VARCHAR(20) DEFAULT '_self',
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE,
    INDEX `menus_module_index` (`module`),
    INDEX `menus_position_index` (`position`),
    INDEX `menus_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. THEMES
-- ============================================================

CREATE TABLE `themes` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL,
    `description` VARCHAR(500) NULL,
    `version`     VARCHAR(20) DEFAULT '1.0.0',
    `author`      VARCHAR(100) NULL,
    `path`        VARCHAR(255) NULL,
    `is_active`   BOOLEAN DEFAULT FALSE,       -- only one active at a time
    `is_default`  BOOLEAN DEFAULT FALSE,
    `settings`    JSON NULL,                   -- theme tokens, color overrides
    `screenshot`  VARCHAR(255) NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `themes_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. SETTINGS (Key-Value Store)
-- ============================================================

CREATE TABLE `settings` (
    `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`        VARCHAR(100) NOT NULL,
    `value`      TEXT NULL,
    `type`       VARCHAR(30) DEFAULT 'string', -- string, integer, boolean, json, text
    `group`      VARCHAR(50) DEFAULT 'general',-- general, school, smtp, library, etc.
    `label`      VARCHAR(255) NULL,            -- human-readable label
    `is_public`  BOOLEAN DEFAULT FALSE,        -- visible in frontend?
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `settings_key_unique` (`key`),
    INDEX `settings_group_index` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. MEDIA
-- ============================================================

CREATE TABLE `media` (
    `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `model_type`     VARCHAR(255) NULL,         -- polymorphic: App\Models\Book, etc.
    `model_id`       BIGINT UNSIGNED NULL,
    `collection`     VARCHAR(100) DEFAULT 'default',
    `filename`       VARCHAR(255) NOT NULL,
    `original_name`  VARCHAR(255) NOT NULL,
    `mime_type`      VARCHAR(100) NOT NULL,
    `disk`           VARCHAR(50) DEFAULT 'public',
    `path`           VARCHAR(500) NOT NULL,
    `size`           BIGINT UNSIGNED DEFAULT 0, -- bytes
    `metadata`       JSON NULL,                 -- dimensions, alt text, etc.
    `uploaded_by`    BIGINT UNSIGNED NULL,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     TIMESTAMP NULL,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `media_model_index` (`model_type`, `model_id`),
    INDEX `media_collection_index` (`collection`),
    INDEX `media_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. NOTIFICATIONS
-- ============================================================

CREATE TABLE `notifications` (
    `id`              CHAR(36) PRIMARY KEY,     -- UUID
    `type`            VARCHAR(255) NOT NULL,    -- notification class
    `notifiable_type` VARCHAR(255) NOT NULL,    -- polymorphic
    `notifiable_id`   BIGINT UNSIGNED NOT NULL,
    `data`            JSON NOT NULL,
    `channel`         VARCHAR(50) DEFAULT 'database', -- database, mail, whatsapp
    `read_at`         TIMESTAMP NULL,
    `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `notifications_notifiable_index` (`notifiable_type`, `notifiable_id`),
    INDEX `notifications_read_at_index` (`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. AUDIT & ACTIVITY LOGS
-- ============================================================

CREATE TABLE `audit_logs` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     BIGINT UNSIGNED NULL,
    `action`      VARCHAR(50) NOT NULL,        -- create, update, delete, login, logout, etc.
    `model_type`  VARCHAR(255) NULL,           -- affected model class
    `model_id`    BIGINT UNSIGNED NULL,
    `old_values`  JSON NULL,
    `new_values`  JSON NULL,
    `ip_address`  VARCHAR(45) NULL,
    `user_agent`  VARCHAR(500) NULL,
    `url`         VARCHAR(500) NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `audit_logs_user_id_index` (`user_id`),
    INDEX `audit_logs_model_index` (`model_type`, `model_id`),
    INDEX `audit_logs_action_index` (`action`),
    INDEX `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_logs` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     BIGINT UNSIGNED NULL,
    `description` VARCHAR(500) NOT NULL,       -- human-readable activity
    `module`      VARCHAR(50) NULL,
    `properties`  JSON NULL,                   -- extra context data
    `ip_address`  VARCHAR(45) NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `activity_logs_user_id_index` (`user_id`),
    INDEX `activity_logs_module_index` (`module`),
    INDEX `activity_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. WIDGETS
-- ============================================================

CREATE TABLE `widgets` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,       -- widget identifier
    `title`       VARCHAR(255) NOT NULL,       -- display title
    `module`      VARCHAR(50) NULL,            -- source module
    `component`   VARCHAR(255) NOT NULL,       -- view component path
    `position`    INT DEFAULT 0,
    `is_active`   BOOLEAN DEFAULT TRUE,
    `permission`  VARCHAR(100) NULL,           -- required permission
    `settings`    JSON NULL,                   -- widget config (filter, limit, period)
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `widgets_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. PLUGINS
-- ============================================================

CREATE TABLE `plugins` (
    `id`           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`         VARCHAR(100) NOT NULL,
    `slug`         VARCHAR(100) NOT NULL,
    `description`  VARCHAR(500) NULL,
    `version`      VARCHAR(20) DEFAULT '1.0.0',
    `author`       VARCHAR(100) NULL,
    `path`         VARCHAR(255) NULL,
    `is_active`    BOOLEAN DEFAULT FALSE,
    `settings`     JSON NULL,
    `installed_at` TIMESTAMP NULL,
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX `plugins_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. BACKUPS
-- ============================================================

CREATE TABLE `backups` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename`    VARCHAR(255) NOT NULL,
    `path`        VARCHAR(500) NOT NULL,
    `disk`        VARCHAR(50) DEFAULT 'local',
    `size`        BIGINT UNSIGNED DEFAULT 0,
    `type`        VARCHAR(30) DEFAULT 'full',  -- full, database, files
    `status`      VARCHAR(30) DEFAULT 'pending', -- pending, running, completed, failed
    `notes`       TEXT NULL,
    `created_by`  BIGINT UNSIGNED NULL,
    `completed_at` TIMESTAMP NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `backups_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. LIBRARY MODULE — Members
-- ============================================================

CREATE TABLE `members` (
    `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`        BIGINT UNSIGNED NULL,
    `member_number`  VARCHAR(50) NOT NULL,      -- NISN for students, NIP for teachers
    `type`           ENUM('student', 'teacher', 'staff') NOT NULL,
    `phone`          VARCHAR(20) NULL,
    `address`        TEXT NULL,
    `class_name`     VARCHAR(50) NULL,           -- Class for students (e.g. 'X IPA 1')
    `join_date`      DATE NULL,
    `photo`          VARCHAR(255) NULL,
    `status`         ENUM('active', 'suspended', 'inactive') DEFAULT 'active',
    `notes`          TEXT NULL,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    UNIQUE INDEX `members_member_number_unique` (`member_number`),
    INDEX `members_type_index` (`type`),
    INDEX `members_status_index` (`status`),
    INDEX `members_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. LIBRARY MODULE — Catalog
-- ============================================================

CREATE TABLE `categories` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id`   BIGINT UNSIGNED NULL,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(100) NOT NULL,
    `description` VARCHAR(500) NULL,
    `position`    INT DEFAULT 0,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    UNIQUE INDEX `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `books` (
    `id`                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id`          BIGINT UNSIGNED NULL,
    `title`                VARCHAR(255) NOT NULL,
    `isbn`                 VARCHAR(20) NULL,
    `author`               VARCHAR(255) NOT NULL,
    `publisher`            VARCHAR(255) NULL,
    `publish_year`         SMALLINT UNSIGNED NULL,
    `edition`              VARCHAR(50) NULL,
    `language`             VARCHAR(50) DEFAULT 'Indonesia',
    `pages`                INT UNSIGNED NULL,
    `ddc_classification`   VARCHAR(20) NULL,     -- Dewey Decimal (e.g. '300', '800')
    `description`          TEXT NULL,
    `cover_image`          VARCHAR(255) NULL,
    `total_copies`         INT UNSIGNED DEFAULT 0, -- denormalized count
    `available_copies`     INT UNSIGNED DEFAULT 0, -- denormalized count
    `created_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`           TIMESTAMP NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    UNIQUE INDEX `books_isbn_unique` (`isbn`),
    INDEX `books_title_index` (`title`),
    INDEX `books_author_index` (`author`),
    INDEX `books_category_id_index` (`category_id`),
    INDEX `books_ddc_index` (`ddc_classification`),
    INDEX `books_deleted_at_index` (`deleted_at`),
    FULLTEXT INDEX `books_fulltext` (`title`, `author`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `book_items` (
    `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `book_id`         BIGINT UNSIGNED NOT NULL,
    `barcode`         VARCHAR(100) NOT NULL,     -- individual asset tag barcode
    `call_number`     VARCHAR(50) NULL,          -- shelf classification number
    `shelf_location`  VARCHAR(100) NULL,         -- e.g. 'A1', 'B3'
    `acquisition_date` DATE NULL,
    `acquisition_source` VARCHAR(100) NULL,      -- purchase, donation, grant
    `price`           DECIMAL(12,2) NULL,
    `condition`       ENUM('good', 'fair', 'damaged', 'lost') DEFAULT 'good',
    `status`          ENUM('available', 'borrowed', 'reserved', 'maintenance', 'lost', 'disposed') DEFAULT 'available',
    `notes`           TEXT NULL,
    `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      TIMESTAMP NULL,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
    UNIQUE INDEX `book_items_barcode_unique` (`barcode`),
    INDEX `book_items_book_id_index` (`book_id`),
    INDEX `book_items_status_index` (`status`),
    INDEX `book_items_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. LIBRARY MODULE — Circulation
-- ============================================================

CREATE TABLE `borrow_records` (
    `id`               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`        BIGINT UNSIGNED NOT NULL,
    `book_item_id`     BIGINT UNSIGNED NOT NULL,
    `librarian_out_id` BIGINT UNSIGNED NOT NULL,  -- librarian who checked out
    `librarian_in_id`  BIGINT UNSIGNED NULL,       -- librarian who checked in
    `borrow_date`      DATE NOT NULL,
    `due_date`         DATE NOT NULL,
    `return_date`      DATE NULL,
    `extend_count`     TINYINT UNSIGNED DEFAULT 0,
    `status`           ENUM('borrowed', 'returned', 'overdue', 'lost') DEFAULT 'borrowed',
    `notes`            TEXT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`member_id`) REFERENCES `members`(`id`),
    FOREIGN KEY (`book_item_id`) REFERENCES `book_items`(`id`),
    FOREIGN KEY (`librarian_out_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`librarian_in_id`) REFERENCES `users`(`id`),
    INDEX `borrow_records_member_id_index` (`member_id`),
    INDEX `borrow_records_book_item_id_index` (`book_item_id`),
    INDEX `borrow_records_status_index` (`status`),
    INDEX `borrow_records_borrow_date_index` (`borrow_date`),
    INDEX `borrow_records_due_date_index` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservations` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id`   BIGINT UNSIGNED NOT NULL,
    `book_id`     BIGINT UNSIGNED NOT NULL,     -- reservation is per title, not per item
    `reserved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at`  TIMESTAMP NULL,
    `status`      ENUM('pending', 'ready', 'fulfilled', 'cancelled', 'expired') DEFAULT 'pending',
    `notes`       TEXT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
    INDEX `reservations_member_id_index` (`member_id`),
    INDEX `reservations_book_id_index` (`book_id`),
    INDEX `reservations_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. LIBRARY MODULE — Fines
-- ============================================================

CREATE TABLE `fines` (
    `id`               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `borrow_record_id` BIGINT UNSIGNED NOT NULL,
    `fine_type`        ENUM('overdue', 'damage', 'loss') DEFAULT 'overdue',
    `fine_amount`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `paid_amount`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `status`           ENUM('unpaid', 'partially_paid', 'paid', 'waived') DEFAULT 'unpaid',
    `payment_date`     DATE NULL,
    `waived_by`        BIGINT UNSIGNED NULL,
    `notes`            TEXT NULL,
    `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`borrow_record_id`) REFERENCES `borrow_records`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`waived_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `fines_borrow_record_id_index` (`borrow_record_id`),
    INDEX `fines_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. SESSION (Laravel default)
-- ============================================================

CREATE TABLE `sessions` (
    `id`            VARCHAR(255) PRIMARY KEY,
    `user_id`       BIGINT UNSIGNED NULL,
    `ip_address`    VARCHAR(45) NULL,
    `user_agent`    TEXT NULL,
    `payload`       LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. PASSWORD RESET TOKENS (Laravel default)
-- ============================================================

CREATE TABLE `password_reset_tokens` (
    `email`      VARCHAR(255) PRIMARY KEY,
    `token`      VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. JOBS & FAILED JOBS (Queue — Laravel default)
-- ============================================================

CREATE TABLE `jobs` (
    `id`           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `queue`        VARCHAR(255) NOT NULL,
    `payload`      LONGTEXT NOT NULL,
    `attempts`     TINYINT UNSIGNED NOT NULL,
    `reserved_at`  INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at`   INT UNSIGNED NOT NULL,
    INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
    `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid`       VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue`      TEXT NOT NULL,
    `payload`    LONGTEXT NOT NULL,
    `exception`  LONGTEXT NOT NULL,
    `failed_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE INDEX `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 19. CACHE (Laravel database cache driver)
-- ============================================================

CREATE TABLE `cache` (
    `key`        VARCHAR(255) PRIMARY KEY,
    `value`      MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key`        VARCHAR(255) PRIMARY KEY,
    `owner`      VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- NOTES
-- ============================================================
-- 1. All tables use InnoDB for FK support and ACID compliance.
-- 2. Soft deletes (deleted_at) on: users, members, books, book_items, media.
-- 3. JSON columns require MySQL 5.7.8+ (target is 8.0+).
-- 4. FULLTEXT index on books for catalog search.
-- 5. Indices on FK columns and high-cardinality filter columns.
-- 6. UUID used for notifications (Laravel convention).
-- 7. Polymorphic columns (model_type/model_id) on media and audit_logs.
-- 8. Settings uses key-value with group for organized retrieval.
-- 9. Queue tables included for database queue driver (shared hosting).
-- 10. Cache tables included for database cache driver (shared hosting).