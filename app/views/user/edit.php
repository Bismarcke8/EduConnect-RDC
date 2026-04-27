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

            <h3 id="photo-section">Photo de profil</h3>
            
            <!-- Current photo preview -->
            <div class="current-photo-preview" style="text-align: center; margin-bottom: var(--spacing-lg);">
                <img src="<?php echo $user['profile_photo'] ? APP_BASE_PATH . '/' . $user['profile_photo'] : 'https://via.placeholder.com/120x120/4cc2ff/ffffff?text=' . urlencode(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>" 
 
                     alt="Photo actuelle" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-border);">
                <p style="margin-top: var(--spacing-sm); color: var(--color-text-secondary); font-size: 0.9rem;">Photo actuelle</p>
            </div>
            
            <form id="photo-form" enctype="multipart/form-data" style="border: 2px dashed var(--color-border); border-radius: var(--radius-md); padding: var(--spacing-lg); text-align: center; background: var(--color-surface-alt);">
                <input type="hidden" id="photo-csrf-token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                
                <div style="margin-bottom: var(--spacing-md);">
                    <label for="photo-input" style="cursor: pointer; display: inline-block;">
                        <div style="padding: var(--spacing-lg); border: 2px dashed var(--color-accent); border-radius: var(--radius-md); background: rgba(76, 194, 255, 0.1); transition: all 0.3s ease;">
                            📸 <strong>Cliquez pour sélectionner une nouvelle photo</strong>
                            <br><small style="color: var(--color-text-secondary);">Formats acceptés: JPG, PNG, GIF (max 5MB)</small>
                        </div>
                    </label>
                    <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;" required>
                </div>
                
                <div id="file-info" style="margin-bottom: var(--spacing-md); display: none;">
                    <small style="color: var(--color-accent);">Fichier sélectionné: <span id="file-name"></span></small>
                </div>
                
                <button type="submit" class="btn btn-primary" id="upload-btn" disabled>
                    🚀 Mettre à jour la photo
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Handle file selection preview
document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const uploadBtn = document.getElementById('upload-btn');
    
    if (file) {
        fileName.textContent = file.name;
        fileInfo.style.display = 'block';
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '🚀 Mettre à jour la photo';
    } else {
        fileInfo.style.display = 'none';
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = 'Sélectionnez d\'abord une photo';
    }
});

// Handle photo upload
document.getElementById('photo-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('photo-input');
    const uploadBtn = document.getElementById('upload-btn');
    
    if (!fileInput.files[0]) {
        alert('Veuillez sélectionner une photo');
        return;
    }
    
    // Show loading state
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '⏳ Téléchargement...';
    
    const formData = new FormData();
    formData.append('photo', fileInput.files[0]);
    formData.append('csrf_token', document.getElementById('photo-csrf-token').value || '');
    
    const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '';
    
    fetch(basePath + '/user/upload-photo', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Success
            uploadBtn.innerHTML = '✅ Photo mise à jour !';
            uploadBtn.className = 'btn btn-success';
            
            // Reload page after short delay to show new photo
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            // Error
            alert('Erreur: ' + data.error);
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '🚀 Mettre à jour la photo';
            uploadBtn.className = 'btn btn-primary';
        }
    })
    .catch(error => {
        alert('Erreur de connexion. Veuillez réessayer.');
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '🚀 Mettre à jour la photo';
        uploadBtn.className = 'btn btn-primary';
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
