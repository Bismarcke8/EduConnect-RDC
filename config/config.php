<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'educonnect_rdc');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('APP_NAME', 'EduConnect-RDC');
define('APP_URL', 'http://localhost/EduConnect-RDC');
define('APP_DEBUG', true);
define('APP_BASE_PATH', rtrim((string) (parse_url(APP_URL, PHP_URL_PATH) ?? ''), '/') . '/public');

// Timezone
date_default_timezone_set('Africa/Kinshasa');

// File upload configuration
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads/');

// Security
define('PASSWORD_COST', 10); // bcrypt cost factor

// API configuration
define('ITEMS_PER_PAGE', 15);
define('API_RATE_LIMIT', 100); // requests per hour

