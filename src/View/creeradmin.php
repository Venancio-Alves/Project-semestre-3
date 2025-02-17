<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Créer un compte</h2>
            <form method="GET">
                <input type="hidden" name="action" value="comptecreeradmin">

                <!-- ID -->
                <div class="mb-3">
                    <label for="id" class="form-label">ID :</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" id="id" name="id" class="form-control" placeholder="Entrez un identifiant complexe" required>
                    </div>
                </div>

                <!-- Nom -->
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom :</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" id="nom" name="nom" class="form-control" placeholder="Entrez votre nom" required>
                    </div>
                </div>

                <!-- Prénom -->
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Entrez votre prénom" required>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="pwd" class="form-control" placeholder="Entrez un mot de passe" required>
                    </div>
                </div>

                <!-- Ville -->
                <div class="mb-3">
                    <label for="ville" class="form-label">Ville :</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input id="ville" name="ville" class="form-control" placeholder="Entrez votre ville" list="browsers">
                    </div>
                </div>

                <!-- Bouton de validation -->
                <button type="submit" name="ok" value="valider" class="btn btn-primary w-100">Valider</button>
            </form>

            <!-- Message -->
            <?php if ($message != null): ?>
                <div class="alert alert-info mt-3 text-center">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Liste des villes -->
<datalist id="browsers">
    <?php
    foreach ($ville as $row) {
        echo "<option value='" . htmlspecialchars($row) . "'>";
    }
    ?>
</datalist>

<!-- Bootstrap JS + Icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
<!-- #region -->