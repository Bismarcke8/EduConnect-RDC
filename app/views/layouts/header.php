<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo APP_BASE_PATH; ?>/">
    <meta name="app-base-path" content="<?php echo APP_BASE_PATH; ?>">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php
    $pendingInvitesCount = 0;
    if (isset($_SESSION['user_id'])) {
        try {
            $inviteUserModel = new \App\Models\User();
            $pendingInvitesCount = $inviteUserModel->countIncomingPendingInvites((int) $_SESSION['user_id']);
        } catch (\Throwable $e) {
            $pendingInvitesCount = 0;
        }
    }
    ?>
    <header class="ec-topbar">
        <div class="container">
            <nav class="ec-nav">
                <div class="ec-nav-left">
                    <a href="./" class="ec-logo">e</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="search" method="POST" class="ec-search-form">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="text" name="query" class="ec-search-input" placeholder="Rechercher sur EduConnect..." required>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <ul class="ec-nav-center">
                        <li><a href="feed" class="ec-nav-link">Accueil</a></li>
                        <li><a href="posts" class="ec-nav-link">Publications</a></li>
                        <li><a href="messages" class="ec-nav-link">Messages</a></li>
                        <li>
                            <a href="user/invitations" class="ec-nav-link ec-nav-link-with-badge">
                                Invitations
                                <?php if ($pendingInvitesCount > 0): ?>
                                    <span class="ec-nav-badge"><?php echo (int) $pendingInvitesCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="notifications" class="ec-nav-link">Notifications</a></li>
                    </ul>

                    <div class="ec-nav-right">
                        <a href="profile/<?php echo $_SESSION['user_id']; ?>" class="btn btn-secondary btn-sm">Profil</a>
                        <a href="auth/logout" class="btn btn-danger btn-sm">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <div class="ec-nav-right">
                        <a href="auth/login" class="btn btn-secondary btn-sm">Connexion</a>
                        <a href="auth/register" class="btn btn-primary btn-sm">S'inscrire</a>
                    </div>
                <?php endif; ?>
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
