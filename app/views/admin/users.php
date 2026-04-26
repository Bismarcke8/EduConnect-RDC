<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <h2>👥 Gestion des utilisateurs</h2>

    <div class="card">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border);">
                    <th style="padding: var(--spacing-md); text-align: left;">Nom</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Email</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Université</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Statut</th>
                    <th style="padding: var(--spacing-md); text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td style="padding: var(--spacing-md);">
                            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <?php echo htmlspecialchars($user['university'] ?? '-'); ?>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <span class="badge" style="background-color: <?php echo $user['is_active'] ? 'var(--color-success)' : 'var(--color-danger)'; ?>; color: white;">
                                <?php echo $user['is_active'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td style="padding: var(--spacing-md); text-align: right;">
                            <?php if (!$user['is_active']): ?>
                                <span style="color: var(--color-text-secondary);">Banni</span>
                            <?php else: ?>
                                <form method="POST" action="/admin/user/<?php echo $user['id']; ?>/ban" style="display: inline;">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr?')">
                                        Bannir
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="/admin/users?page=<?php echo $i; ?>" 
               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <a href="/admin/dashboard" class="btn btn-secondary" style="margin-top: var(--spacing-lg);">← Retour au dashboard</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
