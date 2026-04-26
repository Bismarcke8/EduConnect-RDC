<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Security;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
    }

    /**
     * Show user feed
     */
    public function feed()
    {
        $this->requireAuth();

        $postModel = new Post();
        $userModel = new User();

        $page = intval($_GET['page'] ?? 1);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $userId = $this->auth->getUserId();
        $posts = $userModel->getFeed($userId, ITEMS_PER_PAGE, $offset);

        foreach ($posts as &$post) {
            $post['comments'] = $postModel->getComments($post['id']);
            $post['liked_by_user'] = $postModel->isLikedBy($post['id'], $userId);
        }

        $this->view('post/feed', [
            'posts' => $posts,
            'page' => $page,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Show all posts
     */
    public function list()
    {
        $postModel = new Post();

        $page = intval($_GET['page'] ?? 1);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $posts = $postModel->getAllWithStats(ITEMS_PER_PAGE, $offset);

        $userId = $this->auth->getUserId();
        if ($userId) {
            foreach ($posts as &$post) {
                $post['liked_by_user'] = $postModel->isLikedBy($post['id'], $userId);
            }
        }

        $this->view('post/list', [
            'posts' => $posts,
            'page' => $page
        ]);
    }

    /**
     * Show single post
     */
    public function show()
    {
        $postId = intval($_GET['id'] ?? 0);

        $postModel = new Post();
        $post = $postModel->getWithStats($postId);

        if (!$post) {
            http_response_code(404);
            echo "Post not found";
            return;
        }

        $post['comments'] = $postModel->getComments($postId);

        $userId = $this->auth->getUserId();
        if ($userId) {
            $post['liked_by_user'] = $postModel->isLikedBy($postId, $userId);
        }

        $this->view('post/show', [
            'post' => $post,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Show create post form
     */
    public function create()
    {
        $this->requireAuth();

        $this->view('post/create', [
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    /**
     * Store new post
     */
    public function store()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/post/create');
        }

        $title = Security::sanitize($_POST['title'] ?? '');
        $content = Security::sanitize($_POST['content'] ?? '');

        if (empty($content)) {
            $_SESSION['error'] = 'Post content is required';
            $this->redirect('/post/create');
        }

        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $validation = Security::validateFileUpload($_FILES['image']);
            if (!$validation['valid']) {
                $_SESSION['error'] = $validation['error'];
                $this->redirect('/post/create');
            }

            $filename = Security::generateFileName($_FILES['image']['name']);
            $destination = UPLOAD_PATH . 'posts/' . $filename;

            if (!is_dir(UPLOAD_PATH . 'posts/')) {
                mkdir(UPLOAD_PATH . 'posts/', 0755, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = 'uploads/posts/' . $filename;
            }
        }

        $postModel = new Post();
        $postId = $postModel->create([
            'user_id' => $this->auth->getUserId(),
            'title' => $title,
            'content' => $content,
            'image_path' => $imagePath,
            'is_published' => 1
        ]);

        $_SESSION['success'] = 'Post created successfully';
        $this->redirect('/post/' . $postId);
    }

    /**
     * Update post
     */
    public function update()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $postId = intval($_POST['post_id'] ?? 0);
        $postModel = new Post();
        $post = $postModel->find($postId);

        if (!$post || $post['user_id'] != $this->auth->getUserId()) {
            http_response_code(403);
            die('You do not have permission to edit this post');
        }

        if (!Security::verifyToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request';
            $this->redirect('/post/' . $postId);
        }

        $title = Security::sanitize($_POST['title'] ?? '');
        $content = Security::sanitize($_POST['content'] ?? '');

        if (empty($content)) {
            $_SESSION['error'] = 'Post content is required';
            $this->redirect('/post/' . $postId);
        }

        $postModel->update($postId, [
            'title' => $title,
            'content' => $content
        ]);

        $_SESSION['success'] = 'Post updated successfully';
        $this->redirect('/post/' . $postId);
    }

    /**
     * Delete post
     */
    public function delete()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $postId = intval($_POST['post_id'] ?? 0);
        $postModel = new Post();
        $post = $postModel->find($postId);

        if (!$post || $post['user_id'] != $this->auth->getUserId()) {
            http_response_code(403);
            die('You do not have permission to delete this post');
        }

        $postModel->delete($postId);

        // Also delete likes and comments
        $this->db->delete('likes', ['post_id' => $postId]);
        $this->db->delete('comments', ['post_id' => $postId]);
        $this->db->delete('notifications', ['post_id' => $postId]);

        $_SESSION['success'] = 'Post deleted successfully';
        $this->redirect('/feed');
    }

    /**
     * Like post
     */
    public function like()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $this->requireAuth();

        $postId = intval($_POST['post_id'] ?? 0);
        $userId = $this->auth->getUserId();

        $postModel = new Post();
        $post = $postModel->find($postId);

        if (!$post) {
            $this->json(['success' => false, 'error' => 'Post not found'], 404);
        }

        // Check if already liked
        $alreadyLiked = $postModel->isLikedBy($postId, $userId);

        if ($alreadyLiked) {
            // Unlike
            $this->db->delete('likes', ['post_id' => $postId, 'user_id' => $userId]);
        } else {
            // Like
            $this->db->insert('likes', ['post_id' => $postId, 'user_id' => $userId]);

            // Create notification
            if ($post['user_id'] != $userId) {
                $this->db->insert('notifications', [
                    'user_id' => $post['user_id'],
                    'from_user_id' => $userId,
                    'type' => 'like',
                    'post_id' => $postId
                ]);
            }
        }

        $likesCount = $this->db->count('likes', ['post_id' => $postId]);

        $this->json(['success' => true, 'liked' => !$alreadyLiked, 'likes_count' => $likesCount]);
    }

    /**
     * Add comment
     */
    public function comment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $this->requireAuth();

        $postId = intval($_POST['post_id'] ?? 0);
        $content = Security::sanitize($_POST['content'] ?? '');
        $userId = $this->auth->getUserId();

        if (empty($content)) {
            $this->json(['success' => false, 'error' => 'Comment cannot be empty'], 400);
        }

        $postModel = new Post();
        $post = $postModel->find($postId);

        if (!$post) {
            $this->json(['success' => false, 'error' => 'Post not found'], 404);
        }

        $commentId = $this->db->insert('comments', [
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content
        ]);

        // Create notification
        if ($post['user_id'] != $userId) {
            $this->db->insert('notifications', [
                'user_id' => $post['user_id'],
                'from_user_id' => $userId,
                'type' => 'comment',
                'post_id' => $postId,
                'comment_id' => $commentId
            ]);
        }

        $this->json(['success' => true, 'comment_id' => $commentId]);
    }

    /**
     * Get posts via API (for AJAX)
     */
    public function apiGetPosts()
    {
        $page = intval($_GET['page'] ?? 1);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $postModel = new Post();
        $posts = $postModel->getAllWithStats(ITEMS_PER_PAGE, $offset);

        $userId = $this->auth->getUserId();
        if ($userId) {
            foreach ($posts as &$post) {
                $post['liked_by_user'] = $postModel->isLikedBy($post['id'], $userId);
            }
        }

        $this->json(['success' => true, 'posts' => $posts, 'page' => $page]);
    }
}

