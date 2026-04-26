<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'users';

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
}

