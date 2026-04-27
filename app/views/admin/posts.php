<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <h2>📝 Gestion des publications</h2>

    <div class="card">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border);">
                    <th style="padding: var(--spacing-md); text-align: left;">Titre</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Auteur</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Date</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Statut</th>
                    <th style="padding: var(--spacing-md); text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td style="padding: var(--spacing-md);">
                            <strong><?php echo htmlspecialchars(substr($post['title'] ?? 'Sans titre', 0, 50)); ?></strong>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <span class="badge" style="background-color: <?php echo $post['is_published'] ? 'var(--color-success)' : 'var(--color-warning)'; ?>; color: white;">
                                <?php echo $post['is_published'] ? 'Publiée' : 'Brouillon'; ?>
                            </span>
                        </td>
                        <td style="padding: var(--spacing-md); text-align: right;">
                            <form method="POST" action="admin/post/<?php echo $post['id']; ?>/delete" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="admin/posts?page=<?php echo $i; ?>" 
               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <a href="admin/dashboard" class="btn btn-secondary" style="margin-top: var(--spacing-lg);">← Retour au dashboard</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
