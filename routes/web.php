<?php

use Core\Router;

$router = new Router();

// ==========================================
// PUBLIC ROUTES
// ==========================================

// Home
$router->get('/', 'HomeController@index');

// Auth Routes
$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@handleLogin');
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@handleRegister');
$router->get('/auth/logout', 'AuthController@logout');

// ==========================================
// PROTECTED ROUTES (Student/User)
// ==========================================

// User Routes
$router->get('/profile/:id', 'UserController@profile');
$router->get('/user/invitations', 'UserController@invitations');
$router->get('/user/settings', 'UserController@settings');
$router->post('/user/update-profile', 'UserController@updateProfile');
$router->post('/user/change-password', 'UserController@changePassword');
$router->post('/user/upload-photo', 'UserController@uploadPhoto');

// Feed/Posts Routes
$router->get('/feed', 'PostController@feed');
$router->get('/posts', 'PostController@list');
$router->get('/post/:id', 'PostController@show');
$router->get('/post/create', 'PostController@create');
$router->post('/post/store', 'PostController@store');
$router->post('/post/:id/update', 'PostController@update');
$router->post('/post/:id/delete', 'PostController@delete');
$router->post('/post/:id/like', 'PostController@like');
$router->post('/post/:id/comment', 'PostController@comment');

// Message Routes
$router->get('/messages', 'MessageController@inbox');
$router->get('/messages/:id', 'MessageController@conversation');
$router->post('/messages/send', 'MessageController@send');

// Search Routes
$router->get('/search', 'SearchController@index');
$router->post('/search', 'SearchController@search');

// Follow Routes
$router->post('/user/:id/follow', 'UserController@follow');
$router->post('/user/:id/unfollow', 'UserController@unfollow');
$router->post('/user/:id/invite', 'UserController@sendInvite');
$router->post('/user/:id/invite/accept', 'UserController@acceptInvite');
$router->post('/user/:id/invite/decline', 'UserController@declineInvite');

// Notifications Routes
$router->get('/notifications', 'NotificationController@index');
$router->post('/notification/:id/read', 'NotificationController@markAsRead');

// ==========================================
// ADMIN ROUTES
// ==========================================

$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/posts', 'AdminController@posts');
$router->post('/admin/user/:id/ban', 'AdminController@banUser');
$router->post('/admin/post/:id/delete', 'AdminController@deletePost');
$router->post('/admin/create-post', 'AdminController@createPost');
$router->get('/admin/logs', 'AdminController@logs');
$router->get('/admin/stats', 'AdminController@stats');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/update-settings', 'AdminController@updateSettings');

// ==========================================
// API ROUTES (AJAX)
// ==========================================

$router->get('/api/posts', 'PostController@apiGetPosts');
$router->get('/api/notifications', 'NotificationController@apiGetNotifications');
$router->post('/api/message/send', 'MessageController@apiSend');

// Dispatch the router
$router->dispatch();
