<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Security;

class AdminController extends Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->requireAuth();
        
        // Check if user is admin
        if (!$this->auth->isAdmin()) {
            http_response_code(403);
            die('Access denied');
        }
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => $this->db->count('users'),
            'active_users' => $this->db->count('users', ['is_active' => 1]),
            'total_posts' => $this->db->count('posts'),
            'total_messages' => $this->db->count('messages'),
            'total_followers' => $this->db->count('followers'),
        ];

        $recentUsers = $this->db->fetchAll(
            "SELECT id, first_name, last_name, email, created_at FROM users ORDER BY created_at DESC LIMIT 10"
        );

        $recentPosts = $this->db->fetchAll(
            "SELECT p.id, p.title, u.first_name, u.last_name, p.created_at
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC LIMIT 10"
        );

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'recent_posts' => $recentPosts
        ]);
    }

    /**
     * Show users list
     */
    public function users()
    {
        $page = intval($_GET['page'] ?? 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $users = $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        $totalUsers = $this->db->count('users');
        $totalPages = ceil($totalUsers / $limit);

        $this->view('admin/users', [
            'users' => $users,
            'page' => $page,
            'total_pages' => $totalPages
        ]);
    }

    /**
     * Show posts list
     */
    public function posts()
    {
        $page = intval($_GET['page'] ?? 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $posts = $this->db->fetchAll(
            "SELECT p.id, p.title, u.first_name, u.last_name, p.created_at, p.is_published
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        $totalPosts = $this->db->count('posts');
        $totalPages = ceil($totalPosts / $limit);

        $this->view('admin/posts', [
            'posts' => $posts,
            'page' => $page,
            'total_pages' => $totalPages
        ]);
    }

    /**
     * Ban user
     */
    public function banUser($userId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/admin/users');
        }

        $userId = intval($userId ?? ($_POST['user_id'] ?? 0));

        if ($userId == $this->auth->getUserId()) {
            $_SESSION['error'] = 'You cannot ban yourself';
            $this->redirect('/admin/users');
        }

        $this->db->update('users', ['is_active' => 0], ['id' => $userId]);

        // Log action
        $this->logAdminAction('ban_user', 'users', $userId, 'User banned');

        $_SESSION['success'] = 'User banned successfully';
        $this->redirect('/admin/users');
    }

    /**
     * Delete post
     */
    public function deletePost($postId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/admin/posts');
        }

        $postId = intval($postId ?? ($_POST['post_id'] ?? 0));

        $post = $this->db->fetch("SELECT id FROM posts WHERE id = ?", [$postId]);

        if (!$post) {
            $_SESSION['error'] = 'Post not found';
            $this->redirect('/admin/posts');
        }

        $this->db->delete('posts', ['id' => $postId]);
        $this->db->delete('likes', ['post_id' => $postId]);
        $this->db->delete('comments', ['post_id' => $postId]);
        $this->db->delete('notifications', ['post_id' => $postId]);

        // Log action
        $this->logAdminAction('delete_post', 'posts', $postId, 'Post deleted');

        $_SESSION['success'] = 'Post deleted successfully';
        $this->redirect('/admin/posts');
    }

    /**
     * Show admin logs
     */
    public function logs()
    {
        $page = intval($_GET['page'] ?? 1);
        $limit = 100;
        $offset = ($page - 1) * $limit;

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.first_name, u.last_name
            FROM admin_logs al
            JOIN users u ON al.admin_id = u.id
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        $totalLogs = $this->db->count('admin_logs');
        $totalPages = ceil($totalLogs / $limit);

        $this->view('admin/logs', [
            'logs' => $logs,
            'page' => $page,
            'total_pages' => $totalPages
        ]);
    }

    /**
     * Create post as admin
     */
    public function createPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/admin/dashboard');
        }

        $content = Security::sanitize($_POST['content'] ?? '');
        $isOfficial = isset($_POST['is_official']) ? 1 : 0;

        if (empty($content)) {
            $_SESSION['error'] = 'Le contenu de la publication ne peut pas être vide';
            $this->redirect('admin/dashboard');
        }

        // Create post with admin user ID
        $adminId = $this->auth->getUserId();
        
        $postId = $this->db->insert('posts', [
            'user_id' => $adminId,
            'content' => $content,
            'is_official' => $isOfficial,
            'is_published' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Log action
        $this->logAdminAction('create_post', 'posts', $postId, 'Admin post created');

        $_SESSION['success'] = 'Publication créée avec succès';
        $this->redirect('admin/dashboard');
    }

    /**
     * Show detailed statistics
     */
    public function stats()
    {
        // User statistics
        $userStats = [
            'total_users' => $this->db->count('users'),
            'active_users' => $this->db->count('users', ['is_active' => 1]),
            'inactive_users' => $this->db->count('users', ['is_active' => 0]),
            'admin_users' => $this->db->count('users', ['role' => 'admin']),
            'student_users' => $this->db->count('users', ['role' => 'student']),
        ];

        // Post statistics
        $postStats = [
            'total_posts' => $this->db->count('posts'),
            'published_posts' => $this->db->count('posts', ['is_published' => 1]),
            'unpublished_posts' => $this->db->count('posts', ['is_published' => 0]),
            'official_posts' => $this->db->count('posts', ['is_official' => 1]),
        ];

        // Activity statistics (last 30 days)
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $activityStats = [
            'new_users_30d' => $this->db->count('users', "created_at >= '$thirtyDaysAgo'"),
            'new_posts_30d' => $this->db->count('posts', "created_at >= '$thirtyDaysAgo'"),
            'new_messages_30d' => $this->db->count('messages', "created_at >= '$thirtyDaysAgo'"),
        ];

        // Top universities
        $topUniversities = $this->db->fetchAll(
            "SELECT university, COUNT(*) as count FROM users WHERE university IS NOT NULL AND university != '' GROUP BY university ORDER BY count DESC LIMIT 10"
        );

        $this->view('admin/stats', [
            'user_stats' => $userStats,
            'post_stats' => $postStats,
            'activity_stats' => $activityStats,
            'top_universities' => $topUniversities
        ]);
    }

    /**
     * Show admin settings
     */
    public function settings()
    {
        // Get current settings from config or database
        $settings = [
            'site_name' => 'EduConnect-RDC',
            'site_description' => 'Plateforme collaborative pour l\'éducation en RDC',
            'allow_registration' => true,
            'max_upload_size' => MAX_UPLOAD_SIZE,
            'items_per_page' => ITEMS_PER_PAGE,
        ];

        if (!empty($_SESSION['admin_settings']) && is_array($_SESSION['admin_settings'])) {
            $saved = $_SESSION['admin_settings'];
            $settings['site_name'] = $saved['site_name'] ?? $settings['site_name'];
            $settings['site_description'] = $saved['site_description'] ?? $settings['site_description'];
            $settings['allow_registration'] = isset($saved['allow_registration']) ? (bool) $saved['allow_registration'] : $settings['allow_registration'];
            $settings['max_upload_size'] = isset($saved['max_upload_size']) ? ((int) $saved['max_upload_size'] * 1024 * 1024) : $settings['max_upload_size'];
            $settings['items_per_page'] = $saved['items_per_page'] ?? $settings['items_per_page'];
        }

        $this->view('admin/settings', ['settings' => $settings]);
    }

    /**
     * Update admin settings (demo persistence via session)
     */
    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/admin/settings');
        }

        $_SESSION['admin_settings'] = [
            'site_name' => Security::sanitize($_POST['site_name'] ?? APP_NAME),
            'site_description' => Security::sanitize($_POST['site_description'] ?? ''),
            'allow_registration' => isset($_POST['allow_registration']),
            'max_upload_size' => max(1, min(50, intval($_POST['max_upload_size'] ?? 5))),
            'items_per_page' => max(5, min(100, intval($_POST['items_per_page'] ?? ITEMS_PER_PAGE))),
        ];

        $this->logAdminAction('update_settings', 'settings', 0, 'System settings updated');
        $_SESSION['success'] = 'Paramètres enregistrés avec succès';
        $this->redirect('/admin/settings');
    }

    /**
     * Log admin action
     */
    private function logAdminAction($action, $targetType, $targetId, $description)
    {
        $this->db->insert('admin_logs', [
            'admin_id' => $this->auth->getUserId(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'description' => $description
        ]);
    }
}
