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

        $this->view('user/profile', [
            'user' => $user,
            'skills' => $skills,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'postsCount' => $postsCount,
            'isFollowing' => $isFollowing,
            'isOwnProfile' => $userId == $this->auth->getUserId()
        ]);
    }

    /**
     * Show user settings
     */
    public function settings()
    {
        $userModel = new User();
        $user = $userModel->find($this->auth->getUserId());

        $this->view('user/settings', [
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
        $this->redirect('/profile?id=' . $userId);
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
    public function follow()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $followingId = intval($_POST['user_id'] ?? 0);
        $followerId = $this->auth->getUserId();

        if ($followingId == $followerId) {
            $_SESSION['error'] = 'You cannot follow yourself';
            $this->redirect('/profile?id=' . $followingId);
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

        $this->redirect('/profile?id=' . $followingId);
    }

    /**
     * Unfollow user
     */
    public function unfollow()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $followingId = intval($_POST['user_id'] ?? 0);
        $followerId = $this->auth->getUserId();

        $this->db->delete('followers', [
            'follower_id' => $followerId,
            'following_id' => $followingId
        ]);

        $_SESSION['success'] = 'You are no longer following this user';
        $this->redirect('/profile?id=' . $followingId);
    }
}

