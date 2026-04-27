<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 760px;">
    <div class="d-flex align-items-center justify-content-between mb-lg">
        <h2>Fil public</h2>
        <a href="post/create" class="btn btn-primary">✍️ Créer</a>
    </div>

    <?php if (empty($posts)): ?>
        <div class="card" style="text-align: center;">
            <p style="color: var(--color-text-secondary); padding: var(--spacing-xl) 0;">
                Aucune publication disponible
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article class="card ec-post-card">
                <div class="card-header">
                    <div class="d-flex align-items-center" style="gap: var(--spacing-md); flex: 1;">
                        <img src="<?php echo $post['profile_photo'] ? '/' . $post['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($post['first_name']); ?>" 
                             class="avatar">
                        <div>
                            <a href="profile/<?php echo $post['user_id']; ?>" style="color: var(--color-text); text-decoration: none; font-weight: 500;">
                                <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>
                            </a>
                            <p style="font-size: 0.85rem; color: var(--color-text-secondary);">
                                <?php echo date('d M Y à H:i', strtotime($post['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (!empty($post['title'])): ?>
                        <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 150))); ?></p>
                    <a href="post/<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm">Lire plus →</a>
                    
                    <?php if (!empty($post['image_path'])): ?>
                        <img src="/<?php echo $post['image_path']; ?>" 
                             alt="Post image" 
                             style="max-width: 100%; height: auto; border-radius: var(--radius-md); margin-top: var(--spacing-md);">
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <div class="ec-post-meta d-flex justify-content-between">
                        <span>❤️ <?php echo (int) $post['likes_count']; ?> mentions J'aime</span>
                        <span>💬 <?php echo (int) $post['comments_count']; ?> commentaires</span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
