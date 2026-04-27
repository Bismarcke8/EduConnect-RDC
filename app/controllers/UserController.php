<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Security;
use App\Models\User;

class UserController extends Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->requireAuth();
    }

    /**
     * Show user profile
     */
    public function profile($userId = null)
    {
        if (!$userId) {
            $userId = $this->auth->getUserId();
        }

        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user) {
            http_response_code(404);
            echo "User not found";
            return;
        }

        $skills = $userModel->getSkills($userId);
        $followersCount = $userModel->getFollowersCount($userId);
        $followingCount = $userModel->getFollowingCount($userId);
        $postsCount = $this->db->count('posts', ['user_id' => $userId]);
        $isFollowing = $userId != $this->auth->getUserId() && 
                      $userModel->isFollowing($this->auth->getUserId(), $userId);
        $currentUserId = $this->auth->getUserId();
        $outgoingInvitePending = $userId != $currentUserId && $userModel->getPendingInviteFrom($currentUserId, $userId);
        $incomingInvitePending = $userId != $currentUserId && $userModel->getPendingInviteFrom($userId, $currentUserId);
        $pendingInvites = ($userId == $currentUserId) ? $userModel->getIncomingPendingInvites($currentUserId) : [];

        // Get user's posts
        $posts = $this->db->fetchAll(
            "SELECT p.*, 
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
                    EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?) as is_liked
             FROM posts p 
             WHERE p.user_id = ? AND p.is_published = 1 
             ORDER BY p.created_at DESC",
            [$currentUserId, $userId]
        );

        $this->view('user/profile', [
            'user' => $user,
            'skills' => $skills,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'postsCount' => $postsCount,
            'posts' => $posts,
            'isFollowing' => $isFollowing,
            'isOwnProfile' => $userId == $currentUserId,
            'outgoingInvitePending' => !empty($outgoingInvitePending),
            'incomingInvitePending' => !empty($incomingInvitePending),
            'pendingInvites' => $pendingInvites
        ]);
    }

    public function invitations()
    {
        $userId = $this->auth->getUserId();
        $userModel = new User();

        $friends = $userModel->getFriends($userId, 200);
        $allUsers = $userModel->getAllUsersExcept($userId, 200);
        $friendRequests = $userModel->getFriendRequests($userId);

        $inviteStatus = [];
        foreach ($friendRequests as $request) {
            if ($request['sender_id'] == $userId) {
                $inviteStatus[$request['receiver_id']] = ['direction' => 'outgoing', 'status' => $request['status']];
            } else {
                $inviteStatus[$request['sender_id']] = ['direction' => 'incoming', 'status' => $request['status']];
            }
        }

        $this->view('user/invitations', [
            'incomingInvites' => $userModel->getIncomingPendingInvites($userId, 100),
            'outgoingInvites' => $userModel->getOutgoingPendingInvites($userId, 100),
            'friends' => $friends,
            'allUsers' => $allUsers,
            'inviteStatus' => $inviteStatus,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Show user settings
     */
    public function settings()
    {
        $userModel = new User();
        $user = $userModel->find($this->auth->getUserId());

        $this->view('user/edit', [
            'user' => $user,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/user/settings');
        }

        $userId = $this->auth->getUserId();

        $data = [
            'first_name' => Security::sanitize($_POST['first_name'] ?? ''),
            'last_name' => Security::sanitize($_POST['last_name'] ?? ''),
            'university' => Security::sanitize($_POST['university'] ?? ''),
            'field_of_study' => Security::sanitize($_POST['field_of_study'] ?? ''),
            'bio' => Security::sanitize($_POST['bio'] ?? '')
        ];

        $this->db->update('users', $data, ['id' => $userId]);

        $_SESSION['success'] = 'Profile updated successfully';
        $this->redirect('/profile/' . $userId);
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/user/settings');
        }

        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('/user/settings');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match';
            $this->redirect('/user/settings');
        }

        $result = $this->auth->changePassword($this->auth->getUserId(), $oldPassword, $newPassword);

        if ($result['success']) {
            $_SESSION['success'] = 'Password changed successfully';
            $this->redirect('/user/settings');
        } else {
            $_SESSION['error'] = $result['error'];
            $this->redirect('/user/settings');
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'error' => 'Invalid CSRF token'], 403);
        }

        if (!isset($_FILES['photo'])) {
            $this->json(['success' => false, 'error' => 'No file uploaded'], 400);
        }

        // Validate file
        $validation = Security::validateFileUpload($_FILES['photo']);
        if (!$validation['valid']) {
            $this->json(['success' => false, 'error' => $validation['error']], 400);
        }

        // Generate filename and move file
        $filename = Security::generateFileName($_FILES['photo']['name']);
        $destination = UPLOAD_PATH . 'profiles/' . $filename;

        if (!is_dir(UPLOAD_PATH . 'profiles/')) {
            mkdir(UPLOAD_PATH . 'profiles/', 0755, true);
        }

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
            $this->json(['success' => false, 'error' => 'Failed to upload file'], 500);
        }

        // Update user profile
        $userId = $this->auth->getUserId();
        $this->db->update('users', ['profile_photo' => 'uploads/profiles/' . $filename], ['id' => $userId]);

        $this->json(['success' => true, 'photo_path' => 'uploads/profiles/' . $filename]);
    }

    /**
     * Follow user
     */
    public function follow($followingId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/feed');
        }

        $followingId = intval($followingId ?? ($_POST['user_id'] ?? 0));
        $followerId = $this->auth->getUserId();

        if ($followingId == $followerId) {
            $_SESSION['error'] = 'You cannot follow yourself';
            $this->redirect('/profile/' . $followingId);
        }

        // Check if already following
        $existing = $this->db->fetch(
            "SELECT id FROM followers WHERE follower_id = ? AND following_id = ?",
            [$followerId, $followingId]
        );

        if (!$existing) {
            $this->db->insert('followers', [
                'follower_id' => $followerId,
                'following_id' => $followingId
            ]);

            // Create notification
            $this->db->insert('notifications', [
                'user_id' => $followingId,
                'from_user_id' => $followerId,
                'type' => 'follow'
            ]);

            $_SESSION['success'] = 'You are now following this user';
        }

        $this->redirect('/profile/' . $followingId);
    }

    /**
     * Unfollow user
     */
    public function unfollow($followingId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/feed');
        }

        $followingId = intval($followingId ?? ($_POST['user_id'] ?? 0));
        $followerId = $this->auth->getUserId();

        $this->db->delete('followers', [
            'follower_id' => $followerId,
            'following_id' => $followingId
        ]);

        $_SESSION['success'] = 'You are no longer following this user';
        $this->redirect('/profile/' . $followingId);
    }

    public function sendInvite($receiverId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            }
            die('Method not allowed');
        }
        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Invalid CSRF token'], 403);
            }
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/feed');
        }

        $senderId = $this->auth->getUserId();
        $receiverId = intval($receiverId ?? ($_POST['user_id'] ?? 0));
        if ($receiverId <= 0 || $receiverId === $senderId) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Invitation invalide'], 400);
            }
            $_SESSION['error'] = 'Invitation invalide';
            $this->redirect('/feed');
        }

        $userModel = new User();
        if ($userModel->isFollowing($senderId, $receiverId) && $userModel->isFollowing($receiverId, $senderId)) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Déjà connectés'], 400);
            }
            $_SESSION['error'] = 'Vous êtes déjà connectés';
            $this->redirect('/profile/' . $receiverId);
        }

        $userModel->sendInvite($senderId, $receiverId);
        $this->db->insert('notifications', [
            'user_id' => $receiverId,
            'from_user_id' => $senderId,
            'type' => 'invite'
        ]);

        if ($this->isAjaxRequest()) {
            $this->json(['success' => true, 'message' => 'Invitation envoyée']);
        }

        $_SESSION['success'] = 'Invitation envoyée';
        $this->redirect('/profile/' . $receiverId);
    }

    public function acceptInvite($senderId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            }
            die('Method not allowed');
        }
        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Invalid CSRF token'], 403);
            }
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/profile');
        }

        $receiverId = $this->auth->getUserId();
        $senderId = intval($senderId ?? ($_POST['user_id'] ?? 0));
        $userModel = new User();
        $pending = $userModel->getPendingInviteFrom($senderId, $receiverId);
        if (!$pending) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Invitation introuvable'], 404);
            }
            $_SESSION['error'] = 'Invitation introuvable';
            $this->redirect('/profile');
        }

        $userModel->updateInviteStatus($senderId, $receiverId, 'accepted');
        if (!$userModel->isFollowing($senderId, $receiverId)) {
            $this->db->insert('followers', ['follower_id' => $senderId, 'following_id' => $receiverId]);
        }
        if (!$userModel->isFollowing($receiverId, $senderId)) {
            $this->db->insert('followers', ['follower_id' => $receiverId, 'following_id' => $senderId]);
        }

        $this->db->insert('notifications', [
            'user_id' => $senderId,
            'from_user_id' => $receiverId,
            'type' => 'follow'
        ]);

        if ($this->isAjaxRequest()) {
            $this->json(['success' => true, 'message' => 'Invitation acceptée']);
        }

        $_SESSION['success'] = 'Invitation acceptée';
        $this->redirect('/profile/' . $senderId);
    }

    public function declineInvite($senderId = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            }
            die('Method not allowed');
        }
        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'error' => 'Invalid CSRF token'], 403);
            }
            $_SESSION['error'] = 'Requête invalide (CSRF)';
            $this->redirect('/profile');
        }

        $receiverId = $this->auth->getUserId();
        $senderId = intval($senderId ?? ($_POST['user_id'] ?? 0));
        $userModel = new User();
        $userModel->updateInviteStatus($senderId, $receiverId, 'declined');

        if ($this->isAjaxRequest()) {
            $this->json(['success' => true, 'message' => 'Invitation refusée']);
        }

        $_SESSION['success'] = 'Invitation refusée';
        $this->redirect('/profile');
    }

    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

