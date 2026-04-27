<?php

namespace Core;

class Security
{
    /**
     * Hash password using bcrypt
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Sanitize input string
     */
    public static function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email
     */
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Generate CSRF token
     */
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Verify CSRF token
     */
    public static function verifyToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Escape for SQL (use prepared statements instead!)
     */
    public static function escapeSql($input)
    {
        return addslashes($input);
    }

    /**
     * Escape for HTML output
     */
    public static function escapeHtml($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file)
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'Invalid file upload'];
        }

        $detectedType = mime_content_type($file['tmp_name']) ?: '';
        if (!in_array($detectedType, ALLOWED_FILE_TYPES, true)) {
            return ['valid' => false, 'error' => 'File type not allowed'];
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return ['valid' => false, 'error' => 'File size exceeds limit'];
        }

        return ['valid' => true];
    }

    /**
     * Generate unique filename
     */
    public static function generateFileName($originalName)
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('file_', true) . '.' . $ext;
    }
}
