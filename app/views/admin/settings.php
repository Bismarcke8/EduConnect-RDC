<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xl py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <h2 class="mb-2 mb-md-0">⚙️ Paramètres Système</h2>
        <a href="admin/dashboard" class="btn btn-secondary">Retour au dashboard</a>
    </div>

    <!-- General Settings -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">Paramètres Généraux</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="admin/update-settings">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="site_name">Nom du site</label>
                    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="site_description">Description du site</label>
                    <textarea id="site_description" name="site_description" rows="3" required><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="allow_registration" value="1" <?php echo $settings['allow_registration'] ? 'checked' : ''; ?>>
                        Autoriser les nouvelles inscriptions
                    </label>
                </div>

                <div class="form-group">
                    <label for="max_upload_size">Taille maximale d'upload (MB)</label>
                    <input type="number" id="max_upload_size" name="max_upload_size" value="<?php echo $settings['max_upload_size'] / (1024 * 1024); ?>" min="1" max="50">
                </div>

                <div class="form-group">
                    <label for="items_per_page">Éléments par page</label>
                    <input type="number" id="items_per_page" name="items_per_page" value="<?php echo $settings['items_per_page']; ?>" min="5" max="100">
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer les paramètres</button>
            </form>
        </div>
    </div>

    <!-- System Information -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h3 class="card-title">Informations Système</h3>
        </div>
        <div class="card-body">
            <div class="grid-2" style="gap: var(--spacing-lg);">
                <div>
                    <h4>Version PHP</h4>
                    <p><?php echo PHP_VERSION; ?></p>
                </div>
                <div>
                    <h4>Système d'exploitation</h4>
                    <p><?php echo PHP_OS; ?></p>
                </div>
                <div>
                    <h4>Base de données</h4>
                    <p>MySQL <?php echo $this->db->getServerVersion(); ?></p>
                </div>
                <div>
                    <h4>Espace disque</h4>
                    <p><?php echo round(disk_free_space(ROOT_PATH) / (1024 * 1024 * 1024), 2); ?> GB libre</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Actions de Maintenance</h3>
        </div>
        <div class="card-body">
            <div class="grid-3" style="gap: var(--spacing-lg);">
                <div style="text-align: center;">
                    <button class="btn btn-warning" onclick="clearCache()">🧹 Vider le cache</button>
                    <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin-top: var(--spacing-sm);">Nettoie les fichiers temporaires</p>
                </div>
                <div style="text-align: center;">
                    <button class="btn btn-info" onclick="exportData()">📤 Exporter les données</button>
                    <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin-top: var(--spacing-sm);">Télécharge une sauvegarde</p>
                </div>
                <div style="text-align: center;">
                    <button class="btn btn-danger" onclick="confirmReset()">🔄 Réinitialiser</button>
                    <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin-top: var(--spacing-sm);">⚠️ Action dangereuse</p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider le cache ?')) {
        // Implement cache clearing logic
        alert('Cache vidé avec succès !');
    }
}

function exportData() {
    if (confirm('Télécharger une sauvegarde des données ?')) {
        // Implement data export logic
        window.location.href = 'admin/export-data';
    }
}

function confirmReset() {
    if (confirm('⚠️ ATTENTION : Cette action va supprimer TOUTES les données ! Êtes-vous absolument sûr ?')) {
        if (confirm('DERNIÈRE CHANCE : Tapez "RESET" pour confirmer')) {
            // Implement reset logic
            alert('Fonctionnalité non implémentée pour des raisons de sécurité');
        }
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>