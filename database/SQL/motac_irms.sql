-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 01:31 PM
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
-- Database: `motac_irms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approvable_type` varchar(255) NOT NULL,
  `approvable_id` bigint(20) UNSIGNED NOT NULL,
  `officer_id` bigint(20) UNSIGNED NOT NULL,
  `stage` varchar(255) DEFAULT NULL COMMENT 'Approval stage: e.g., support_review, hod_review',
  `status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT 'Approval status: pending, approved, rejected, canceled, forwarded',
  `notes` text DEFAULT NULL COMMENT 'Notes or comments from officer',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT 'Timestamp when approved',
  `rejected_at` timestamp NULL DEFAULT NULL COMMENT 'Timestamp when rejected',
  `canceled_at` timestamp NULL DEFAULT NULL COMMENT 'Timestamp when canceled',
  `resubmitted_at` timestamp NULL DEFAULT NULL COMMENT 'Timestamp when resubmitted/forwarded',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Category name',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Department/Bahagian/Unit name',
  `description` text DEFAULT NULL COMMENT 'Description',
  `branch_type` enum('state','headquarters') DEFAULT NULL COMMENT 'Pejabat Negeri/Ibu Pejabat distinction',
  `code` varchar(255) DEFAULT NULL COMMENT 'Optional department code',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `head_of_department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `birth_and_place` varchar(255) NOT NULL,
  `national_number` varchar(255) NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `address` varchar(255) NOT NULL,
  `notes` longtext DEFAULT NULL,
  `max_leave_allowed` int(11) NOT NULL DEFAULT 0,
  `delay_counter` time NOT NULL DEFAULT '00:00:00',
  `hourly_counter` time NOT NULL DEFAULT '00:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sub_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_code` varchar(255) DEFAULT NULL COMMENT 'Unique internal identifier',
  `tag_id` varchar(255) DEFAULT NULL COMMENT 'Asset tag / No. Aset',
  `serial_number` varchar(255) DEFAULT NULL COMMENT 'Manufacturer Serial Number',
  `asset_type` varchar(255) NOT NULL DEFAULT 'other_ict' COMMENT 'Specific type of asset',
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available' COMMENT 'Operational status',
  `condition_status` varchar(255) NOT NULL DEFAULT 'good' COMMENT 'Physical condition',
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `classification` varchar(255) DEFAULT NULL,
  `acquisition_type` varchar(255) DEFAULT NULL,
  `funded_by` varchar(255) DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_categories`
--

CREATE TABLE `equipment_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Category name',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Grade name, e.g., "41", "N19", "JUSA C"',
  `level` int(11) DEFAULT NULL COMMENT 'Numeric level for sorting/comparison',
  `position_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'FK to positions (added as FK later)',
  `min_approval_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_approver_grade` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Can users of this grade approve applications?',
  `description` text DEFAULT NULL,
  `service_scheme` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk_attachments`
--

CREATE TABLE `helpdesk_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attachable_type` varchar(255) NOT NULL,
  `attachable_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk_categories`
--

CREATE TABLE `helpdesk_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Category name, e.g., Hardware, Software',
  `description` text DEFAULT NULL COMMENT 'Category description',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk_comments`
--

CREATE TABLE `helpdesk_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'True if only visible to agents/staff',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk_priorities`
--

CREATE TABLE `helpdesk_priorities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Priority name (Low, High, etc.)',
  `level` int(11) NOT NULL DEFAULT 0 COMMENT 'Sorting level, e.g., 1=Low, 5=Critical',
  `color_code` varchar(255) DEFAULT NULL COMMENT 'Hex color for UI (optional)',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk_tickets`
--

