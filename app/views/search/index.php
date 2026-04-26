<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 700px;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">🔍 Rechercher des étudiants</h2>
        </div>

        <div class="card-body">
            <form method="POST" action="/search">
                <div class="form-group">
                    <label for="query">Nom, Email ou Université</label>
                    <input type="text" id="query" name="query" placeholder="Cherchez un étudiant...">
                </div>

                <div class="form-group">
                    <label for="university">Filtrer par université (optionnel)</label>
                    <input type="text" id="university" name="university" placeholder="Ex: Université de Kinshasa">
                </div>

                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
