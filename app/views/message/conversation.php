<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <div class="grid-2" style="gap: var(--spacing-lg); grid-template-columns: 300px 1fr;">
        <!-- Conversations List -->
        <aside>
            <div class="card">
                <h2 style="margin-bottom: var(--spacing-md);">💬 Messages</h2>
                <!-- Conversation list would be here -->
            </div>
        </aside>

        <!-- Conversation Area -->
        <main>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center" style="gap: var(--spacing-md);">
                        <img src="<?php echo $recipient['profile_photo'] ? '/' . $recipient['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($recipient['first_name']); ?>" 
                             class="avatar">
                        <div>
                            <h4 style="margin: 0;"><?php echo htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']); ?></h4>
                            <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin: 0;">En ligne</p>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="height: 400px; overflow-y: auto; padding: var(--spacing-md); border-bottom: 1px solid var(--color-border);">
                    <?php if (empty($messages)): ?>
                        <p style="color: var(--color-text-secondary); text-align: center;">
                            Aucun message. Soyez le premier à écrire!
                        </p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                            <?php foreach ($messages as $message): ?>
                                <div style="display: flex; <?php echo $message['sender_id'] === $_SESSION['user_id'] ? 'justify-content: flex-end;' : ''; ?>">
                                    <div style="max-width: 60%; background-color: <?php echo $message['sender_id'] === $_SESSION['user_id'] ? 'var(--color-accent);' : 'var(--color-surface-alt);'; ?> padding: var(--spacing-md); border-radius: var(--radius-lg); color: <?php echo $message['sender_id'] === $_SESSION['user_id'] ? 'var(--color-bg);' : 'var(--color-text);'; ?>">
                                        <p style="margin: 0;"><?php echo nl2br(htmlspecialchars($message['content'])); ?></p>
                                        <p style="font-size: 0.8rem; margin-top: var(--spacing-sm); opacity: 0.7;">
                                            <?php echo date('H:i', strtotime($message['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <form method="POST" action="messages/send" style="display: flex; gap: var(--spacing-md);">
                        <input type="hidden" name="recipient_id" value="<?php echo $recipient['id']; ?>">
                        <textarea name="content" placeholder="Écrivez un message..." required style="flex: 1;"></textarea>
                        <button type="submit" class="btn btn-primary" style="align-self: flex-end;">Envoyer</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
