<?php
// Charger le modèle
require_once __DIR__ . '/../Model/ModelBD.php';


// Récupérer les stations pour la liste de sélection
$stations = ModelBD::Ville();

// Récupérer les données de la station sélectionnée
$stationId = $_GET['station_id'] ?? '';
$dataStation = [];
if ($stationId) {
    // Détails de la station
    $stationData = ModelBD::villedetails($stationId);
    if ($stationId) {
        // Données climatiques associées
        $dataStation = ModelBD::getvillestat($stationId);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Données de Station</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Pour les graphiques -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        .select-container {
            margin: 20px 0;
            text-align: center;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<h1>Station météorologique</h1>

<!-- Liste de sélection des stations -->

<div class="select-container">
    <form method="GET" action="frontcontroller.php">
        <input type="hidden" name="controller" value="controller">
        <input type="hidden" name="action" value="station">
        <label for="station_id">Choisissez une station :</label>
        <select name="station_id" id="station_id" onchange="this.form.submit()">
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($stations as $station): ?>
                <option value="<?= $station ?>">
                    <?= htmlspecialchars($station) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($stationId && $stationData): ?>
    <!-- Afficher les détails de la station -->
    <h2>Détails de la station</h2>
    <table>
        <tr>
            <th>Nom</th>
            <td><?= htmlspecialchars($stationData['nom_ville']) ?></td>
        </tr>
        <tr>
            <th>Latitude</th>
            <td><?= htmlspecialchars($stationData['latitude']) ?></td>
        </tr>
        <tr>
            <th>Longitude</th>
            <td><?= htmlspecialchars($stationData['longitude']) ?></td>
        </tr>
        <tr>
            <th>Code Département</th>
            <td><?= htmlspecialchars($stationData['code_departement']) ?></td>
        </tr>
    </table>

    <!-- Graphiques et tableaux -->
    <div class="chart-container">
        <canvas id="temperatureChart"></canvas>
    </div>
    <div class="chart-container">
        <canvas id="precipitationChart"></canvas>
    </div>

    <script>
        // Données des graphiques issues de data_station
        const dataStation = <?= json_encode($dataStation) ?>;
        const dates = dataStation.map(d => d.date);
        const temperatures = dataStation.map(d => parseFloat(d.tc));
        const precipitations = dataStation.map(d => parseFloat(d.rr24));

        // Graphique des températures
        const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
        new Chart(temperatureCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Température (°C)',
                    data: temperatures,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des températures'
                    }
                }
            }
        });

        // Graphique des précipitations
        const precipitationCtx = document.getElementById('precipitationChart').getContext('2d');
        new Chart(precipitationCtx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Précipitations (mm)',
                    data: precipitations,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Cumul des précipitations'
                    }
                }
            }
        });
    </script>
<?php endif; ?>

</body>
</html>
