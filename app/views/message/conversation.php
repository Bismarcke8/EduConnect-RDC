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
                    Aucune conversation
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <a href="messages/<?php echo $conv['id']; ?>"
                       class="conversation-item <?php echo $conv['id'] == $recipient['id'] ? 'active' : ''; ?>">
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
        <!-- Chat Header -->
        <div class="chat-header">
            <img src="<?php echo $recipient['profile_photo'] ? '/' . $recipient['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>"
                 alt="<?php echo htmlspecialchars($recipient['first_name']); ?>"
                 class="avatar">
            <div class="chat-header-info">
                <h4><?php echo htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']); ?></h4>
                <p>En ligne</p>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="messages-container">
            <?php if (empty($messages)): ?>
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--color-text-secondary);">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">💬</div>
                        <h3>Aucun message</h3>
                        <p>Soyez le premier à écrire!</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message-group <?php echo $message['sender_id'] === $_SESSION['user_id'] ? 'sent' : 'received'; ?>">
                        <div class="message-bubble">
                            <div><?php echo nl2br(htmlspecialchars($message['content'])); ?></div>
                            <div class="message-time">
                                <?php echo date('H:i', strtotime($message['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Message Input Area -->
        <div class="chat-input-area">
            <form method="POST" action="messages/send" class="message-form">
                <input type="hidden" name="recipient_id" value="<?php echo $recipient['id']; ?>">
                <textarea name="content"
                          class="message-input"
                          placeholder="Écrivez un message..."
                          required
                          rows="1"
                          onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.submit(); }"></textarea>
                <button type="submit" class="send-button">Envoyer</button>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-resize textarea
document.querySelector('.message-input').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Scroll to bottom of messages
document.getElementById('messages-container').scrollTop = document.getElementById('messages-container').scrollHeight;
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
