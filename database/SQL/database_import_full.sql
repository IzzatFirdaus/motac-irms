-- MOTAC ICT LOAN HRMS - Full SQL import script
-- Purpose:
--   - Provide a clean data import for a fresh database environment
--   - Insert the minimum viable master data (roles, departments, positions, grades, users, categories, locations, settings, helpdesk)
--   - Respect enums and constraints defined in your migrations
--   - Avoid FK issues by truncating in a safe order with FK checks disabled
--
-- Notes:
--   - This script assumes your schema (tables, FKs, indexes) is already created (via migrations).
--   - If you need a schema + data dump, run your migrations first, then execute this script to import data.
--   - Password hash used is Laravel's default hash for 'password'
--       ('password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
--   - All insert values comply with enums and nullable FKs based on migrations.
--   - This seeds a consistent base so you can run application or continue with Laravel seeders for larger datasets.

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- Disable FK checks globally during truncate + seed to avoid constraint errors
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- TRUNCATE TABLES (in safe order)
-- ------------------------------------------------------------

-- Spatie Permission pivots first
TRUNCATE TABLE role_has_permissions;
TRUNCATE TABLE model_has_roles;
TRUNCATE TABLE model_has_permissions;

-- Transactional tables (if they exist) to avoid FK references
TRUNCATE TABLE loan_transaction_items;
TRUNCATE TABLE loan_transactions;
TRUNCATE TABLE loan_application_items;
TRUNCATE TABLE loan_applications;
TRUNCATE TABLE approvals;

-- Helpdesk module tables
TRUNCATE TABLE helpdesk_attachments;
TRUNCATE TABLE helpdesk_comments;
TRUNCATE TABLE helpdesk_tickets;
TRUNCATE TABLE helpdesk_priorities;
TRUNCATE TABLE helpdesk_categories;

-- Equipment tables
TRUNCATE TABLE equipment;               -- depends on departments, users, locations
TRUNCATE TABLE sub_categories;
TRUNCATE TABLE equipment_categories;

-- Master data
TRUNCATE TABLE settings;
TRUNCATE TABLE locations;
TRUNCATE TABLE grades;
TRUNCATE TABLE positions;
TRUNCATE TABLE departments;

-- Spatie permission base
TRUNCATE TABLE permissions;
TRUNCATE TABLE roles;

-- Utility/other (optional safe)
TRUNCATE TABLE notifications;
TRUNCATE TABLE employees;

-- Finally users (since many FKs point to users)
TRUNCATE TABLE users;

-- ------------------------------------------------------------
-- INSERT ROLES & PERMISSIONS (Spatie)
-- ------------------------------------------------------------
-- Minimal viable roles required by seeders and app
INSERT INTO roles (id, name, guard_name, created_at, updated_at) VALUES
  (1, 'Admin', 'web', NOW(), NOW()),
  (2, 'BPM Staff', 'web', NOW(), NOW()),
  (3, 'IT Admin', 'web', NOW(), NOW()),
  (4, 'Approver', 'web', NOW(), NOW()),
  (5, 'HOD', 'web', NOW(), NOW()),
  (6, 'User', 'web', NOW(), NOW());

-- Optional: minimal permissions (you can expand via Laravel seeder later)
-- Create a small set commonly required for UI access
INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES
  (1, 'view_users', 'web', NOW(), NOW()),
  (2, 'create_users', 'web', NOW(), NOW()),
  (3, 'edit_users', 'web', NOW(), NOW()),
  (4, 'view_equipment', 'web', NOW(), NOW()),
  (5, 'view_loan_applications', 'web', NOW(), NOW()),
  (6, 'create_helpdesk_tickets', 'web', NOW(), NOW()),
  (7, 'view_helpdesk_tickets', 'web', NOW(), NOW());

-- Map all minimal permissions to Admin
INSERT INTO role_has_permissions (permission_id, role_id) VALUES
  (1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1);

-- ------------------------------------------------------------
-- INSERT MASTER DATA - DEPARTMENTS
-- ------------------------------------------------------------
-- Create the BPM department used by AdminUserSeeder
INSERT INTO departments (id, name, description, branch_type, code, is_active, head_of_department_id, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (1, 'Bahagian Pengurusan Maklumat (BPM MOTAC)', 'Bahagian ICT/IT BPM MOTAC', 'headquarters', 'BPM', 1, NULL, NULL, NULL, NULL, NOW(), NOW(), NULL);

-- Additional core HQ/state departments (matches DepartmentSeeder basics)
INSERT INTO departments (name, code, branch_type, description, is_active, created_at, updated_at)
VALUES
  ('Bahagian Pentadbiran', 'BP', 'headquarters', 'Menguruskan hal-hal pentadbiran am dan sumber manusia', 1, NOW(), NOW()),
  ('Bahagian Kewangan', 'BK', 'headquarters', 'Menguruskan kewangan dan belanjawan kementerian', 1, NOW(), NOW()),
  ('Bahagian Kebudayaan', 'BKB', 'headquarters', 'Membangun dan mempromosikan kebudayaan Malaysia', 1, NOW(), NOW()),
  ('Bahagian Kesenian', 'BKS', 'headquarters', 'Membangun industri kesenian tempatan', 1, NOW(), NOW()),
  ('Bahagian Pelancongan', 'BPL', 'headquarters', 'Mempromosikan pelancongan Malaysia', 1, NOW(), NOW()),
  ('Unit Teknologi Maklumat', 'UTM', 'headquarters', 'Menguruskan infrastruktur dan sistem ICT', 1, NOW(), NOW()),
  ('Unit Komunikasi Korporat', 'UKK', 'headquarters', 'Menguruskan komunikasi dan perhubungan awam', 1, NOW(), NOW()),
  ('Unit Perancangan Strategik', 'UPS', 'headquarters', 'Perancangan strategik dan dasar kementerian', 1, NOW(), NOW()),
  ('Jabatan MOTAC Selangor', 'JMSEL', 'state', 'Pejabat negeri MOTAC di Selangor', 1, NOW(), NOW()),
  ('Jabatan MOTAC Johor', 'JMJOH', 'state', 'Pejabat negeri MOTAC di Johor', 1, NOW(), NOW()),
  ('Jabatan MOTAC Pulau Pinang', 'JMPPG', 'state', 'Pejabat negeri MOTAC di Pulau Pinang', 1, NOW(), NOW()),
  ('Jabatan MOTAC Sabah', 'JMSAB', 'state', 'Pejabat negeri MOTAC di Sabah', 1, NOW(), NOW()),
  ('Jabatan MOTAC Sarawak', 'JMSRW', 'state', 'Pejabat negeri MOTAC di Sarawak', 1, NOW(), NOW());

-- ------------------------------------------------------------
-- INSERT MASTER DATA - POSITIONS (only those needed by AdminUserSeeder)
-- ------------------------------------------------------------
-- Two primary positions used in AdminUserSeeder
INSERT INTO positions (id, name, description, is_active, grade_id, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (100, 'Pegawai Teknologi Maklumat Sistem', 'Position for IT system officer', 1, NULL, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (101, 'Ketua Unit Aplikasi', 'Head of application unit', 1, NULL, NULL, NULL, NULL, NOW(), NOW(), NULL);

-- ------------------------------------------------------------
-- INSERT MASTER DATA - GRADES (minimum needed by AdminUserSeeder)
-- ------------------------------------------------------------
-- Create F41, F44, N19
INSERT INTO grades (id, name, level, position_id, min_approval_grade_id, is_approver_grade, description, service_scheme, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (200, 'F41', 41, NULL, NULL, 1, 'Pegawai Teknologi Maklumat', 'Perkhidmatan Awam', NULL, NULL, NULL, NOW(), NOW(), NULL),
  (201, 'F44', 44, NULL, NULL, 1, 'Pegawai Teknologi Maklumat Kanan', 'Perkhidmatan Awam', NULL, NULL, NULL, NOW(), NOW(), NULL),
  (202, 'N19', 19, NULL, NULL, 0, 'Pembantu Tadbir', 'Perkhidmatan Sokongan', NULL, NULL, NULL, NOW(), NOW(), NULL);

-- ------------------------------------------------------------
-- INSERT USERS (AdminUserSeeder baseline)
-- ------------------------------------------------------------
-- Use valid status enum: active, inactive, pending (per migration 2013_11_01_132200_add_motac_columns_to_users_table)
-- BCrypt hash for 'password' (Laravel default) used for all users here
-- Tip: Change passwords after import if needed.

INSERT INTO users (
  id, name, email, email_verified_at, password, remember_token,
  title, identification_number, passport_number,
  department_id, position_id, grade_id, level, mobile_number, personal_email, motac_email, user_id_assigned,
  service_status, appointment_type, previous_department_name, previous_department_email,
  status, is_admin, is_bpm_staff, profile_photo_path, employee_id,
  created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
) VALUES
  (1000, 'Pentadbir Sistem Utama', 'admin@motac.gov.my', NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,
    'tuan', '800101010001', 'AA12345678',
    1, 101, 201, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    'active', 1, 1, NULL, NULL,
    NULL, NULL, NULL, NOW(), NOW(), NULL),

  (1001, 'Staf Sokongan BPM', 'bpmstaff@motac.gov.my', NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,
    'tuan', '850202020002', 'BB12345678',
    1, 100, 200, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    'active', 0, 1, NULL, NULL,
    NULL, NULL, NULL, NOW(), NOW(), NULL),

  (1002, 'Pegawai IT Admin', 'itadmin@motac.gov.my', NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,
    'tuan', '820303030003', 'CC12345678',
    1, 100, 200, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    'active', 0, 0, NULL, NULL,
    NULL, NULL, NULL, NOW(), NOW(), NULL),

  (1003, 'Pegawai Penyokong (Approver)', 'approver@motac.gov.my', NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,
    'tuan', '780505050005', 'DD12345678',
    1, 101, 201, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    'active', 0, 0, NULL, NULL,
    NULL, NULL, NULL, NOW(), NOW(), NULL),

  (1004, 'Pengguna Biasa Sistem', 'pengguna01@motac.gov.my', NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,
    'tuan', '900404040004', 'EE12345678',
    1, 100, 202, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    'active', 0, 0, NULL, NULL,
    NULL, NULL, NULL, NOW(), NOW(), NULL),

  (1005, 'Izzat Firdaus (System Developer)', 'izzatfirdaus@motac.gov.my', NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,
    'tuan', '980328145171', 'FF12345678',
    1, 101, 201, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
    'active', 1, 1, NULL, NULL,
    NULL, NULL, NULL, NOW(), NOW(), NULL);

-- Assign roles to users (model_has_roles)
-- model_type for User model
SET @USER_MODEL := 'App\\Models\\User';

-- Admin
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (1, @USER_MODEL, 1000);
-- BPM Staff
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (2, @USER_MODEL, 1001);
-- IT Admin
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (3, @USER_MODEL, 1002);
-- Approver
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (4, @USER_MODEL, 1003);
-- User
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (6, @USER_MODEL, 1004);

-- Developer must always be Admin and BPM Staff
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (1, @USER_MODEL, 1005);
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (2, @USER_MODEL, 1005);

-- ------------------------------------------------------------
-- INSERT LOCATIONS (LocationSeeder baseline list)
-- ------------------------------------------------------------
INSERT INTO locations (id, name, description, address, city, state, country, postal_code, is_active, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (1, 'MOTAC HQ - Aras G, Stor Utama ICT', 'Stor utama penyimpanan peralatan ICT di Ibu Pejabat MOTAC, Aras G.', 'Kementerian Pelancongan, Seni dan Budaya, Aras G, Presint 5', 'Putrajaya', 'WP Putrajaya', 'Malaysia', '62200', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (2, 'MOTAC HQ - Aras 10, Bilik Server Utama', 'Lokasi selamat untuk server utama dan peralatan rangkaian di Ibu Pejabat.', 'Kementerian Pelancongan, Seni dan Budaya, Aras 10, Presint 5', 'Putrajaya', 'WP Putrajaya', 'Malaysia', '62200', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (3, 'MOTAC HQ - Aras 18, Bahagian Pengurusan Maklumat', 'Ruang pejabat Bahagian Pengurusan Maklumat di Aras 18.', 'Kementerian Pelancongan, Seni dan Budaya, Aras 18, Presint 5', 'Putrajaya', 'WP Putrajaya', 'Malaysia', '62200', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (4, 'Pejabat MOTAC Negeri Perak - Pejabat Am', 'Pejabat pentadbiran utama di MOTAC Negeri Perak, Ipoh.', 'Jalan Panglima Bukit Gantang Wahab', 'Ipoh', 'Perak', 'Malaysia', '30000', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (5, 'Auditorium Kementerian', 'Auditorium utama untuk acara rasmi dan taklimat.', 'Kementerian Pelancongan, Seni dan Budaya, Aras 2, Presint 5', 'Putrajaya', 'WP Putrajaya', 'Malaysia', '62200', 1, NULL, NULL, NULL, NOW(), NOW(), NULL);

-- ------------------------------------------------------------
-- INSERT EQUIPMENT CATEGORIES & SUB-CATEGORIES (EquipmentCategorySeeder + SubCategoriesSeeder)
-- ------------------------------------------------------------
INSERT INTO equipment_categories (id, name, description, is_active, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (10, 'Komputer Riba', 'Komputer mudah alih untuk kegunaan pejabat dan lapangan.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (11, 'Projektor LCD', 'Alat untuk paparan visual mesyuarat dan pembentangan.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (12, 'Pencetak', 'Pencetak untuk dokumen pejabat (Laser, Inkjet).', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (13, 'Peralatan Rangkaian', 'Penghala, suis, dan perkakasan rangkaian lain.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (14, 'Peranti Input/Output', 'Papan kekunci, tetikus, kamera web, monitor, dll.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (15, 'Storan Mudah Alih', 'Pemacu keras luaran dan pemacu kilat USB.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (16, 'Komputer Meja (Desktop PC)', 'Komputer stesen kerja tetap.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (17, 'Peralatan ICT Lain', 'Peralatan ICT lain yang tidak dikategorikan secara spesifik.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL);

-- Sub-categories for select categories
INSERT INTO sub_categories (equipment_category_id, name, description, is_active, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (10, 'Ultrabook MOTAC', 'Komputer riba nipis dan ringan.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (10, 'Laptop Pejabat Standard', 'Laptop untuk kegunaan pejabat am.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (11, 'Projektor Jarak Dekat (Bilik Mesyuarat)', 'Projektor untuk ruang kecil.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (11, 'Projektor Mudah Alih (Acara Luar)', 'Projektor kompak untuk perjalanan dan acara luar.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (14, 'Papan Kekunci (Wayarles)', 'Papan kekunci tanpa wayar.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (14, 'Tetikus (Ergonomik)', 'Tetikus dengan reka bentuk ergonomik.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (14, 'Monitor LED 24 inci', 'Monitor LED bersaiz 24 inci.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL);

-- ------------------------------------------------------------
-- INSERT HELP DESK MASTER DATA (HelpdeskCategorySeeder + HelpdeskPrioritySeeder)
-- ------------------------------------------------------------
INSERT INTO helpdesk_categories (id, name, description, is_active, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (1, 'Hardware', 'Issues related to physical computer components, peripherals, etc.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (2, 'Software', 'Problems with operating systems, applications, or specialized software.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (3, 'Network', 'Connectivity issues, Wi-Fi problems, VPN access, etc.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (4, 'Account & Access', 'Password resets, account lockouts, access permissions.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (5, 'Printer', 'Printer setup, toner replacement, paper jams, and other printing issues.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (6, 'Email', 'Email client configuration, sending/receiving issues.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (7, 'System Performance', 'Slow computer, application crashes, freezing.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL),
  (8, 'Other', 'Miscellaneous IT support requests not covered by other categories.', 1, NULL, NULL, NULL, NOW(), NOW(), NULL);

INSERT INTO helpdesk_priorities (id, name, level, color_code, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
VALUES
  (1, 'Low', 10, '#28a745', NULL, NULL, NULL, NOW(), NOW(), NULL),
  (2, 'Medium', 20, '#007bff', NULL, NULL, NULL, NOW(), NOW(), NULL),
  (3, 'High', 30, '#ffc107', NULL, NULL, NULL, NOW(), NOW(), NULL),
  (4, 'Critical', 40, '#dc3545', NULL, NULL, NULL, NOW(), NOW(), NULL);

-- ------------------------------------------------------------
-- INSERT SETTINGS (SettingsSeeder - single authoritative row)
-- ------------------------------------------------------------
INSERT INTO settings (
  id, site_name, site_logo_path, default_notification_email_from, default_notification_email_name,
  sms_api_sender, sms_api_username, sms_api_password,
  terms_and_conditions_loan, terms_and_conditions_email,
  application_name, default_system_email, default_loan_period_days, max_loan_items_per_application,
  contact_us_email, system_maintenance_mode, system_maintenance_message,
  created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
) VALUES
  (
    1,
    'MOTAC Integrated Resource Management System',
    '/images/motac_default_logo.png',
    'noreply@motac.gov.my',
    'MOTAC Resource Management System',
    'MOTACGov', NULL, NULL,
    'Sila patuhi semua terma dan syarat peminjaman peralatan ICT MOTAC.',
    'Penggunaan alamat e-mel rasmi MOTAC tertakluk pada polisi keselamatan ICT dan tatakelola data MOTAC.',
    'MOTAC Integrated Resource Management System',
    'system.rms@motac.gov.my',
    7, 5,
    'aduan.rms@motac.gov.my',
    0,
    'Sistem kini dalam mod penyelenggaraan. Sila cuba lagi dalam beberapa minit.',
    1000, 1000, NULL, NOW(), NOW(), NULL
  );

-- ------------------------------------------------------------
-- OPTIONAL: Minimal Equipment (so AppEquipment::count() > 0)
-- ------------------------------------------------------------
-- These values comply with equipment table constraints (strings for status/condition as per migration defaults).
-- Ensure referenced FKs exist: equipment_category_id (10), sub_category_id (any from inserts), location_id (1..5), department_id (1), created_by (1000)
INSERT INTO equipment (
  equipment_category_id, sub_category_id, item_code, tag_id, serial_number, asset_type,
  brand, model, description, purchase_price, purchase_date, warranty_expiry_date,
  status, condition_status, location_id, current_location, notes,
  classification, acquisition_type, funded_by, supplier_name, department_id,
  created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
) VALUES
  (10, (SELECT id FROM sub_categories WHERE equipment_category_id=10 LIMIT 1),
   'ITEM-ABCD-00001', 'MOTAC/ICT/2025' '000001', 'SN-12345678AB',
   'laptop', 'Dell', 'Latitude 7420', 'Ultrabook unit for BPM staff', 4200.00, '2024-03-15', '2026-03-15',
   'available', 'good', 1, 'MOTAC HQ - Aras G, Stor Utama ICT', 'Issued for demo/testing',
   'asset', 'purchase', 'Peruntukan MOTAC', 'Dell Malaysia', 1,
   1000, 1000, NULL, NOW(), NOW(), NULL);

-- ------------------------------------------------------------
-- RE-ENABLE FOREIGN KEY CHECKS
-- ------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 1;

-- END OF SCRIPT