CREATE TABLE `helpdesk_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'open',
  `priority_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `sla_due_at` timestamp NULL DEFAULT NULL COMMENT 'SLA due date',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `progress` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Percentage progress for the job',
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Applicant User ID',
  `responsible_officer_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Officer responsible, if not applicant',
  `supporting_officer_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Supporting officer',
  `purpose` text NOT NULL,
  `location` varchar(255) NOT NULL COMMENT 'Usage location',
  `return_location` varchar(255) DEFAULT NULL COMMENT 'Return location',
  `loan_start_date` datetime NOT NULL,
  `loan_end_date` datetime NOT NULL,
  `status` enum('draft','pending_support','pending_approver_review','pending_bpm_review','approved','rejected','partially_issued','issued','returned','overdue','cancelled','partially_returned_pending_inspection') NOT NULL DEFAULT 'draft',
  `rejection_reason` text DEFAULT NULL,
  `applicant_confirmation_timestamp` timestamp NULL DEFAULT NULL COMMENT 'Applicant confirmation timestamp',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `current_approval_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_approval_stage` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_application_items`
--

CREATE TABLE `loan_application_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_application_id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `equipment_type` varchar(255) NOT NULL COMMENT 'e.g., Laptop, Projektor, LCD Monitor',
  `quantity_requested` int(10) UNSIGNED NOT NULL,
  `quantity_approved` int(10) UNSIGNED DEFAULT NULL,
  `quantity_issued` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `quantity_returned` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Returned quantity',
  `status` enum('pending_approval','item_approved','item_rejected','awaiting_issuance','fully_issued','partially_issued','fully_returned','item_cancelled') NOT NULL DEFAULT 'pending_approval' COMMENT 'Status of this requested item',
  `notes` text DEFAULT NULL COMMENT 'Specific requirements or remarks by applicant',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_transactions`
--

CREATE TABLE `loan_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_application_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('issue','return') NOT NULL DEFAULT 'issue',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `issuing_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiving_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accessories_checklist_on_issue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accessories_checklist_on_issue`)),
  `issue_notes` text DEFAULT NULL,
  `issue_timestamp` timestamp NULL DEFAULT NULL COMMENT 'Actual moment of physical issue',
  `returning_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `return_accepting_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accessories_checklist_on_return` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accessories_checklist_on_return`)),
  `return_notes` text DEFAULT NULL,
  `return_timestamp` timestamp NULL DEFAULT NULL COMMENT 'Actual moment of physical return',
  `related_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `due_date` date DEFAULT NULL COMMENT 'Applicable for issue transactions',
  `status` enum('pending','issued','returned_pending_inspection','returned_good','returned_damaged','items_reported_lost','completed','cancelled','overdue','returned_with_loss','returned_with_damage_and_loss','returned','partially_returned') NOT NULL DEFAULT 'pending' COMMENT 'Status of the transaction itself',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_transaction_items`
--

