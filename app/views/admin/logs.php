<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl">
    <h2>📋 Historique des actions administrateur</h2>

    <div class="card">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--color-border);">
                    <th style="padding: var(--spacing-md); text-align: left;">Admin</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Action</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Description</th>
                    <th style="padding: var(--spacing-md); text-align: left;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td style="padding: var(--spacing-md);">
                            <?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <span class="badge badge-primary"><?php echo htmlspecialchars($log['action']); ?></span>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <?php echo htmlspecialchars($log['description']); ?>
                        </td>
                        <td style="padding: var(--spacing-md);">
                            <?php echo date('d M Y à H:i', strtotime($log['created_at'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="admin/logs?page=<?php echo $i; ?>" 
               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <a href="admin/dashboard" class="btn btn-secondary" style="margin-top: var(--spacing-lg);">← Retour au dashboard</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
