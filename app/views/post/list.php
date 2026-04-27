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
            <article class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center" style="gap: var(--spacing-md); flex: 1;">
                        <img src="<?php echo $post['profile_photo'] ? APP_BASE_PATH . '/uploads/' . $post['profile_photo'] : 'https://via.placeholder.com/40x40/4cc2ff/ffffff?text=' . urlencode(substr($post['first_name'], 0, 1) . substr($post['last_name'], 0, 1)); ?>" 
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
                        <h4 style="color: #ffffff !important; font-weight: 600;"><?php echo htmlspecialchars($post['title']); ?></h4>
                    <?php endif; ?>
                    <p style="color: #e0e0e0 !important; line-height: 1.6;"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 150))); ?></p>
                    <a href="post/<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm">Lire plus →</a>
                    
                    <?php if (!empty($post['image_path'])): ?>
                        <img src="<?php echo APP_BASE_PATH . '/uploads/posts/' . $post['image_path']; ?>" 
                             alt="Post image" 
                             style="max-width: 100%; height: auto; border-radius: var(--radius-md); margin-top: var(--spacing-md);">
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <div class="ec-post-meta d-flex justify-content-between">
                        <div class="d-flex" style="gap: var(--spacing-lg);">
                            <form method="POST" action="post/<?php echo $post['id']; ?>/like" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-link p-0 <?php echo $post['is_liked'] ? 'text-primary' : 'text-muted'; ?>" style="border: none; background: none;">
                                    ❤️ <?php echo (int) $post['likes_count']; ?> J'aime
                                </button>
                            </form>
                            <a href="post/<?php echo $post['id']; ?>" style="color: var(--color-text-secondary); text-decoration: none;">
                                💬 <?php echo (int) $post['comments_count']; ?> Commentaires
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
