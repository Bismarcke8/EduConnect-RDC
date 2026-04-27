<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Security;

class NotificationController extends Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->requireAuth();
    }

    /**
     * Show notifications page
     */
    public function index()
    {
        $userId = $this->auth->getUserId();

        $notifications = $this->db->fetchAll(
            "SELECT n.*, u.first_name, u.last_name, u.profile_photo
            FROM notifications n
            JOIN users u ON n.from_user_id = u.id
            WHERE n.user_id = ?
            ORDER BY n.created_at DESC
            LIMIT 100",
            [$userId]
        );

        $this->view('notification/index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'error' => 'Invalid CSRF token'], 403);
        }

        $notificationId = intval($notificationId ?? ($_POST['notification_id'] ?? 0));
        $userId = $this->auth->getUserId();

        // Verify notification belongs to user
        $notification = $this->db->fetch(
            "SELECT id FROM notifications WHERE id = ? AND user_id = ?",
            [$notificationId, $userId]
        );

        if (!$notification) {
            http_response_code(403);
            die('You do not have permission');
        }

        $this->db->update('notifications', ['is_read' => 1], ['id' => $notificationId]);

        $this->json(['success' => true]);
    }

    /**
     * Get notifications via API
     */
    public function apiGetNotifications()
    {
        $userId = $this->auth->getUserId();
        $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] == '1';

        $sql = "SELECT n.*, u.first_name, u.last_name, u.profile_photo
            FROM notifications n
            JOIN users u ON n.from_user_id = u.id
            WHERE n.user_id = ?";
        $params = [$userId];
        if ($unreadOnly) {
            $sql .= " AND n.is_read = 0";
        }
        $sql .= " ORDER BY n.created_at DESC LIMIT 50";
        $notifications = $this->db->fetchAll($sql, $params);

        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $this->db->count('notifications', ['user_id' => $userId, 'is_read' => 0])
        ]);
    }
}
