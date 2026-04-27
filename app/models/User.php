<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'users';

    private function ensureFriendRequestsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS friend_requests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_request (sender_id, receiver_id),
            INDEX idx_receiver_status (receiver_id, status),
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->query($sql);
    }

    /**
     * Get user by email
     */
    public function findByEmail($email)
    {
        return $this->findWhere(['email' => $email]);
    }

    /**
     * Get user's posts
     */
    public function getPosts($userId, $limit = 15, $offset = 0)
    {
        $sql = "SELECT p.*, COUNT(l.id) as likes_count, COUNT(c.id) as comments_count
                FROM posts p
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE p.user_id = ? AND p.is_published = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->query($sql, [$userId, $limit, $offset])->fetchAll();
    }

    /**
     * Get user's followers count
     */
    public function getFollowersCount($userId)
    {
        return $this->db->count('followers', ['following_id' => $userId]);
    }

    /**
     * Get user's following count
     */
    public function getFollowingCount($userId)
    {
        return $this->db->count('followers', ['follower_id' => $userId]);
    }

    /**
     * Get user's skills
     */
    public function getSkills($userId)
    {
        $sql = "SELECT skill_name FROM skills WHERE user_id = ? ORDER BY skill_name";
        $result = $this->query($sql, [$userId])->fetchAll();
        return array_column($result, 'skill_name');
    }

    /**
     * Check if user is following another user
     */
    public function isFollowing($followerId, $followingId)
    {
        $sql = "SELECT id FROM followers WHERE follower_id = ? AND following_id = ?";
        return $this->query($sql, [$followerId, $followingId])->fetch() !== false;
    }

    /**
     * Get user feed (posts from followed users)
     */
    public function getFeed($userId, $limit = 15, $offset = 0)
    {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.profile_photo,
                COUNT(l.id) as likes_count, COUNT(c.id) as comments_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE (
                    p.user_id = ? OR
                    p.user_id IN (SELECT following_id FROM followers WHERE follower_id = ?)
                )
                AND p.is_published = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->query($sql, [$userId, $userId, $limit, $offset])->fetchAll();
    }

    public function getInviteStatus($senderId, $receiverId)
    {
        $this->ensureFriendRequestsTable();
        $sql = "SELECT status FROM friend_requests WHERE sender_id = ? AND receiver_id = ? LIMIT 1";
        $row = $this->query($sql, [$senderId, $receiverId])->fetch();
        return $row['status'] ?? null;
    }

    public function getPendingInviteFrom($senderId, $receiverId)
    {
        $this->ensureFriendRequestsTable();
        $sql = "SELECT id, sender_id, receiver_id, status, created_at
                FROM friend_requests
                WHERE sender_id = ? AND receiver_id = ? AND status = 'pending'
                LIMIT 1";
        return $this->query($sql, [$senderId, $receiverId])->fetch();
    }

    public function sendInvite($senderId, $receiverId)
    {
        $this->ensureFriendRequestsTable();
        $sql = "INSERT INTO friend_requests (sender_id, receiver_id, status)
                VALUES (?, ?, 'pending')
                ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = CURRENT_TIMESTAMP";
        $this->query($sql, [$senderId, $receiverId]);
    }

    public function updateInviteStatus($senderId, $receiverId, $status)
    {
        $this->ensureFriendRequestsTable();
        $sql = "UPDATE friend_requests SET status = ? WHERE sender_id = ? AND receiver_id = ?";
        return $this->query($sql, [$status, $senderId, $receiverId])->rowCount();
    }

    public function getIncomingPendingInvites($userId, $limit = 10)
    {
        $this->ensureFriendRequestsTable();
        $sql = "SELECT fr.sender_id, fr.created_at, u.first_name, u.last_name, u.profile_photo, u.university
                FROM friend_requests fr
                JOIN users u ON u.id = fr.sender_id
                WHERE fr.receiver_id = ? AND fr.status = 'pending'
                ORDER BY fr.created_at DESC
                LIMIT ?";
        return $this->query($sql, [$userId, $limit])->fetchAll();
    }

    public function getOutgoingPendingInvites($userId, $limit = 20)
    {
        $this->ensureFriendRequestsTable();
        $sql = "SELECT fr.receiver_id, fr.created_at, u.first_name, u.last_name, u.profile_photo, u.university
                FROM friend_requests fr
                JOIN users u ON u.id = fr.receiver_id
                WHERE fr.sender_id = ? AND fr.status = 'pending'
                ORDER BY fr.created_at DESC
                LIMIT ?";
        return $this->query($sql, [$userId, $limit])->fetchAll();
    }

    public function countIncomingPendingInvites($userId)
    {
        $this->ensureFriendRequestsTable();
        $sql = "SELECT COUNT(*) AS total FROM friend_requests WHERE receiver_id = ? AND status = 'pending'";
        $row = $this->query($sql, [$userId])->fetch();
        return (int) ($row['total'] ?? 0);
    }
}

