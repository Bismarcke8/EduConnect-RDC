<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div style="text-align: center; padding: 60px 0;">
        <h1 style="font-size: 2.5rem; margin-bottom: 20px;">🎓 Bienvenue sur EduConnect-RDC</h1>
        <p style="font-size: 1.1rem; color: var(--color-text-secondary); margin-bottom: 40px;">
            La plateforme collaborative pour les étudiants de la RDC
        </p>

        <div class="grid-2" style="max-width: 800px; margin: 0 auto;">
            <div class="card">
                <h3>📚 Connectez-vous</h3>
                <p>Rejoignez des milliers d'étudiants et construisez votre réseau professionnel</p>
                <a href="auth/login" class="btn btn-primary">Se connecter</a>
            </div>

            <div class="card">
                <h3>✍️ Partagez</h3>
                <p>Publiez vos compétences, projets et contenus académiques</p>
                <a href="auth/register" class="btn btn-primary">Créer un compte</a>
            </div>
        </div>

        <div style="margin-top: 80px;">
            <h2>Fonctionnalités Principales</h2>
            <div class="grid-3">
                <div class="card">
                    <h4>👥 Réseau Social</h4>
                    <p>Connectez-vous avec d'autres étudiants et professionnels</p>
                </div>
                <div class="card">
                    <h4>📝 Publications</h4>
                    <p>Partagez vos expériences et connaissances académiques</p>
                </div>
                <div class="card">
                    <h4>💬 Messagerie</h4>
                    <p>Communiquez directement avec les autres membres</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
