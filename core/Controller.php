<?php

namespace Core;

class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Render a view file with data
     */
    public function view($view, $data = [])
    {
        $viewPath = ROOT_PATH . '/app/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("View not found: " . $view);
        }

        // Extract data variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        include $viewPath;
        
        $content = ob_get_clean();
        echo $content;
    }

    /**
     * Get a model instance
     */
    protected function model($modelName)
    {
        $modelClass = "App\\Models\\" . $modelName;

        if (!class_exists($modelClass)) {
            die("Model not found: " . $modelClass);
        }

        return new $modelClass();
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url)
    {
        // Ensure URL starts with /
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }
        
        // Add base path for correct redirection
        $fullUrl = APP_BASE_PATH . $url;
        
        header("Location: " . $fullUrl);
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get authenticated user ID
     */
    protected function getAuthUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/auth/login');
        }
    }

    /**
     * Sanitize input
     */
    protected function sanitize($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF($token)
    {
        return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
    }
}
