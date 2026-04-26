<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\User;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Show homepage
     */
    public function index()
    {
        $auth = new Auth();

        if ($auth->isAuthenticated()) {
            // Show feed for authenticated users
            $postModel = new Post();
            $userModel = new User();
            
            $userId = $auth->getUserId();
            $posts = $postModel->getAllWithStats(15, 0);
            $user = $userModel->find($userId);
            $unreadMessages = $this->db->count('messages', ['receiver_id' => $userId, 'is_read' => 0]);

            $this->view('home/feed', [
                'posts' => $posts,
                'user' => $user,
                'unreadMessages' => $unreadMessages
            ]);
        } else {
            // Show landing page for guests
            $this->view('home/index');
        }
    }
}

