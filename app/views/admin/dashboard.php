<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl admin-dashboard py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <h2 class="mb-2 mb-md-0">👨‍💼 Tableau de bord administrateur</h2>
        <div class="d-flex gap-2">
            <a href="admin/stats" class="btn btn-info btn-sm">Statistiques</a>
            <a href="admin/settings" class="btn btn-warning btn-sm">Paramètres</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <h4><?php echo $stats['total_users']; ?></h4>
                <p>Utilisateurs totaux</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <h4><?php echo $stats['active_users']; ?></h4>
                <p>Utilisateurs actifs</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <h4><?php echo $stats['total_posts']; ?></h4>
                <p>Publications</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <h4><?php echo $stats['total_messages']; ?></h4>
                <p>Messages</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Recent Users -->
        <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Utilisateurs récents</h3>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <?php foreach ($recent_users as $user): ?>
                            <tr style="border-bottom: 1px solid var(--color-border); padding: var(--spacing-md);">
                                <td style="padding: var(--spacing-sm);">
                                    <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong><br>
                                    <small style="color: var(--color-text-secondary);"><?php echo htmlspecialchars($user['email']); ?></small>
                                </td>
                                <td style="padding: var(--spacing-sm); text-align: right; font-size: 0.85rem; color: var(--color-text-secondary);">
                                    <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="admin/users" class="btn btn-secondary btn-sm">Voir tous les utilisateurs</a>
            </div>
        </div>
        </div>

        <!-- Recent Posts -->
        <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Publications récentes</h3>
            </div>
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <?php foreach ($recent_posts as $post): ?>
                            <tr style="border-bottom: 1px solid var(--color-border); padding: var(--spacing-md);">
                                <td style="padding: var(--spacing-sm);">
                                    <strong><?php echo htmlspecialchars(substr($post['title'] ?? $post['id'], 0, 30)); ?></strong><br>
                                    <small style="color: var(--color-text-secondary);">par <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></small>
                                </td>
                                <td style="padding: var(--spacing-sm); text-align: right; font-size: 0.85rem; color: var(--color-text-secondary);">
                                    <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="admin/posts" class="btn btn-secondary btn-sm">Voir toutes les publications</a>
            </div>
        </div>
        </div>
    </div>

    <div class="card" style="margin-top: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">✍️ Créer une publication admin</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="admin/create-post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="admin_content">Contenu de la publication</label>
                    <textarea id="admin_content" name="content" rows="4" placeholder="Écrivez votre publication officielle..." required style="width: 100%; padding: var(--spacing-md); border: 1px solid var(--color-border); border-radius: var(--radius-md); background-color: var(--color-surface); color: var(--color-text); resize: vertical;"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_official" value="1" checked>
                        Publication officielle (visible à tous les utilisateurs)
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Publier</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">Navigation admin</h3>
        </div>
        <div class="card-body row g-2">
            <div class="col-12 col-md-6 col-xl-4">
            <a href="admin/users" class="btn btn-primary">
                👥 Gérer les utilisateurs<br>
                <small style="color: var(--color-text-secondary);">Voir, modifier, bannir</small>
            </a>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
            <a href="admin/posts" class="btn btn-primary">
                📝 Gérer les publications<br>
                <small style="color: var(--color-text-secondary);">Modérer le contenu</small>
            </a>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
            <a href="admin/logs" class="btn btn-primary">
                📊 Voir les logs<br>
                <small style="color: var(--color-text-secondary);">Activité système</small>
            </a>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
            <a href="admin/stats" class="btn btn-info">
                📈 Statistiques<br>
                <small style="color: var(--color-text-secondary);">Analyses détaillées</small>
            </a>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
            <a href="admin/settings" class="btn btn-warning">
                ⚙️ Paramètres<br>
                <small style="color: var(--color-text-secondary);">Configuration</small>
            </a>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
            <a href="./" class="btn btn-secondary">
                🏠 Retour au site<br>
                <small style="color: var(--color-text-secondary);">Interface utilisateur</small>
            </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
