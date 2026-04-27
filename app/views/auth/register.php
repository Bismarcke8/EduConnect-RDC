<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 450px; margin-top: 40px;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Créer un compte</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="auth/register">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Jean" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Dupont" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="jean@example.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
            </form>

            <p style="margin-top: var(--spacing-lg); text-align: center; color: var(--color-text-secondary);">
                Déjà inscrit? <a href="auth/login" style="color: var(--color-accent); text-decoration: none;">Connexion</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
