<?php
require_once  __DIR__."/../Model/ModelBD.php";

$ville = ModelBD::ville(); 
$region = ModelBD::region(); 

if (!ModelBD::exist($_SESSION['user']['id']??null) ) {
    session_destroy();
    header('loccalhost/sae/web/frontcontroller.php');

    

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Météo Information</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
    /* Fond dégradé pour l'arrière-plan de la page */
    body {
        background: linear-gradient(135deg, #6dd5fa, #2980b9);
        font-family: 'Arial', sans-serif;
        color: #fff;
        animation: fadeIn 1.5s ease-in-out;
    }

    /* Animation pour l'apparition de la page */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Style du container */
    .container {
        max-width: 700px;
        margin: 0 auto;
        padding: 30px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease-in-out;
    }

    /* Effet de zoom sur le container au survol */
    .container:hover {
        transform: scale(1.03);
    }

    /* Style du titre */
    h1 {
        font-size: 2.5rem;
        margin-bottom: 30px;
        text-align: center;
        color: rgb(0, 76, 127);
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* Formulaire - champs de texte */
    .form-control {
        border-radius: 10px;
        transition: border-color 0.3s;
    }

    /* Effet de focus sur les champs de formulaire */
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.8);
    }

    /* Style du fieldset */
    fieldset {
        border: none;
        color: rgb(0, 76, 127);
    }

    /* Style de la légende */
    legend {
        font-size: 1.5rem;
        margin-bottom: 20px;
        text-transform: uppercase;
        font-weight: bold;
    }

    /* Style des labels */
    label {
        font-size: 1.1rem;
        display: inline-block;
        margin-bottom: 10px;
    }

    /* Style des cases à cocher */
    .form-check-label {
        font-size: 1rem;
    }

    /* Espacement entre la case à cocher et le texte */
    .form-check-input {
        margin-left: 10px;
        transition: transform 0.3s;
    }

    /* Effet de zoom sur les cases à cocher */
    .form-check-input:hover {
        transform: scale(1.1);
    }

    /* Style du bouton */
    .btn-primary {
        background-color: #2980b9;
        border: none;
        border-radius: 10px;
        transition: background-color 0.3s, transform 0.3s;
        font-size: 1.2rem;
    }

    /* Effet au survol du bouton */
    .btn-primary:hover {
        background-color: #3498db;
        transform: translateY(-2px);
    }

    /* Animation du bouton au focus */
    .btn-primary:focus {
        outline: none;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.8);
    }

    /* Style de l'input de type date */
    input[type="date"] {
        background-color: #f0f8ff;
        border-radius: 5px;
        transition: background-color 0.3s ease-in-out;
    }

    /* Effet au focus de l'input date */
    input[type="date"]:focus {
        background-color: #eaf3fc;
    }

    /* Style pour les filtres (checkbox) */
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
    }

    .checkbox-group .form-check {
        width: 48%;
        margin-bottom: 10px;
    }

    /* Style pour les datalists */
    datalist {
        margin-top: 10px;
    }

    /* Animation des éléments du formulaire */
    .form-check-label,
    .form-control,
    .btn-primary {
        animation: fadeIn 1s ease-out;
    }

    .form-check-label {
        animation-delay: 0.3s;
    }

    .form-control {
        animation-delay: 0.5s;
    }

    .btn-primary {
        animation-delay: 0.7s;
    }
    </style>

    <script>
    // Fonction qui bloque ou débloque les champs en fonction de l'entrée dans le champ 1
    function toggleFields() {
        var champ1 = document.getElementById("villes").value;
        var champ2 = document.getElementById("regions").value;
        var champ3 = document.getElementById("inter");

        if (champ1.trim() !== "") {
            document.getElementById("regions").disabled = true;
            champ3.disabled = false;
        } else if (champ2.trim() !== "") {
            document.getElementById("villes").disabled = true;
            champ3.disabled = true;
        } else {
            document.getElementById("villes").disabled = false;
            document.getElementById("regions").disabled = false;
            champ3.disabled = false;
        }
    }
    </script>
</head>


<body>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Météo Information</h1>
        <form method="get" class="bg-light p-4 rounded shadow">
            <fieldset>
                <legend class="mb-3">Recherche météo :</legend>

                <input type="hidden" name="action" value="rech">

                <div class="mb-3">
                    <label for="ville" class="form-label">
                        <i class="fas fa-city"></i> Ville :
                    </label>
                    <input list="browsers" id="villes" name="villes" class="form-control" oninput="toggleFields()">
                </div>
                <div class="mb-3">
                    <label for="ville" class="form-label"><i class="fas fa-filter"></i> Filtre :</label><br>
                    <label for="temperature" class="form-label">
                        <i class="fas fa-thermometer-half"></i> Température :
                    </label>
                    <input type="checkbox" class="form-check-input" id="temperature" name="tc" value="1">

                    <label for="vent" class="form-label">
                        <i class="fas fa-wind"></i> Direction du vent :
                    </label>
                    <input type="checkbox" class="form-check-input" id="vent" name="dd" value="1">

                    <label for="vitesseVent" class="form-label">
                        <i class="fas fa-tachometer-alt"></i> Vitesse du vent :
                    </label>
                    <input type="checkbox" class="form-check-input" id="vitesseVent" name="ff" value="1">

                    <label for="humidite" class="form-label">
                        <i class="fas fa-water"></i> Humidité :
                    </label>
                    <input type="checkbox" class="form-check-input" id="humidite" name="u" value="1">

                    <label for="precipitation" class="form-label">
                        <i class="fas fa-cloud-rain"></i> Précipitation sur 24 heures :
                    </label>
                    <input type="checkbox" class="form-check-input" id="precipitation" name="rr24" value="1">

                    <label for="phenomenes" class="form-label">
                        <i class="fas fa-exclamation-triangle"></i> Phénomènes spéciaux :
                    </label>
                    <input type="checkbox" class="form-check-input" id="phenomenes" name="phenspe1" value="1">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Date :
                        </label>
                        <input type="date" id="date" name="date" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="inter" class="form-label">
                            <i class="fas fa-calendar-check"></i> Jusqu'à :
                        </label>
                        <input type="date" id="inter" name="inter" class="form-control" oninput="toggleFields()">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="regions" class="form-label">
                        <i class="fas fa-globe-americas"></i> Régions :
                    </label>
                    <input type="text" id="regions" name="regions" class="form-control"
                        placeholder="Entrez le nom de la région" list="browser" oninput="toggleFields()">
                </div>

                <button type="submit" name="ok" class="btn btn-primary">
                    <i class="fas fa-check"></i> Valider
                </button>
            </fieldset>
        </form>
    </div>

    <datalist id="browsers">
        <?php
    foreach ($ville as $row) {
        $row = htmlspecialchars($row, ENT_QUOTES, 'UTF-8');
        echo "<option value='$row'>";    
    }
   ?>
    </datalist>
    <datalist id="browser">
        <?php
    foreach ($region as $row) {
        $row = htmlspecialchars($row, ENT_QUOTES, 'UTF-8');
        echo "<option value='$row'>";    
    }
   ?>
    </datalist>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>