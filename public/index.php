<?php

/*
 * Front Controller - Point d'entrée de l'application
 * EduConnect-RDC
 */

define('ROOT_PATH', dirname(__DIR__));

// Session configuration
ini_set('session.gc_maxlifetime', 86400 * 7); // 7 days
ini_set('session.cookie_lifetime', 864000 * 7);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger la configuration
require_once ROOT_PATH . '/config/config.php';

// Autoloader avec namespace
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Create upload directory if not exists
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Charger et dispatcher les routes
require_once ROOT_PATH . '/routes/web.php';

