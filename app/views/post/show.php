<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 700px;">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center" style="gap: var(--spacing-md);">
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
                <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <?php endif; ?>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <?php if (!empty($post['image_path'])): ?>
                <img src="/<?php echo $post['image_path']; ?>" 
                     alt="Post image" 
                     style="max-width: 100%; height: auto; border-radius: var(--radius-md); margin-top: var(--spacing-lg);">
            <?php endif; ?>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <div style="display: flex; gap: var(--spacing-lg);">
                    <button class="btn-like" data-post-id="<?php echo $post['id']; ?>" style="border: none; background: none; cursor: pointer; color: var(--color-text-secondary);">
                        <?php echo $post['liked_by_user'] ? '❤️' : '🤍'; ?> 
                        <span><?php echo $post['likes_count']; ?> J'aime</span>
                    </button>
                    <span style="color: var(--color-text-secondary);">💬 <?php echo count($post['comments']); ?> Commentaires</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div id="comments">
        <h3 style="margin-top: var(--spacing-xl); margin-bottom: var(--spacing-lg);">Commentaires</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="card mb-lg">
                <form method="POST" action="/post/<?php echo $post['id']; ?>/comment" class="card-body">
                    <textarea name="content" placeholder="Écrire un commentaire..." required style="margin-bottom: var(--spacing-md);"></textarea>
                    <button type="submit" class="btn btn-primary">Commenter</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (empty($post['comments'])): ?>
            <div class="card" style="text-align: center;">
                <p style="color: var(--color-text-secondary); padding: var(--spacing-lg) 0;">
                    Aucun commentaire pour le moment. Soyez le premier à commenter!
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($post['comments'] as $comment): ?>
                <div class="card mb-md">
                    <div class="card-header">
                        <div class="d-flex align-items-center" style="gap: var(--spacing-md);">
                            <img src="<?php echo $comment['profile_photo'] ? '/' . $comment['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($comment['first_name']); ?>" 
                                 class="avatar-sm">
                            <div>
                                <a href="profile?id=<?php echo $comment['user_id']; ?>" style="color: var(--color-text); text-decoration: none; font-weight: 500;">
                                    <?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?>
                                </a>
                                <p style="font-size: 0.85rem; color: var(--color-text-secondary);">
                                    <?php echo date('d M Y à H:i', strtotime($comment['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="posts" class="btn btn-secondary" style="margin-top: var(--spacing-lg);">← Retour aux publications</a>
</div>

<script>
document.querySelector('.btn-like').addEventListener('click', function(e) {
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
            if (data.liked) {
                this.innerHTML = '❤️ <span>' + data.likes_count + ' J\'aime</span>';
            } else {
                this.innerHTML = '🤍 <span>' + data.likes_count + ' J\'aime</span>';
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
