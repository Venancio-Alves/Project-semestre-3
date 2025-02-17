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
                <input type="hidden" name="action" value="meteocreate">

                <div class="mb-3">
                    <label for="id" class="form-label">nom meteoteque :</label>
                    <input type="text" id="id" name="nom" class="form-control" placeholder="Entrez un identifiant complexe" required>
                </div>
                <div class="mb-3">
                    <label for="ville" class="form-label">definition météotéque:</label>
                    <input type="text" id="ville" name="definition" class="form-control" placeholder="Entrez votre ville" >
                </div>

                <button type="submit" name="ok" value="valider" class="btn btn-primary w-100">Valider</button>
            </form>
            <?php if ($message != null): ?>
                <div class="alert alert-info mt-3">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<datalist id="browsers">
    <?php
    foreach ($ville as $row) {
        echo "<option value='$row'>";    
    }
   ?>
</datalist>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
