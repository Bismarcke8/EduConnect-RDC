<?php

namespace App\Models;

use Core\Model;

class Message extends Model
{
    protected $table = 'messages';

    /**
     * Get conversation between two users
     */
    public function getConversation($userId1, $userId2, $limit = 50, $offset = 0)
    {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.profile_photo
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE (
                    (m.sender_id = ? AND m.receiver_id = ?) OR
                    (m.sender_id = ? AND m.receiver_id = ?)
                )
                ORDER BY m.created_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->query($sql, [$userId1, $userId2, $userId2, $userId1, $limit, $offset])->fetchAll();
    }

    /**
     * Get user's conversations (list of people they've messaged)
     */
    public function getConversations($userId)
    {
        $sql = "SELECT DISTINCT u.id, u.first_name, u.last_name, u.profile_photo,
                m.content as last_message, m.created_at as last_message_time,
                (SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND sender_id = u.id AND is_read = 0) as unread_count
                FROM (
                    SELECT sender_id as user_id FROM messages WHERE receiver_id = ?
                    UNION
                    SELECT receiver_id as user_id FROM messages WHERE sender_id = ?
                ) as msg
                JOIN users u ON msg.user_id = u.id
                LEFT JOIN messages m ON (
                    (m.sender_id = ? AND m.receiver_id = u.id) OR
                    (m.sender_id = u.id AND m.receiver_id = ?)
                )
                WHERE m.id = (
                    SELECT id FROM messages m2
                    WHERE (
                        (m2.sender_id = ? AND m2.receiver_id = u.id) OR
                        (m2.sender_id = u.id AND m2.receiver_id = ?)
                    )
                    ORDER BY m2.created_at DESC
                    LIMIT 1
                )
                ORDER BY last_message_time DESC";
        
        return $this->query($sql, [$userId, $userId, $userId, $userId, $userId, $userId, $userId])->fetchAll();
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount($userId)
    {
        return $this->db->count('messages', ['receiver_id' => $userId, 'is_read' => 0]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($userId, $senderId)
    {
        return $this->db->update(
            'messages',
            ['is_read' => 1],
            ['receiver_id' => $userId, 'sender_id' => $senderId]
        );
    }
}

