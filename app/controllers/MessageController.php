<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Security;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->requireAuth();
    }

    /**
     * Show inbox (list of conversations)
     */
    public function inbox()
    {
        $messageModel = new Message();
        $userId = $this->auth->getUserId();

        $conversations = $messageModel->getConversations($userId);

        $this->view('message/inbox', [
            'conversations' => $conversations,
            'unread_count' => $messageModel->getUnreadCount($userId)
        ]);
    }

    /**
     * Show conversation with a specific user
     */
    public function conversation($recipientId = null)
    {
        $recipientId = intval($recipientId ?? 0);
        $userId = $this->auth->getUserId();

        if ($recipientId == $userId) {
            $_SESSION['error'] = 'Invalid conversation';
            $this->redirect('/messages');
        }

        $userModel = new User();
        $recipient = $userModel->find($recipientId);

        if (!$recipient) {
            http_response_code(404);
            echo "User not found";
            return;
        }

        $messageModel = new Message();
        $messages = $messageModel->getConversation($userId, $recipientId);

        // Mark messages as read
        $messageModel->markAsRead($userId, $recipientId);

        // Get conversations for sidebar
        $conversations = $messageModel->getConversations($userId);

        $this->view('message/conversation', [
            'recipient' => $recipient,
            'messages' => array_reverse($messages),
            'conversations' => $conversations,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Send message
     */
    public function send()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $this->requireAuth();

        $recipientId = intval($_POST['recipient_id'] ?? 0);
        $content = Security::sanitize($_POST['content'] ?? '');
        $userId = $this->auth->getUserId();

        if (empty($content)) {
            $_SESSION['error'] = 'Message cannot be empty';
            $this->redirect('/messages/' . $recipientId);
        }

        if ($recipientId == $userId) {
            $_SESSION['error'] = 'Invalid recipient';
            $this->redirect('/messages');
        }

        $userModel = new User();
        $recipient = $userModel->find($recipientId);

        if (!$recipient) {
            $_SESSION['error'] = 'Recipient not found';
            $this->redirect('/messages');
        }

        $messageModel = new Message();
        $messageModel->create([
            'sender_id' => $userId,
            'receiver_id' => $recipientId,
            'content' => $content,
            'is_read' => 0
        ]);

        // Create notification
        $this->db->insert('notifications', [
            'user_id' => $recipientId,
            'from_user_id' => $userId,
            'type' => 'message'
        ]);

        $_SESSION['success'] = 'Message sent';
        $this->redirect('/messages/' . $recipientId);
    }

    /**
     * Send message via API (AJAX)
     */
    public function apiSend()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $this->requireAuth();

        $recipientId = intval($_POST['recipient_id'] ?? 0);
        $content = Security::sanitize($_POST['content'] ?? '');
        $userId = $this->auth->getUserId();

        if (empty($content)) {
            $this->json(['success' => false, 'error' => 'Message cannot be empty'], 400);
        }

        if ($recipientId == $userId) {
            $this->json(['success' => false, 'error' => 'Invalid recipient'], 400);
        }

        $userModel = new User();
        $recipient = $userModel->find($recipientId);

        if (!$recipient) {
            $this->json(['success' => false, 'error' => 'Recipient not found'], 404);
        }

        $messageModel = new Message();
        $messageId = $messageModel->create([
            'sender_id' => $userId,
            'receiver_id' => $recipientId,
            'content' => $content,
            'is_read' => 0
        ]);

        // Create notification
        $this->db->insert('notifications', [
            'user_id' => $recipientId,
            'from_user_id' => $userId,
            'type' => 'message'
        ]);

        $this->json([
            'success' => true,
            'message_id' => $messageId,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
