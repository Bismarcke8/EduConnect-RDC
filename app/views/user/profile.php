<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <div class="grid-3" style="gap: var(--spacing-lg);">
        <!-- Left Sidebar -->
        <aside style="grid-column: 1;">
            <div class="card">
                <div style="text-align: center;">
                    <img src="<?php echo $user['profile_photo'] ? '/' . $user['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                         alt="<?php echo htmlspecialchars($user['first_name']); ?>" 
                         class="avatar avatar-lg"
                         style="margin-bottom: var(--spacing-lg);">
                    <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                    <?php if (!empty($user['university'])): ?>
                        <p style="color: var(--color-text-secondary); font-size: 0.95rem;">
                            📍 <?php echo htmlspecialchars($user['university']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($user['field_of_study'])): ?>
                        <p style="color: var(--color-text-secondary); font-size: 0.95rem;">
                            🎓 <?php echo htmlspecialchars($user['field_of_study']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($isOwnProfile): ?>
                    <a href="/user/settings" class="btn btn-primary btn-block" style="margin-top: var(--spacing-lg);">Modifier le profil</a>
                <?php elseif ($isFollowing): ?>
                    <form method="POST" action="/user/<?php echo $user['id']; ?>/unfollow">
                        <button type="submit" class="btn btn-secondary btn-block" style="margin-top: var(--spacing-lg);">Following ✓</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="/user/<?php echo $user['id']; ?>/follow">
                        <button type="submit" class="btn btn-primary btn-block" style="margin-top: var(--spacing-lg);">Suivre</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="card">
                <div style="text-align: center;">
                    <div style="padding: var(--spacing-lg) 0; border-bottom: 1px solid var(--color-border);">
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--color-accent);"><?php echo $followersCount; ?></div>
                        <p style="color: var(--color-text-secondary);">Followers</p>
                    </div>
                    <div style="padding: var(--spacing-lg) 0; border-bottom: 1px solid var(--color-border);">
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--color-accent);"><?php echo $followingCount; ?></div>
                        <p style="color: var(--color-text-secondary);">Suivis</p>
                    </div>
                    <div style="padding: var(--spacing-lg) 0;">
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--color-accent);"><?php echo $postsCount; ?></div>
                        <p style="color: var(--color-text-secondary);">Publications</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div style="grid-column: 2 / 4;">
            <?php if (!empty($user['bio'])): ?>
                <div class="card">
                    <h3>À propos</h3>
                    <p><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($skills)): ?>
                <div class="card">
                    <h3>Compétences</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: var(--spacing-sm);">
                        <?php foreach ($skills as $skill): ?>
                            <span class="badge badge-primary"><?php echo htmlspecialchars($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <h3>Publications</h3>
            <!-- Publications will be listed here -->
            <div class="card">
                <p style="color: var(--color-text-secondary); text-align: center;">Aucune publication pour le moment</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
