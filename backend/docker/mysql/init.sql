-- Create database if not exists
CREATE DATABASE IF NOT EXISTS loan_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges
GRANT ALL PRIVILEGES ON loan_system.* TO 'loan_user'@'%';
FLUSH PRIVILEGES;