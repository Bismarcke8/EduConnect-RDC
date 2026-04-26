<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 700px;">
    <h2>Résultats de recherche (<?php echo $count; ?> résultats)</h2>

    <?php if (empty($results)): ?>
        <div class="card" style="text-align: center;">
            <p style="color: var(--color-text-secondary); padding: var(--spacing-xl) 0;">
                Aucun étudiant trouvé pour votre recherche
            </p>
            <a href="/search" class="btn btn-secondary">Nouvelle recherche</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: var(--spacing-lg);">
            <?php foreach ($results as $user): ?>
                <div class="card">
                    <div style="text-align: center; padding: var(--spacing-lg);">
                        <img src="<?php echo $user['profile_photo'] ? '/' . $user['profile_photo'] : '/EduConnect-RDC/public/assets/images/default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($user['first_name']); ?>" 
                             class="avatar avatar-lg"
                             style="margin-bottom: var(--spacing-md);">
                        <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                        <?php if (!empty($user['university'])): ?>
                            <p style="color: var(--color-text-secondary); font-size: 0.9rem;">
                                <?php echo htmlspecialchars($user['university']); ?>
                            </p>
                        <?php endif; ?>
                        <a href="/profile?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm btn-block" style="margin-top: var(--spacing-md);">
                            Voir le profil
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
