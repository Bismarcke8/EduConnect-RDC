<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 400px; margin-top: 80px;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Connexion</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="auth/login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Connexion</button>
            </form>

            <p style="margin-top: var(--spacing-lg); text-align: center; color: var(--color-text-secondary);">
                Pas encore inscrit? <a href="auth/register" style="color: var(--color-accent); text-decoration: none;">S'inscrire</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
