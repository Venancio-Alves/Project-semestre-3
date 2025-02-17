<?php
require_once  __DIR__."/../Model/ModelBD.php";
$ville = ModelBD::Ville();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 50px;
            padding-left: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<form  method="GET">
    <h2>Modification</h2>
    <input type="hidden" name="action" value="adminupdated">
    <label for="Utilisateur">id:</label>
    <input type="text" id="Utilisateur" name="id" placeholder="Entrez votre id" value='<?=$utils['id_utilisateur']?>' required
    readonly>
    <label for="Utilisateur">Nom:</label>
    <input type="text" id="Utilisateur" name="nom" placeholder="Entrez votre nom" value='<?=$utils['nom_utilisateur']?>' required>
    <label for="Utilisateur">Prenom:</label>
    <input type="text" id="Utilisateur" name="prenom" placeholder="Entrez votre prenom" value='<?=$utils['prenom_utilisateur']?>'required> 

    <label for="Utilisateur">Ville :</label>
    <input type="text" id="Utilisateur" name="ville" placeholder="Entrez votre ville" value='<?=$utils['nom_ville'] ?>' list="browsers">
    <label for="Utilisateur">role:</label>
    <input type="text" id="Utilisateur" name="role" placeholder="Entrez votre role" value='<?=$utils['role'] ?>'list="role" required>
    <input type="submit"  value="valider">
</form>
<datalist id="browsers">
    <?php

    foreach ($ville as $row) {
        $row = htmlspecialchars($row, ENT_QUOTES, 'UTF-8');
        echo "<option value='$row'>";    
    }
   ?>
</datalist>
<datalist id="role">
    <option value="admin"></option>
    <option value="utilisateur"></option>
    <option value="attente"></option>
</datalist>
</datalist>
</body>
</html>
