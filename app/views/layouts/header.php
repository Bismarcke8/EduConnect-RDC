<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/EduConnect-RDC/public/">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="./" class="navbar-brand">🎓 EduConnect-RDC</a>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="feed">Feed</a></li>
                        <li><a href="posts">Publications</a></li>
                        <li><a href="search">Rechercher</a></li>
                        <li><a href="messages" style="position: relative;">
                            Messages
                            <?php if (isset($unreadMessages) && $unreadMessages > 0): ?>
                                <span style="position: absolute; top: -5px; right: -10px; background: #e74c3c; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                    <?php echo $unreadMessages; ?>
                                </span>
                            <?php endif; ?>
                        </a></li>
                        <li><a href="notifications">Notifications</a></li>
                        <li><a href="profile?id=<?php echo $_SESSION['user_id']; ?>">Profil</a></li>
                        <li><a href="auth/logout">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="auth/login">Connexion</a></li>
                        <li><a href="auth/register" class="btn btn-primary btn-sm">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Display session messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mt-md">
            <div class="alert alert-success">
                <span>✓</span>
                <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="container mt-md">
            <div class="alert alert-danger">
                <span>✕</span>
                <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            </div>
        </div>
    <?php endif; ?>

    <main>
