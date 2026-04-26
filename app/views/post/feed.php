<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 700px;">
    <h2 style="margin-bottom: var(--spacing-lg);">📝 Fil d'actualité</h2>

    <?php if (empty($posts)): ?>
        <div class="card" style="text-align: center;">
            <p style="color: var(--color-text-secondary); padding: var(--spacing-xl) 0;">
                Aucune publication disponible
            </p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center" style="gap: var(--spacing-md); flex: 1;">
                        <img src="<?php echo $post['profile_photo'] ? '/' . $post['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($post['first_name']); ?>" 
                             class="avatar">
                        <div>
                            <a href="profile?id=<?php echo $post['user_id']; ?>" style="color: var(--color-text); text-decoration: none; font-weight: 500;">
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
                    <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?></p>
                    <?php if (strlen($post['content']) > 200): ?>
                        <a href="post/<?php echo $post['id']; ?>" style="color: var(--color-accent);">Lire plus...</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($post['image_path'])): ?>
                        <img src="/<?php echo $post['image_path']; ?>" 
                             alt="Post image" 
                             style="max-width: 100%; height: auto; border-radius: var(--radius-md); margin-top: var(--spacing-md);">
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <div class="d-flex" style="gap: var(--spacing-lg);">
                        <span style="color: var(--color-text-secondary);">❤️ <?php echo $post['likes_count']; ?></span>
                        <a href="post/<?php echo $post['id']; ?>" style="color: var(--color-text-secondary); text-decoration: none;">
                            💬 <?php echo $post['comments_count']; ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($page > 1): ?>
        <a href="posts?page=<?php echo $page - 1; ?>" class="btn btn-secondary">← Précédent</a>
    <?php endif; ?>
    
    <?php if (!empty($posts) && count($posts) === ITEMS_PER_PAGE): ?>
        <a href="/posts?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Suivant →</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
