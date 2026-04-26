<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
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
                <a href="profile?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-block" style="margin-top: var(--spacing-md);">Voir le profil</a>
            </div>

            <div class="card">
                <h3 style="margin-bottom: var(--spacing-md);">🔔 Suggestions</h3>
                <p style="color: var(--color-text-secondary); text-align: center;">Aucune suggestion pour le moment</p>
            </div>
        </aside>

        <!-- Main Feed -->
        <div style="grid-column: 2;">
            <div class="card">
                <div class="card-body">
                    <a href="post/create" class="btn btn-primary btn-block">✍️ Créer une publication</a>
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
                    <div class="card">
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
                            <div class="d-flex" style="gap: var(--spacing-md);">
                                <button class="btn-like-post" data-post-id="<?php echo $post['id']; ?>" style="border: none; background: none; cursor: pointer; color: var(--color-text-secondary);">
                                    <?php echo $post['liked_by_user'] ? '❤️' : '🤍'; ?> 
                                    <span><?php echo $post['likes_count']; ?></span>
                                </button>
                                <button onclick="document.location='/post/<?php echo $post['id']; ?>#comments'" style="border: none; background: none; cursor: pointer; color: var(--color-text-secondary);">
                                    💬 <span><?php echo $post['comments_count']; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <aside style="grid-column: 3;">
            <div class="card">
                <h3 style="margin-bottom: var(--spacing-md);">🔍 Rechercher</h3>
                <form action="/search" method="POST">
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
        
        fetch('/post/' + postId + '/like', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'post_id=' + postId
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const icon = this.querySelector('span').parentElement;
                if (data.liked) {
                    this.innerHTML = '❤️ <span>' + data.likes_count + '</span>';
                } else {
                    this.innerHTML = '🤍 <span>' + data.likes_count + '</span>';
                }
            }
        });
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
