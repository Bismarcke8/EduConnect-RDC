<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 700px;">
    <h2>🔔 Notifications</h2>

    <?php if (empty($notifications)): ?>
        <div class="card" style="text-align: center;">
            <p style="color: var(--color-text-secondary); padding: var(--spacing-xl) 0;">
                Aucune notification
            </p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
            <?php foreach ($notifications as $notif): ?>
                <div class="card">
                    <div class="d-flex align-items-center" style="gap: var(--spacing-md);">
                        <img src="<?php echo $notif['profile_photo'] ? '/' . $notif['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($notif['first_name']); ?>" 
                             class="avatar">
                        <div style="flex: 1;">
                            <?php 
                            $message = '';
                            $url = '#';
                            switch ($notif['type']) {
                                case 'like':
                                    $message = 'a aimé votre publication';
                                    $url = 'post/' . intval($notif['post_id']);
                                    break;
                                case 'comment':
                                    $message = 'a commenté votre publication';
                                    $url = 'post/' . intval($notif['post_id']);
                                    break;
                                case 'message':
                                    $message = 'vous a envoyé un message';
                                    $url = 'messages/' . intval($notif['from_user_id']);
                                    break;
                                case 'invite':
                                    $message = 'vous a envoyé une invitation';
                                    $url = 'user/invitations';
                                    break;
                                case 'publication':
                                    $message = 'a publié une nouvelle publication';
                                    $url = 'post/' . intval($notif['post_id']);
                                    break;
                                case 'follow':
                                    $message = 'vous suit';
                                    $url = 'profile/' . intval($notif['from_user_id']);
                                    break;
                            }
                            ?>
                            <a href="<?php echo $url; ?>" style="text-decoration: none; color: inherit;">
                                <strong><?php echo htmlspecialchars($notif['first_name'] . ' ' . $notif['last_name']); ?></strong>
                                <span style="color: var(--color-text-secondary);"> <?php echo $message; ?></span>
                            </a>
                            <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-top: var(--spacing-sm);">
                                <?php echo date('d M Y à H:i', strtotime($notif['created_at'])); ?>
                            </p>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <form method="POST" action="notification/<?php echo $notif['id']; ?>/read" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Marquer comme lue</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
