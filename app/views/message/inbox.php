<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <div class="grid-2" style="gap: var(--spacing-lg); grid-template-columns: 300px 1fr;">
        <!-- Conversations List -->
        <aside>
            <div class="card">
                <h2 style="margin-bottom: var(--spacing-md);">💬 Messages</h2>
                
                <?php if (empty($conversations)): ?>
                    <p style="color: var(--color-text-secondary); text-align: center;">
                        Aucune conversation pour le moment
                    </p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <?php foreach ($conversations as $conv): ?>
                            <a href="messages/<?php echo $conv['id']; ?>" 
                               class="d-flex align-items-center"
                               style="padding: var(--spacing-md); border-radius: var(--radius-md); text-decoration: none; background-color: var(--color-surface-alt); color: var(--color-text); gap: var(--spacing-md);">
                                <img src="<?php echo $conv['profile_photo'] ? '/' . $conv['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($conv['first_name']); ?>" 
                                     class="avatar-sm">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($conv['first_name'] . ' ' . $conv['last_name']); ?>
                                    </div>
                                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars(substr($conv['last_message'], 0, 30)); ?>
                                    </p>
                                </div>
                                <?php if ($conv['unread_count'] > 0): ?>
                                    <span style="background: var(--color-accent); color: var(--color-bg); border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                        <?php echo $conv['unread_count']; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Conversation Area -->
        <main>
            <div class="card">
                <p style="color: var(--color-text-secondary); text-align: center; padding: var(--spacing-xl) 0;">
                    Sélectionnez une conversation pour commencer à discuter
                </p>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
