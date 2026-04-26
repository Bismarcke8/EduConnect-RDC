<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 700px;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">✍️ Créer une publication</h2>
        </div>

        <div class="card-body">
            <form method="POST" action="/post/store" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre (optionnel)</label>
                    <input type="text" id="title" name="title" placeholder="Titre de votre publication">
                </div>

                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea id="content" name="content" placeholder="Écrivez votre publication..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image (optionnel)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small style="color: var(--color-text-secondary);">Taille maximale: 5MB. Formats: JPG, PNG, GIF, WEBP</small>
                </div>

                <div style="display: flex; gap: var(--spacing-md);">
                    <button type="submit" class="btn btn-primary">Publier</button>
                    <a href="feed" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
