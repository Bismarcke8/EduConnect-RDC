<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Security;
use App\Models\User;

class AuthController extends Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
    }

    /**
     * Show login form
     */
    public function login()
    {
        // If already logged in, redirect to feed
        if ($this->auth->isAuthenticated()) {
            $this->redirect('/feed');
        }

        $this->view('auth/login', ['csrf_token' => $_SESSION['csrf_token']]);
    }

    /**
     * Handle login form submission
     */
    public function handleLogin()
    {
        // Check CSRF token
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/auth/login');
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            $this->redirect('/auth/login');
        }

        // Try to login
        $result = $this->auth->login($email, $password);

        if ($result['success']) {
            // Check if trying to login as admin but not the admin account
            if ($this->auth->isAdmin() && $email !== 'admin@educonnect.rdc') {
                // Logout the user
                $this->auth->logout();
                $_SESSION['error'] = 'Accès refusé. Seule l\'administration peut se connecter avec ce rôle.';
                $this->redirect('/auth/login');
            }

            $_SESSION['success'] = 'Welcome back!';
            if ($this->auth->isAdmin()) {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/feed');
            }
        } else {
            $_SESSION['error'] = $result['error'];
            $this->redirect('/auth/login');
        }
    }

    /**
     * Show register form
     */
    public function register()
    {
        // If already logged in, redirect to feed
        if ($this->auth->isAuthenticated()) {
            $this->redirect('/feed');
        }

        $this->view('auth/register', ['csrf_token' => $_SESSION['csrf_token']]);
    }

    /**
     * Handle register form submission
     */
    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/auth/register');
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        $firstName = Security::sanitize($_POST['first_name'] ?? '');
        $lastName = Security::sanitize($_POST['last_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($firstName) || empty($lastName) || empty($password)) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('/auth/register');
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            $this->redirect('/auth/register');
        }

        // Try to register
        $result = $this->auth->register($email, $password, $firstName, $lastName);

        if ($result['success']) {
            $_SESSION['success'] = 'Registration successful! You can now log in.';
            $this->redirect('/auth/login');
        } else {
            $_SESSION['error'] = $result['error'];
            $this->redirect('/auth/register');
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->auth->logout();
        $_SESSION['success'] = 'You have been logged out';
        $this->redirect('/');
    }
}

