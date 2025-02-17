<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <style>
        /* Style général */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #f9f9fb, #e0e7ff);
            color: #333;
        }

        /* Conteneur principal */
        .profile-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2563eb;
            text-align: center;
            margin-bottom: 10px;
        }

        .profile-info {
            font-size: 1rem;
            text-align: center;
            margin: 5px 0;
            color: #374151;
        }

        /* Liens de modification */
        .profile-links {
            text-align: center;
            margin-top: 20px;
        }

        .profile-links a {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
            margin: 0 10px;
        }

        .profile-links a:hover {
            text-decoration: underline;
            color: #1e40af;
        }

        /* Bloc Météotèque */
        .meteo-section {
            margin: 30px auto;
            max-width: 800px;
        }

        .meteo-section h2 {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
        }

        .meteo-item {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fafb;
            border-left: 6px solid #3b82f6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .meteo-item h5 {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .meteo-item p {
            font-size: 0.95rem;
            margin: 5px 0;
            color: #374151;
        }

        /* Liens */
        a {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
            color: #1e40af;
        }
    </style>
</head>
<body>

    <!-- Conteneur du profil utilisateur -->
    <div class="profile-container">
        <?php
        require_once __DIR__ . '/../Model/ModelBD.php';
        echo "<p class='profile-name'>" . htmlspecialchars($_SESSION['user']['nom']) . "</p>";
        echo "<p class='profile-info'>" . htmlspecialchars($_SESSION['user']['prenom']) . "</p>";
        echo "<p class='profile-info'>" . htmlspecialchars(ModelBD::getVille($_SESSION['user']['id'])) . "</p>";
        ?>
        <div class="profile-links">
            <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=update'>Modifier le profil</a>
        </div>
    </div>

    <!-- Section des météothèques -->
    <div class="meteo-section">
        <?php
        if (ModelBD::getmeteo($_SESSION['user']['id']) != null) {
            echo "<h2>Vos Météothèques</h2>";
            foreach (ModelBD::getmeteo($_SESSION['user']['id']) as $row) {
                echo "<div class='meteo-item'>";
                echo "<h5>Nom : " . htmlspecialchars($row['nom']) . "</h5>";
                echo "<p>Définition : " . htmlspecialchars($row['definition']) . "</p>";
                echo "<p>
                    <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=deletemeteo&id_meteo=" . htmlspecialchars($row['id']) . "'>Supprimer</a> | 
                    <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=ajoutmeteo&id_meteo=" . htmlspecialchars($row['id']) . "'>Ajouter</a>
                </p>";

                // Affichage des données des stations
                foreach (Modelbd::getdonnée($row['id']) as $row2) {
                    echo "<p>
                        <strong>Station :</strong> 
                        <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=station&station_id=" . htmlspecialchars($row2['station']) . "'>" . htmlspecialchars($row2['station']) . "</a> | 
                        <strong>Date :</strong> " . htmlspecialchars($row2['date']) . " | 
                        <strong>Température :</strong> " . htmlspecialchars($row2['temperature']) . "°C | 
                        <strong>Min :</strong> " . htmlspecialchars($row2['temp_min']) . "°C | 
                        <strong>Max :</strong> " . htmlspecialchars($row2['temp_max']) . "°C | 
                        <strong>Humidité :</strong> " . htmlspecialchars($row2['humidité']) . "% | 
                        <strong>Vent :</strong> " . htmlspecialchars($row2['vitesse_vent']) . " km/h | 
                        <strong>Direction :</strong> " . htmlspecialchars($row2['direction_vent']) . " | 
                        <strong>Précipitations :</strong> " . htmlspecialchars($row2['precipitation']) . " mm 
                        <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=deletedonne&id_donne=" . htmlspecialchars($row2['id']) . "'>Supprimer</a>
                    </p>";
                }
                echo "</div>";
            }
        }
        ?>
        <div class="profile-links">
            <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=creermeteo'>Créer une nouvelle météothèque</a>
        </div>
    </div>

</body>
</html>
