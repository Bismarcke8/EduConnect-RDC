<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 600px;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Paramètres du compte</h2>
        </div>

        <div class="card-body">
            <h3>Modifier votre profil</h3>
            <form method="POST" action="user/update-profile">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="university">Université</label>
                    <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($user['university'] ?? ''); ?>" placeholder="Ex: Université de Kinshasa">
                </div>

                <div class="form-group">
                    <label for="field_of_study">Filière</label>
                    <input type="text" id="field_of_study" name="field_of_study" value="<?php echo htmlspecialchars($user['field_of_study'] ?? ''); ?>" placeholder="Ex: Informatique">
                </div>

                <div class="form-group">
                    <label for="bio">Biographie</label>
                    <textarea id="bio" name="bio" placeholder="Parlez-nous de vous..." style="min-height: 150px;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>

            <hr style="margin: var(--spacing-xl) 0; border: none; border-top: 1px solid var(--color-border);">

            <h3>Changer le mot de passe</h3>
            <form method="POST" action="user/change-password">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-group">
                    <label for="old_password">Mot de passe actuel</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
            </form>

            <hr style="margin: var(--spacing-xl) 0; border: none; border-top: 1px solid var(--color-border);">

            <h3>Photo de profil</h3>
            <form id="photo-form" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" id="photo-input" name="photo" accept="image/*" required>
                    <small style="color: var(--color-text-secondary);">Taille maximale: 5MB</small>
                </div>
                <button type="submit" class="btn btn-primary">Télécharger</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('photo-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('photo', document.getElementById('photo-input').files[0]);
    
    fetch('/user/upload-photo', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Photo téléchargée avec succès');
            location.reload();
        } else {
            alert('Erreur: ' + data.error);
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
