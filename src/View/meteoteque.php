<?php
require_once __DIR__ . '/../Model/ModelBD.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Météothèque</title>
    <style>
        /* Style général */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #f4f4f9, #e0e7ff);
            color: #333;
        }

        /* Conteneur principal */
        .container {
            margin: 30px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        /* Titre principal */
        h1 {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            color: #3b82f6;
            margin-bottom: 30px;
        }

        /* Bloc pour chaque météothèque */
        .block {
            margin-bottom: 25px;
            padding: 15px;
            background: #f9fafb;
            border-left: 6px solid #3b82f6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Titre de chaque météothèque */
        .block h5 {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        /* Tableau des données météorologiques */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #3b82f6;
            color: #ffffff;
        }

        table td {
            background-color: #f1f5f9;
        }

        /* Liens pour les stations */
        a {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        a:hover {
            text-decoration: underline;
            color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Météothèque</h1>
        <?php
        // Récupération des données de météothèque
        $meteo = ModelBD::meteo();

        foreach ($meteo as $row) {
            echo "<div class='block'>";
            echo "<h5>Nom météothèque : " . htmlspecialchars($row['nom']) . " | Définition : " . htmlspecialchars($row['definition']) . "</h5>";

            // Récupération des données de la station
            $donnéesStation = Modelbd::getdonnée($row['id']);
            if (!empty($donnéesStation)) {
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Station</th>";
                echo "<th>Date</th>";
                echo "<th>Température</th>";
                echo "<th>Temp. Min</th>";
                echo "<th>Temp. Max</th>";
                echo "<th>Humidité</th>";
                echo "<th>Vent (km/h)</th>";
                echo "<th>Direction Vent</th>";
                echo "<th>Précipitations (24h)</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                foreach ($donnéesStation as $row2) {
                    echo "<tr>";
                    echo "<td><a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=station&station_id=" . htmlspecialchars($row2['station']) . "'>" . htmlspecialchars($row2['station']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row2['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row2['temperature']) . "°C</td>";
                    echo "<td>" . htmlspecialchars($row2['temp_min']) . "°C</td>";
                    echo "<td>" . htmlspecialchars($row2['temp_max']) . "°C</td>";
                    echo "<td>" . htmlspecialchars($row2['humidité']) . "%</td>";
                    echo "<td>" . htmlspecialchars($row2['vitesse_vent']) . "</td>";
                    echo "<td>" . htmlspecialchars($row2['direction_vent']) . "</td>";
                    echo "<td>" . htmlspecialchars($row2['precipitation']) . " mm</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>Aucune donnée disponible pour cette météothèque.</p>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
