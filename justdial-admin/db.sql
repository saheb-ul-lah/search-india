-- Create database
CREATE DATABASE IF NOT EXISTS justdial_admin;
USE justdial_admin;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    country VARCHAR(50),
    postal_code VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    role ENUM('admin', 'manager', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(255),
    image VARCHAR(255),
    parent_id INT DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Businesses table
CREATE TABLE IF NOT EXISTS businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(255),
    logo VARCHAR(255),
    cover_image VARCHAR(255),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    country VARCHAR(50) DEFAULT 'India',
    postal_code VARCHAR(20),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(255),
    opening_hours TEXT,
    founded_year INT,
    owner_id INT,
    status ENUM('pending', 'active', 'inactive', 'rejected') DEFAULT 'pending',
    is_verified TINYINT(1) DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Business categories (many-to-many relationship)
CREATE TABLE IF NOT EXISTS business_categories (
    business_id INT,
    category_id INT,
    PRIMARY KEY (business_id, category_id),
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    price_type ENUM('fixed', 'starting_from', 'hourly', 'daily', 'custom') DEFAULT 'fixed',
    duration VARCHAR(50),
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

-- Business images
CREATE TABLE IF NOT EXISTS business_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    user_id INT NOT NULL,
    rating DECIMAL(2, 1) NOT NULL,
    title VARCHAR(100),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Business operating hours
CREATE TABLE IF NOT EXISTS business_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    opening_time TIME,
    closing_time TIME,
    is_closed TINYINT(1) DEFAULT 0,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    UNIQUE KEY (business_id, day_of_week)
);

-- Inquiries/Leads table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT,
    status ENUM('new', 'contacted', 'converted', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

-- Cities table
CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    country VARCHAR(100) DEFAULT 'India',
    is_featured TINYINT(1) DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Activity logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role, email_verified, status)
VALUES ('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'active');

-- Insert sample categories
INSERT INTO categories (name, slug, description, icon, status, featured, sort_order) VALUES
('Restaurants', 'restaurants', 'Find the best restaurants near you', 'utensils', 'active', 1, 1),
('Hotels', 'hotels', 'Book hotels for your stay', 'hotel', 'active', 1, 2),
('Doctors', 'doctors', 'Find doctors and medical services', 'stethoscope', 'active', 1, 3),
('Real Estate', 'real-estate', 'Property listings and real estate services', 'building', 'active', 1, 4),
('Education', 'education', 'Schools, colleges and educational institutes', 'graduation-cap', 'active', 1, 5),
('Beauty & Spa', 'beauty-spa', 'Salons, spas and wellness centers', 'spa', 'active', 1, 6),
('Home Services', 'home-services', 'Plumbers, electricians and home repair services', 'tools', 'active', 1, 7),
('Travel', 'travel', 'Travel agencies and tour operators', 'plane', 'active', 1, 8),
('Shopping', 'shopping', 'Retail stores and shopping centers', 'shopping-bag', 'active', 1, 9),
('Automotive', 'automotive', 'Car dealers, repair shops and automotive services', 'car', 'active', 1, 10);

-- Insert sample cities
INSERT INTO cities (name, state, country, is_featured, status) VALUES
('Mumbai', 'Maharashtra', 'India', 1, 'active'),
('Delhi', 'Delhi', 'India', 1, 'active'),
('Bangalore', 'Karnataka', 'India', 1, 'active'),
('Hyderabad', 'Telangana', 'India', 1, 'active'),
('Chennai', 'Tamil Nadu', 'India', 1, 'active'),
('Kolkata', 'West Bengal', 'India', 1, 'active'),
('Pune', 'Maharashtra', 'India', 1, 'active'),
('Ahmedabad', 'Gujarat', 'India', 1, 'active'),
('Jaipur', 'Rajasthan', 'India', 1, 'active'),
('Lucknow', 'Uttar Pradesh', 'India', 0, 'active');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'JustDial Admin', 'general'),
('site_tagline', 'Business Listing & Directory Platform', 'general'),
('site_logo', 'logo.png', 'general'),
('site_favicon', 'favicon.ico', 'general'),
('admin_email', 'admin@example.com', 'general'),
('items_per_page', '10', 'general'),
('enable_registration', '1', 'users'),
('enable_email_verification', '1', 'users'),
('enable_reviews', '1', 'businesses'),
('review_moderation', '1', 'businesses'),
('business_approval_required', '1', 'businesses'),
('google_maps_api_key', '', 'integrations'),
('smtp_host', '', 'email'),
('smtp_port', '587', 'email'),
('smtp_username', '', 'email'),
('smtp_password', '', 'email'),
('smtp_encryption', 'tls', 'email'),
('primary_color', '#3b82f6', 'appearance'),
('secondary_color', '#10b981', 'appearance'),
('accent_color', '#f59e0b', 'appearance');




-- Add this table to your db.sql or run it directly
CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) NOT NULL COMMENT 'Identifier for the key (e.g., partner name)',
  `api_key_hash` varchar(255) NOT NULL COMMENT 'Hashed API key',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `requests_today` int(11) NOT NULL DEFAULT 0,
  `last_request_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Example Key Insertion (Generate a secure key and hash it!)
-- PHP:
-- $apiKey = bin2hex(random_bytes(32)); // Generate a random key
-- $hashedKey = password_hash($apiKey, PASSWORD_DEFAULT);
-- echo "API Key: " . $apiKey . "\n";
-- echo "Hashed Key (for DB): " . $hashedKey . "\n";
-- INSERT INTO `api_keys` (`key_name`, `api_key_hash`, `status`) VALUES ('MyFirstApp', '$2y$10$....(your hashed key here)....', 'active');