CREATE TABLE `loan_transaction_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_transaction_id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `loan_application_item_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Link to requested item',
  `quantity_transacted` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Usually 1 for serialized items',
  `status` enum('issued','returned_pending_inspection','returned_good','returned_minor_damage','returned_major_damage','reported_lost','unserviceable_on_return') NOT NULL DEFAULT 'issued' COMMENT 'Status of this item in this transaction',
  `condition_on_return` enum('new','good','fair','minor_damage','major_damage','unserviceable','lost') DEFAULT NULL,
  `accessories_checklist_issue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accessories_checklist_issue`)),
  `accessories_checklist_return` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accessories_checklist_return`)),
  `item_notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Location name',
  `description` text DEFAULT NULL COMMENT 'Optional description',
  `address` varchar(255) DEFAULT NULL COMMENT 'Street or premise address',
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2013_01_01_000000_create_users_table', 1),
(2, '2013_11_01_131800_create_departments_table', 1),
(3, '2013_11_01_131900_create_grades_table', 1),
(4, '2013_11_01_132000_create_positions_table', 1),
(5, '2013_11_01_132154_create_employees_table', 1),
(6, '2013_11_01_132200_add_motac_columns_to_users_table', 1),
(7, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(8, '2014_10_12_100000_create_password_resets_table', 1),
(9, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(10, '2019_08_19_000000_create_failed_jobs_table', 1),
(11, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(12, '2023_10_19_112718_create_sessions_table', 1),
(13, '2023_11_02_105426_create_settings_table', 1),
(14, '2023_11_26_092207_create_notifications_table', 1),
(15, '2023_12_01_195938_create_jobs_table', 1),
(16, '2023_12_30_100000_create_equipment_categories_table', 1),
(17, '2023_12_31_000000_create_sub_categories_table', 1),
(18, '2023_12_31_010000_create_locations_table', 1),
(19, '2024_01_02_000000_create_equipment_table', 1),
(20, '2024_04_16_111956_create_permission_tables', 1),
(21, '2024_05_27_100007_create_webhook_calls_table', 1),
(22, '2024_07_10_100000_create_transitions_table', 1),
(23, '2025_04_22_083504_create_approvals_table', 1),
(24, '2025_04_22_083504_create_loan_applications_table', 1),
(25, '2025_04_22_083516_create_categories_table', 1),
(26, '2025_04_22_105519_create_loan_application_items_table', 1),
(27, '2025_04_22_105519_create_loan_transactions_table', 1),
(28, '2025_05_12_073554_create_activity_log_table', 1),
(29, '2025_05_12_073555_add_event_column_to_activity_log_table', 1),
(30, '2025_05_12_073556_add_batch_uuid_column_to_activity_log_table', 1),
(31, '2025_05_13_143555_create_loan_transaction_items_table', 1),
(32, '2025_06_08_082226_add_foreign_key_and_index_to_grades_table', 1),
(33, '2025_08_01_104700_create_helpdesk_categories_table', 1),
(34, '2025_08_01_104701_create_helpdesk_priorities_table', 1),
(35, '2025_08_01_104702_create_helpdesk_tickets_table', 1),
(36, '2025_08_01_104703_create_helpdesk_comments_table', 1),
(37, '2025_08_01_104704_create_helpdesk_attachments_table', 1),
(38, '2025_08_02_122101_remove_email_provisioning_components', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Position name',
  `description` text DEFAULT NULL COMMENT 'Description',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Active flag',
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name` varchar(255) NOT NULL DEFAULT 'MOTAC Resource Management',
  `site_logo_path` varchar(255) DEFAULT NULL,
  `default_notification_email_from` varchar(255) DEFAULT NULL,
  `default_notification_email_name` varchar(255) DEFAULT NULL,
  `sms_api_sender` varchar(255) DEFAULT NULL,
  `sms_api_username` varchar(255) DEFAULT NULL,
  `sms_api_password` varchar(255) DEFAULT NULL,
  `terms_and_conditions_loan` text DEFAULT NULL,
  `terms_and_conditions_email` text DEFAULT NULL,
  `application_name` varchar(255) NOT NULL DEFAULT 'MOTAC Integrated Resource Management System' COMMENT 'Official name of the application',
  `default_system_email` varchar(255) DEFAULT NULL COMMENT 'Default email for system-originated non-notification emails',
  `default_loan_period_days` int(10) UNSIGNED NOT NULL DEFAULT 7 COMMENT 'Default loan period in days',
  `max_loan_items_per_application` int(10) UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Max items per single loan application',
  `contact_us_email` varchar(255) DEFAULT NULL COMMENT 'Email for contact us inquiries',
  `system_maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `system_maintenance_message` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transitions`
--

CREATE TABLE `transitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `handed_date` date DEFAULT NULL COMMENT 'Date equipment was handed over',
  `return_date` date DEFAULT NULL COMMENT 'Date equipment was returned',
  `center_document_number` varchar(255) DEFAULT NULL COMMENT 'Reference document number',
  `reason` varchar(255) DEFAULT NULL COMMENT 'Reason for handover/return',
  `note` longtext DEFAULT NULL COMMENT 'Additional notes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Display name or username',
  `title` varchar(255) DEFAULT NULL COMMENT 'e.g., Encik, Puan, Dr.',
  `identification_number` varchar(255) DEFAULT NULL COMMENT 'NRIC',
  `passport_number` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL COMMENT 'For "Aras" or floor level, as string',
  `mobile_number` varchar(255) DEFAULT NULL,
  `personal_email` varchar(255) DEFAULT NULL COMMENT 'If distinct from login email',
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Consider using Spatie roles exclusively.',
  `is_bpm_staff` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Consider using Spatie roles exclusively.',
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL COMMENT 'User login email',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'When the email was verified',
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL COMMENT 'Token for "remember me" functionality',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhook_calls`
--

CREATE TABLE `webhook_calls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`headers`)),
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `exception` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  ADD KEY `approvals_officer_id_foreign` (`officer_id`),
  ADD KEY `approvals_created_by_foreign` (`created_by`),
  ADD KEY `approvals_updated_by_foreign` (`updated_by`),
  ADD KEY `approvals_deleted_by_foreign` (`deleted_by`),
  ADD KEY `approvals_stage_index` (`stage`),
  ADD KEY `approvals_status_index` (`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD KEY `categories_created_by_foreign` (`created_by`),
  ADD KEY `categories_updated_by_foreign` (`updated_by`),
  ADD KEY `categories_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`),
  ADD UNIQUE KEY `departments_code_unique` (`code`),
  ADD KEY `departments_head_of_department_id_foreign` (`head_of_department_id`),
  ADD KEY `departments_created_by_foreign` (`created_by`),
  ADD KEY `departments_updated_by_foreign` (`updated_by`),
  ADD KEY `departments_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_national_number_unique` (`national_number`),
  ADD UNIQUE KEY `employees_mobile_number_unique` (`mobile_number`),
  ADD KEY `employees_created_by_foreign` (`created_by`),
  ADD KEY `employees_updated_by_foreign` (`updated_by`),
  ADD KEY `employees_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_item_code_unique` (`item_code`),
  ADD UNIQUE KEY `equipment_tag_id_unique` (`tag_id`),
  ADD UNIQUE KEY `equipment_serial_number_unique` (`serial_number`),
  ADD KEY `equipment_sub_category_id_foreign` (`sub_category_id`),
  ADD KEY `equipment_created_by_foreign` (`created_by`),
  ADD KEY `equipment_updated_by_foreign` (`updated_by`),
  ADD KEY `equipment_deleted_by_foreign` (`deleted_by`),
  ADD KEY `equipment_asset_type_index` (`asset_type`),
  ADD KEY `equipment_status_index` (`status`),
  ADD KEY `equipment_condition_status_index` (`condition_status`),
  ADD KEY `equipment_brand_index` (`brand`),
  ADD KEY `equipment_model_index` (`model`),
  ADD KEY `equipment_equipment_category_id_index` (`equipment_category_id`),
  ADD KEY `equipment_location_id_index` (`location_id`),
  ADD KEY `equipment_department_id_index` (`department_id`),
  ADD KEY `equipment_classification_index` (`classification`),
  ADD KEY `equipment_acquisition_type_index` (`acquisition_type`);

--
-- Indexes for table `equipment_categories`
--
ALTER TABLE `equipment_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_categories_name_unique` (`name`),
  ADD KEY `equipment_categories_created_by_foreign` (`created_by`),
  ADD KEY `equipment_categories_updated_by_foreign` (`updated_by`),
  ADD KEY `equipment_categories_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grades_name_position_id_unique` (`name`,`position_id`),
  ADD KEY `grades_min_approval_grade_id_foreign` (`min_approval_grade_id`),
  ADD KEY `grades_created_by_foreign` (`created_by`),
  ADD KEY `grades_updated_by_foreign` (`updated_by`),
  ADD KEY `grades_deleted_by_foreign` (`deleted_by`),
  ADD KEY `grades_position_id_foreign` (`position_id`);

--
-- Indexes for table `helpdesk_attachments`
--
ALTER TABLE `helpdesk_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `helpdesk_attachments_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`),
  ADD KEY `helpdesk_attachments_created_by_foreign` (`created_by`),
  ADD KEY `helpdesk_attachments_updated_by_foreign` (`updated_by`),
  ADD KEY `helpdesk_attachments_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `helpdesk_categories`
--
ALTER TABLE `helpdesk_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `helpdesk_categories_name_unique` (`name`),
  ADD KEY `helpdesk_categories_created_by_foreign` (`created_by`),
  ADD KEY `helpdesk_categories_updated_by_foreign` (`updated_by`),
  ADD KEY `helpdesk_categories_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `helpdesk_comments`
--
ALTER TABLE `helpdesk_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `helpdesk_comments_ticket_id_foreign` (`ticket_id`),
  ADD KEY `helpdesk_comments_user_id_foreign` (`user_id`),
  ADD KEY `helpdesk_comments_created_by_foreign` (`created_by`),
  ADD KEY `helpdesk_comments_updated_by_foreign` (`updated_by`),
  ADD KEY `helpdesk_comments_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `helpdesk_priorities`
--
ALTER TABLE `helpdesk_priorities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `helpdesk_priorities_name_unique` (`name`),
  ADD KEY `helpdesk_priorities_created_by_foreign` (`created_by`),
  ADD KEY `helpdesk_priorities_updated_by_foreign` (`updated_by`),
  ADD KEY `helpdesk_priorities_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `helpdesk_tickets`
--
ALTER TABLE `helpdesk_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `helpdesk_tickets_category_id_foreign` (`category_id`),
  ADD KEY `helpdesk_tickets_priority_id_foreign` (`priority_id`),
  ADD KEY `helpdesk_tickets_user_id_foreign` (`user_id`),
  ADD KEY `helpdesk_tickets_assigned_to_user_id_foreign` (`assigned_to_user_id`),
  ADD KEY `helpdesk_tickets_closed_by_id_foreign` (`closed_by_id`),
  ADD KEY `helpdesk_tickets_created_by_foreign` (`created_by`),
  ADD KEY `helpdesk_tickets_updated_by_foreign` (`updated_by`),
  ADD KEY `helpdesk_tickets_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_applications_user_id_foreign` (`user_id`),
  ADD KEY `loan_applications_responsible_officer_id_foreign` (`responsible_officer_id`),
  ADD KEY `loan_applications_supporting_officer_id_foreign` (`supporting_officer_id`),
  ADD KEY `loan_applications_approved_by_foreign` (`approved_by`),
  ADD KEY `loan_applications_rejected_by_foreign` (`rejected_by`),
  ADD KEY `loan_applications_cancelled_by_foreign` (`cancelled_by`),
  ADD KEY `loan_applications_current_approval_officer_id_foreign` (`current_approval_officer_id`),
  ADD KEY `loan_applications_created_by_foreign` (`created_by`),
  ADD KEY `loan_applications_updated_by_foreign` (`updated_by`),
  ADD KEY `loan_applications_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `loan_application_items`
--
ALTER TABLE `loan_application_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_application_items_loan_application_id_foreign` (`loan_application_id`),
  ADD KEY `loan_application_items_equipment_id_foreign` (`equipment_id`),
  ADD KEY `loan_application_items_created_by_foreign` (`created_by`),
  ADD KEY `loan_application_items_updated_by_foreign` (`updated_by`),
  ADD KEY `loan_application_items_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_transactions_loan_application_id_foreign` (`loan_application_id`),
  ADD KEY `loan_transactions_issuing_officer_id_foreign` (`issuing_officer_id`),
  ADD KEY `loan_transactions_receiving_officer_id_foreign` (`receiving_officer_id`),
  ADD KEY `loan_transactions_returning_officer_id_foreign` (`returning_officer_id`),
  ADD KEY `loan_transactions_return_accepting_officer_id_foreign` (`return_accepting_officer_id`),
  ADD KEY `loan_transactions_related_transaction_id_foreign` (`related_transaction_id`),
  ADD KEY `loan_transactions_created_by_foreign` (`created_by`),
  ADD KEY `loan_transactions_updated_by_foreign` (`updated_by`),
  ADD KEY `loan_transactions_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `loan_transaction_items`
--
ALTER TABLE `loan_transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_transaction_items_loan_transaction_id_foreign` (`loan_transaction_id`),
  ADD KEY `loan_transaction_items_equipment_id_foreign` (`equipment_id`),
  ADD KEY `loan_transaction_items_loan_application_item_id_foreign` (`loan_application_item_id`),
  ADD KEY `loan_transaction_items_created_by_foreign` (`created_by`),
  ADD KEY `loan_transaction_items_updated_by_foreign` (`updated_by`),
  ADD KEY `loan_transaction_items_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locations_name_unique` (`name`),
  ADD KEY `locations_created_by_foreign` (`created_by`),
  ADD KEY `locations_updated_by_foreign` (`updated_by`),
  ADD KEY `locations_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  ADD KEY `notifications_created_by_foreign` (`created_by`),
  ADD KEY `notifications_updated_by_foreign` (`updated_by`),
  ADD KEY `notifications_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_name_unique` (`name`),
  ADD KEY `positions_grade_id_foreign` (`grade_id`),
  ADD KEY `positions_created_by_foreign` (`created_by`),
  ADD KEY `positions_updated_by_foreign` (`updated_by`),
  ADD KEY `positions_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_created_by_foreign` (`created_by`),
  ADD KEY `settings_updated_by_foreign` (`updated_by`),
  ADD KEY `settings_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_categories_equipment_category_id_foreign` (`equipment_category_id`),
  ADD KEY `sub_categories_created_by_foreign` (`created_by`),
  ADD KEY `sub_categories_updated_by_foreign` (`updated_by`),
  ADD KEY `sub_categories_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `transitions`
--
ALTER TABLE `transitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transitions_center_document_number_unique` (`center_document_number`),
  ADD KEY `transitions_equipment_id_foreign` (`equipment_id`),
  ADD KEY `transitions_employee_id_foreign` (`employee_id`),
  ADD KEY `transitions_created_by_foreign` (`created_by`),
  ADD KEY `transitions_updated_by_foreign` (`updated_by`),
  ADD KEY `transitions_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_identification_number_unique` (`identification_number`),
  ADD UNIQUE KEY `users_passport_number_unique` (`passport_number`),
  ADD UNIQUE KEY `users_personal_email_unique` (`personal_email`),
  ADD KEY `users_department_id_foreign` (`department_id`),
  ADD KEY `users_position_id_foreign` (`position_id`),
  ADD KEY `users_grade_id_foreign` (`grade_id`),
  ADD KEY `users_employee_id_foreign` (`employee_id`),
  ADD KEY `users_created_by_foreign` (`created_by`),
  ADD KEY `users_updated_by_foreign` (`updated_by`),
  ADD KEY `users_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `webhook_calls`
--
ALTER TABLE `webhook_calls`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_categories`
--
ALTER TABLE `equipment_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `helpdesk_attachments`
--
ALTER TABLE `helpdesk_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `helpdesk_categories`
--
ALTER TABLE `helpdesk_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `helpdesk_comments`
--
ALTER TABLE `helpdesk_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `helpdesk_priorities`
--
ALTER TABLE `helpdesk_priorities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `helpdesk_tickets`
--
ALTER TABLE `helpdesk_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_application_items`
--
ALTER TABLE `loan_application_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_transaction_items`
--
ALTER TABLE `loan_transaction_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transitions`
--
ALTER TABLE `transitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webhook_calls`
--
ALTER TABLE `webhook_calls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approvals_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approvals_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_head_of_department_id_foreign` FOREIGN KEY (`head_of_department_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_equipment_category_id_foreign` FOREIGN KEY (`equipment_category_id`) REFERENCES `equipment_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment_categories`
--
ALTER TABLE `equipment_categories`
  ADD CONSTRAINT `equipment_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_min_approval_grade_id_foreign` FOREIGN KEY (`min_approval_grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `helpdesk_attachments`
--
ALTER TABLE `helpdesk_attachments`
  ADD CONSTRAINT `helpdesk_attachments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_attachments_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_attachments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `helpdesk_categories`
--
ALTER TABLE `helpdesk_categories`
  ADD CONSTRAINT `helpdesk_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `helpdesk_comments`
--
ALTER TABLE `helpdesk_comments`
  ADD CONSTRAINT `helpdesk_comments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_comments_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `helpdesk_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `helpdesk_comments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `helpdesk_priorities`
--
ALTER TABLE `helpdesk_priorities`
  ADD CONSTRAINT `helpdesk_priorities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_priorities_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_priorities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `helpdesk_tickets`
--
ALTER TABLE `helpdesk_tickets`
  ADD CONSTRAINT `helpdesk_tickets_assigned_to_user_id_foreign` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_tickets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `helpdesk_categories` (`id`),
  ADD CONSTRAINT `helpdesk_tickets_closed_by_id_foreign` FOREIGN KEY (`closed_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_tickets_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_tickets_priority_id_foreign` FOREIGN KEY (`priority_id`) REFERENCES `helpdesk_priorities` (`id`),
  ADD CONSTRAINT `helpdesk_tickets_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `helpdesk_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD CONSTRAINT `loan_applications_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_current_approval_officer_id_foreign` FOREIGN KEY (`current_approval_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_responsible_officer_id_foreign` FOREIGN KEY (`responsible_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_supporting_officer_id_foreign` FOREIGN KEY (`supporting_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_application_items`
--
ALTER TABLE `loan_application_items`
  ADD CONSTRAINT `loan_application_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_application_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_application_items_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_application_items_loan_application_id_foreign` FOREIGN KEY (`loan_application_id`) REFERENCES `loan_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_application_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  ADD CONSTRAINT `loan_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_issuing_officer_id_foreign` FOREIGN KEY (`issuing_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_loan_application_id_foreign` FOREIGN KEY (`loan_application_id`) REFERENCES `loan_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_transactions_receiving_officer_id_foreign` FOREIGN KEY (`receiving_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_related_transaction_id_foreign` FOREIGN KEY (`related_transaction_id`) REFERENCES `loan_transactions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_return_accepting_officer_id_foreign` FOREIGN KEY (`return_accepting_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_returning_officer_id_foreign` FOREIGN KEY (`returning_officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transactions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loan_transaction_items`
--
ALTER TABLE `loan_transaction_items`
  ADD CONSTRAINT `loan_transaction_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transaction_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transaction_items_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_transaction_items_loan_application_item_id_foreign` FOREIGN KEY (`loan_application_item_id`) REFERENCES `loan_application_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_transaction_items_loan_transaction_id_foreign` FOREIGN KEY (`loan_transaction_id`) REFERENCES `loan_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_transaction_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locations_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `positions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `positions_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `positions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `settings_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sub_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sub_categories_equipment_category_id_foreign` FOREIGN KEY (`equipment_category_id`) REFERENCES `equipment_categories` (`id`),
  ADD CONSTRAINT `sub_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transitions`
--
ALTER TABLE `transitions`
  ADD CONSTRAINT `transitions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transitions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transitions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transitions_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`),
  ADD CONSTRAINT `transitions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
