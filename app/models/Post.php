<?php

namespace App\Models;

use Core\Model;

class Post extends Model
{
    protected $table = 'posts';

    /**
     * Get post with user and stats
     */
    public function getWithStats($postId)
    {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.profile_photo,
                COUNT(DISTINCT l.id) as likes_count,
                COUNT(DISTINCT c.id) as comments_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE p.id = ?
                GROUP BY p.id";
        
        return $this->query($sql, [$postId])->fetch();
    }

    /**
     * Get all posts with stats
     */
    public function getAllWithStats($limit = 15, $offset = 0)
    {
        $sql = "SELECT p.*, u.first_name, u.last_name, u.profile_photo,
                COUNT(DISTINCT l.id) as likes_count,
                COUNT(DISTINCT c.id) as comments_count
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE p.is_published = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->query($sql, [$limit, $offset])->fetchAll();
    }

    /**
     * Check if user liked post
     */
    public function isLikedBy($postId, $userId)
    {
        $sql = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
        return $this->query($sql, [$postId, $userId])->fetch() !== false;
    }

    /**
     * Get post comments
     */
    public function getComments($postId)
    {
        $sql = "SELECT c.*, u.first_name, u.last_name, u.profile_photo
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at ASC";
        
        return $this->query($sql, [$postId])->fetchAll();
    }
}

