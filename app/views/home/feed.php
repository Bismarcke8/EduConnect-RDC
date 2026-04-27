<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl ec-feed-page">
    <div class="grid-3" style="gap: var(--spacing-lg);">
        <!-- Sidebar Left -->
        <aside style="grid-column: 1;">
            <div class="card">
                <h3 style="margin-bottom: var(--spacing-md);">📊 Votre Profil</h3>
                <div style="text-align: center;">
                    <img src="<?php echo $user['profile_photo'] ? '/' . $user['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                         alt="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" 
                         class="avatar avatar-lg" 
                         style="margin-bottom: var(--spacing-md);">
                    <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                    <p style="color: var(--color-text-secondary);"><?php echo htmlspecialchars($user['university'] ?? ''); ?></p>
                </div>
                <a href="profile/<?php echo $user['id']; ?>" class="btn btn-secondary btn-block" style="margin-top: var(--spacing-md);">Voir le profil</a>
            </div>

            <div class="card">
                <h3 style="margin-bottom: var(--spacing-md);">🔔 Suggestions</h3>
                <p style="color: var(--color-text-secondary); text-align: center;">Aucune suggestion pour le moment</p>
            </div>
        </aside>

        <!-- Main Feed -->
        <div style="grid-column: 2;">
            <div class="card ec-composer-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Exprime-toi</h5>
                        <a href="post/create" class="btn btn-primary">✍️ Créer une publication</a>
                    </div>
                </div>
            </div>

            <?php if (empty($posts)): ?>
                <div class="card" style="text-align: center;">
                    <p style="color: var(--color-text-secondary); padding: var(--spacing-xl) 0;">
                        Aucune publication disponible. <a href="search">Cherchez des utilisateurs à suivre</a>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="card ec-post-card">
                        <div class="card-header">
                            <div class="d-flex align-items-center" style="gap: var(--spacing-md); flex: 1;">
                                <img src="<?php echo $post['profile_photo'] ? '/' . $post['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>" 
                                     class="avatar">
                                <div>
                                    <div class="d-flex align-items-center" style="gap: var(--spacing-sm);">
                                        <strong><?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></strong>
                                        <?php if (isset($post['is_official']) && $post['is_official']): ?>
                                            <span class="badge badge-official">🏛️ Officiel</span>
                                        <?php endif; ?>
                                    </div>
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
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if (!empty($post['image_path'])): ?>
                                <img src="/<?php echo $post['image_path']; ?>" 
                                     alt="Post image" 
                                     style="max-width: 100%; height: auto; border-radius: var(--radius-md); margin-top: var(--spacing-md);">
                            <?php endif; ?>
                        </div>

                        <div class="card-footer">
                            <div class="ec-post-meta d-flex justify-content-between mb-sm">
                                <span>❤️ <?php echo (int) $post['likes_count']; ?> mentions J'aime</span>
                                <span>💬 <?php echo (int) $post['comments_count']; ?> commentaires</span>
                            </div>
                            <div class="ec-post-actions">
                                <button class="btn-like-post ec-action-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <?php echo $post['liked_by_user'] ? '❤️ J’aime' : '🤍 J’aime'; ?>
                                </button>
                                <button onclick="document.location='post/<?php echo $post['id']; ?>#comments'" class="ec-action-btn">
                                    💬 Commenter
                                </button>
                                <button type="button" class="ec-action-btn" onclick="navigator.clipboard.writeText(window.location.origin + '<?php echo APP_BASE_PATH; ?>/post/<?php echo $post['id']; ?>')">
                                    ↗️ Partager
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <aside style="grid-column: 3;">
            <div class="card">
                <h3 style="margin-bottom: var(--spacing-md);">🔍 Rechercher</h3>
                <form action="search" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="text" name="query" placeholder="Chercher un étudiant..." required style="margin-bottom: var(--spacing-md);">
                    <button type="submit" class="btn btn-primary btn-block">Rechercher</button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-bottom: var(--spacing-md);">📝 Tendances</h3>
                <ul style="list-style: none;">
                    <li style="padding: var(--spacing-sm); border-bottom: 1px solid var(--color-border);">
                        <a href="#" style="color: var(--color-accent); text-decoration: none; font-weight: 500;">Web Development</a>
                    </li>
                    <li style="padding: var(--spacing-sm); border-bottom: 1px solid var(--color-border);">
                        <a href="#" style="color: var(--color-accent); text-decoration: none; font-weight: 500;">Machine Learning</a>
                    </li>
                    <li style="padding: var(--spacing-sm);">
                        <a href="#" style="color: var(--color-accent); text-decoration: none; font-weight: 500;">Data Science</a>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<script>
document.querySelectorAll('.btn-like-post').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch(basePath + '/post/' + postId + '/like', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'post_id=' + postId + '&csrf_token=' + encodeURIComponent(csrfToken)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.liked) {
                    this.innerHTML = '❤️ J’aime';
                } else {
                    this.innerHTML = '🤍 J’aime';
                }
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
