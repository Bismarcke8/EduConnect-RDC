<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <h2>📊 Statistiques Détaillées</h2>

    <!-- User Statistics -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">👥 Statistiques Utilisateurs</h3>
        </div>
        <div class="card-body">
            <div class="grid-4" style="gap: var(--spacing-lg);">
                <div class="stat-card">
                    <h4><?php echo $user_stats['total_users']; ?></h4>
                    <p>Total utilisateurs</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $user_stats['active_users']; ?></h4>
                    <p>Utilisateurs actifs</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $user_stats['admin_users']; ?></h4>
                    <p>Administrateurs</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $user_stats['student_users']; ?></h4>
                    <p>Étudiants</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Post Statistics -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">📝 Statistiques Publications</h3>
        </div>
        <div class="card-body">
            <div class="grid-4" style="gap: var(--spacing-lg);">
                <div class="stat-card">
                    <h4><?php echo $post_stats['total_posts']; ?></h4>
                    <p>Total publications</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $post_stats['published_posts']; ?></h4>
                    <p>Publications publiées</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $post_stats['official_posts']; ?></h4>
                    <p>Publications officielles</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $activity_stats['new_posts_30d']; ?></h4>
                    <p>Nouveaux posts (30j)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">📈 Activité Récente (30 derniers jours)</h3>
        </div>
        <div class="card-body">
            <div class="grid-3" style="gap: var(--spacing-lg);">
                <div class="stat-card">
                    <h4><?php echo $activity_stats['new_users_30d']; ?></h4>
                    <p>Nouveaux utilisateurs</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $activity_stats['new_posts_30d']; ?></h4>
                    <p>Nouvelles publications</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo $activity_stats['new_messages_30d']; ?></h4>
                    <p>Nouveaux messages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Universities -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">🎓 Top Universités</h3>
        </div>
        <div class="card-body">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--color-border);">
                        <th style="padding: var(--spacing-md); text-align: left;">Université</th>
                        <th style="padding: var(--spacing-md); text-align: right;">Nombre d'étudiants</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_universities as $uni): ?>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <td style="padding: var(--spacing-md);"><?php echo htmlspecialchars($uni['university']); ?></td>
                            <td style="padding: var(--spacing-md); text-align: right; font-weight: bold;"><?php echo $uni['count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: var(--spacing-lg); text-align: center;">
        <a href="admin/dashboard" class="btn btn-secondary">← Retour au dashboard</a>
    </div>
</div>

<style>
.stat-card {
    text-align: center;
    padding: var(--spacing-lg);
    background-color: var(--color-surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--color-border);
}

.stat-card h4 {
    font-size: 2rem;
    color: var(--color-accent);
    margin: 0 0 var(--spacing-sm) 0;
}

.stat-card p {
    color: var(--color-text-secondary);
    margin: 0;
    font-size: 0.9rem;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>