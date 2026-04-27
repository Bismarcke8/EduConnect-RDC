<?php

namespace Core;

use Core\Database;
use Core\Security;

class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Register new user
     */
    public function register($email, $password, $firstName, $lastName)
    {
        // Check if user already exists
        $user = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        
        if ($user) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        // Validate email
        if (!Security::isValidEmail($email)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }

        // Validate password length
        if (strlen($password) < 6) {
            return ['success' => false, 'error' => 'Password must be at least 6 characters'];
        }

        try {
            $userId = $this->db->insert('users', [
                'email' => $email,
                'password' => Security::hashPassword($password),
                'first_name' => Security::sanitize($firstName),
                'last_name' => Security::sanitize($lastName),
            ]);

            return ['success' => true, 'user_id' => $userId];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Registration failed'];
        }
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        $user = $this->db->fetch(
            "SELECT id, email, password, role, first_name FROM users WHERE email = ? AND is_active = 1",
            [$email]
        );

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid credentials'];
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid credentials'];
        }

        // Set session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['first_name'];

        return ['success' => true, 'user_id' => $user['id']];
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session_destroy();
        return true;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     */
    public function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user data
     */
    public function getUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [$this->getUserId()]
        );
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Update password
     */
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);

        if (!$user || !Security::verifyPassword($oldPassword, $user['password'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'error' => 'New password must be at least 6 characters'];
        }

        $this->db->update('users', ['password' => Security::hashPassword($newPassword)], ['id' => $userId]);

        return ['success' => true];
    }
}
