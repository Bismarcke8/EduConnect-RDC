<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="messages-container">
    <!-- Conversations Sidebar -->
    <div class="messages-sidebar">
        <div class="messages-header">
            <h2>💬 Messages</h2>
        </div>

        <div class="conversations-list">
            <?php if (empty($conversations)): ?>
                <div style="padding: var(--spacing-lg); text-align: center; color: var(--color-text-secondary);">
                    Aucune conversation pour le moment
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <a href="messages/<?php echo $conv['id']; ?>"
                       class="conversation-item <?php echo (isset($_GET['id']) && $_GET['id'] == $conv['id']) ? 'active' : ''; ?>">
                        <img src="<?php echo $conv['profile_photo'] ? '/' . $conv['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>"
                             alt="<?php echo htmlspecialchars($conv['first_name']); ?>"
                             class="avatar">
                        <div class="conversation-info">
                            <div class="conversation-name">
                                <?php echo htmlspecialchars($conv['first_name'] . ' ' . $conv['last_name']); ?>
                            </div>
                            <p class="conversation-preview">
                                <?php echo htmlspecialchars(substr($conv['last_message'], 0, 40)); ?>
                            </p>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                            <span class="conversation-time">
                                <?php echo date('H:i', strtotime($conv['last_message_time'])); ?>
                            </span>
                            <?php if ($conv['unread_count'] > 0): ?>
                                <span class="unread-badge">
                                    <?php echo $conv['unread_count']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="messages-main">
        <div class="chat-header">
            <div class="chat-header-info">
                <h4>Boite de reception</h4>
                <p><?php echo count($conversations); ?> conversation(s)</p>
            </div>
        </div>

        <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--color-text-secondary);">
            <div style="text-align: center; max-width: 420px;">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">💬</div>
                <h3 class="mb-2">Selectionnez une conversation</h3>
                <p class="mb-3">Choisissez un contact dans la liste de gauche pour afficher les messages.</p>
                <a href="search" class="btn btn-primary">Trouver des etudiants</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
