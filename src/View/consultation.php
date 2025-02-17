<?php
// Charger le modèle
require_once __DIR__ . '/../Model/ModelBD.php';

// Récupérer les filtres
$spatialType = isset($_GET['spatial_type']) ? $_GET['spatial_type'] : 'ville';
$spatialValue = isset($_GET['spatial_value']) ? $_GET['spatial_value'] : '';
$granularity = isset($_GET['granularity']) ? $_GET['granularity'] : 'jour';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Récupérer les données filtrées
//$dataStation = ModelBD::getFilteredDataBySpatial($spatialType, $spatialValue, $granularity, $startDate, $endDate);
$dataStation = ModelBD::getAveragedData($spatialType, $spatialValue, $granularity, $startDate, $endDate);
// Fetch spatial granularity from the user selection
$spatialType = isset($_GET['spatial_type']) ? $_GET['spatial_type'] : 'ville';

// Grouper les données en fonction des identifiants (concaténation de colonnes 1 et 2)
$groupedData = [];
foreach ($dataStation as $row) {
    $identifier = $row['annee'] . '_' . $row['periode']; // Identifiant unique
    $groupedData[$identifier][] = $row; // Ajouter chaque ligne ayant le même identifiant
}

// Découper les données en groupes pour la pagination
$dataGroups = array_chunk($groupedData, 1, true); // Un groupe par identifiant

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation des Données Climatiques</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Graphiques -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
	
    <style>
    /* Fond dégradé pour l'arrière-plan de la page */
    body {
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
    .infoBo{
        color: black;
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
	table {
    width: 100%;
    border-collapse: collapse;
    background-color: white; /* Fond blanc */
    color: black; /* Texte noir */
    margin: 20px auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optionnel : légère ombre pour un effet moderne */
}

th, td {
    border: 2px solid blue; /* Encadrements en bleu */
    padding: 10px;
    text-align: center; /* Centrer le texte */
}

th {
    background-color: #dceeff; /* Fond bleu clair pour l'en-tête */
    font-weight: bold;
    text-transform: uppercase; /* Majuscules pour l'en-tête */
}

tr:nth-child(even) {
    background-color: #f9f9f9; /* Fond gris clair pour les lignes paires */
}

tr:hover {
    background-color: #e6f7ff; /* Survol : bleu très clair */
}
    </style>
    <style>
		html, body {
            font-family: Arial, sans-serif;
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
        h1 {
            text-align: center;
        }
        .filter-container {
            margin: 20px 0;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .filter-group {
            margin-bottom: 15px;
        }
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f9f9f9;
        }
        #map {
            width: 100%;
            height: 650px;
            margin: 10px auto;
            border: 1px solid #ccc;
        }
		path {
		  stroke: #000;
		  stroke-width: 1px;
		  stroke-linecap: round;
		  stroke-linejoin: round;
		  stroke-opacity: 0.25;
		  fill: blue;
		}

		/* Style d'hover pour les chemins */
		g:hover path {
		  fill: #033f99;
		}
		g path:hover {
		  fill: #77eddd;
		}
    </style>
	
</head>
<body>

<h1>Consultation des Données Climatiques</h1>

<!-- Formulaire de filtres -->
<div class="filter-container">
    <style>
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .filter-row > div {
            flex: 1;
            display: flex;
            align-items: center;
            margin-right: 10px;
        }
        .filter-row > div:last-child {
            margin-right: 0;
        }
        .filter-row label {
            width: 150px; /* Largeur fixe pour aligner les labels */
            margin-right: 10px;
        }
        .filter-row input, .filter-row select {
            flex: 1;
        }
        .checkbox-group {
            margin-bottom: 15px;
        }
        .checkbox-group label {
            margin-right: 15px;
        }
    </style>
    <form method="GET" action="">
        <input type="hidden" name="controller" value="controller">
        <input type="hidden" name="action" value="consultation">

        <!-- Granularité spatiale et Ville -->
        <div class="filter-row">
            <div>
                <label for="spatial_type">Granularité spatiale :</label>
                <select name="spatial_type" id="spatial_type" onchange="updateLabel()">
                    <option value="ville" <?= $spatialType == 'ville' ? 'selected' : '' ?>>Ville</option>
                    <option value="departement" <?= $spatialType == 'departement' ? 'selected' : '' ?>>Département</option>
                    <option value="region" <?= $spatialType == 'region' ? 'selected' : '' ?>>Région</option>
                    <option value="metropole" <?= $spatialType == 'metropole' ? 'selected' : '' ?>>Métropole</option>
                </select>
            </div>
            <div>
                <label for="spatial_value" id="spatial_label"><?= ucfirst($spatialType) ?> :</label>
                <input type="text" name="spatial_value" id="spatial_value" value="<?= htmlspecialchars($spatialValue) ?>">
            </div>
        </div>

        <!-- Granularité temporelle, Date de début, et Date de fin -->
        <div class="filter-row">
            <div>
                <label for="granularity">Granularité temporelle :</label>
                <select name="granularity" id="granularity">
                    <option value="jour" <?= $granularity == 'jour' ? 'selected' : '' ?>>Jour</option>
                    <option value="semaine" <?= $granularity == 'semaine' ? 'selected' : '' ?>>Semaine</option>
                    <option value="mois" <?= $granularity == 'mois' ? 'selected' : '' ?>>Mois</option>
                    <option value="annee" <?= $granularity == 'annee' ? 'selected' : '' ?>>Année</option>
                </select>
            </div>
            <div>
                <label for="start_date">Date de début :</label>
                <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($startDate) ?>">
            </div>
            <div>
                <label for="end_date">Date de fin :</label>
                <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($endDate) ?>">
            </div>
        </div>


        <!-- Bouton de soumission -->
        <button class="btn btn-primary" type="submit">Appliquer les filtres</button>
    </form>

</div>
<hr>
      

<!-- Carte interactive -->
<div id="map"></div>

<!-- Titre sous la carte -->
<h2 id="mapTitle">Carte des températures moyennes °C</h2>
<div id="infoBo" >Année : / Période :</div>

  <!-- Sélection des données climatiques -->
   <div class="checkbox-group">
		<label>
			<input type="radio" name="data_type" value="temperature" checked onchange="updateMap()"> Température
		</label>
		<label>
			<input type="radio" name="data_type" value="humidity" onchange="updateMap()"> Humidité
		</label>
		<label>
			<input type="radio" name="data_type" value="precipitation" onchange="updateMap()"> Précipitations
		</label>
	</div>
<!-- Conteneur pour la navigation et l'affichage d'informations -->
<div class="navigation-container">
    <div class="navigation-buttons">
        <button  class="btn btn-primary" id="nextButton">Précédent</button>
        <button  class="btn btn-primary" id="prevButton">Suivant</button>
    </div>    
</div>

<hr>
<!-- Boutons d'exportation -->
<div class="navigation-container">
    <div class="navigation-buttons d-flex justify-content-center gap-3">
        <form method="POST" action="export_csv.php">
            <button class="btn btn-primary" type="submit">Exporter en CSV</button>
        </form>
        <form method="POST" action="export_pdf.php">
            <button class="btn btn-primary" type="submit">Exporter en PDF</button>
        </form>
    </div>
</div>

<!-- Tableau des données -->
<table>
    <thead>
        <tr>
            <th style="color: black;">Année</th>
            <th style="color: black;">Période</th>
            <th style="color: black;">Localisation</th>
            <th style="color: black;">Latitude</th>
            <th style="color: black;">Longitude</th>
            <th style="color: black;">Moyenne des température(°C)</th>
            <th style="color: black;">Moyenne des précipitations (mm)</th>
            <th style="color: black;">Moyenne de l'humidité (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataStation as $data): ?>
            <tr>
                <td><?= htmlspecialchars($data['annee']) ?></td>
                <td><?= htmlspecialchars($data['periode']) ?></td>
                <td><?= htmlspecialchars($data['localisation']) ?></td>
                <td><?= htmlspecialchars($data['latitude']) ?></td>
                <td><?= htmlspecialchars($data['longitude']) ?></td>
                <td><?= htmlspecialchars($data['avg_temperature']) ?></td>
                <td><?= htmlspecialchars($data['avg_precipitation']) ?></td>
                <td><?= htmlspecialchars($data['avg_humidity']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <script>
        // Fonction pour gérer les cases à cocher de manière exclusive
        document.querySelectorAll('.exclusive-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    // Décoche toutes les autres cases
                    document.querySelectorAll('.exclusive-checkbox').forEach(cb => {
                        if (cb !== this) {
                            cb.checked = false;
                        }
                    });
                }
            });
        });

        // Fonction pour mettre à jour le libellé du champ en fonction de la granularité spatiale
        function updateLabel() {
            const spatialType = document.getElementById('spatial_type').value;
            const label = document.getElementById('spatial_label');

            // Mise à jour du texte du label
            switch (spatialType) {
                case 'ville':
                    label.textContent = 'Ville :';
                    break;
                case 'departement':
                    label.textContent = 'Département :';
                    break;
                case 'region':
                    label.textContent = 'Région :';
                    break;
                case 'metropole':
                    label.textContent = 'Métropole :';
                    break;
                default:
                    label.textContent = 'Sélection :';
            }
        }
      
    // Initialiser la carte Leaflet
    const map = L.map('map').setView([46.603354, 1.888334], 6); // Centre de la France
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
	
	// Couleur des contours
    const borderStyle = {
      color: "#00008B", // Bleu foncé
      weight: 1.5,
      fillColor: "#87CEEB", // Bleu clair pour la France
      fillOpacity: 0.6,
    };

    const hoverStyle = {
      fillColor: '#86eee0',
      fillOpacity: 0.9
    };

    // Fonction pour le rebond d'un marqueur
    const addBouncingMarker = (coordinates, popupContent) => {
      const icon = L.icon({
        iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png'
      });

      const marker = L.marker(coordinates, { bounceOnAdd: true, icon: icon }).addTo(map);
      marker.bindPopup(popupContent);
      return marker;
    };
    // Charger les groupes de données
    const dataGroups = <?= json_encode($dataGroups) ?>;

    // Variables pour la pagination
    let currentGroupIndex = 0;


    // Fonction pour déterminer la couleur du texte en fonction de la couleur du point
    function getTextColor(pointColor) {
        if (pointColor === 'lightblue' || pointColor === 'skyblue' || pointColor === 'yellow') {
            return 'darkblue'; // Texte bleu foncé
        }
        return 'white'; // Texte blanc par défaut
    }
	
	// Fonction pour mettre à jour la carte en fonction de la métrique sélectionnée
	function updateMap() {
		displayGroup(currentGroupIndex);
	}
	
	// Fonction pour convertir un numéro de mois en nom
	function getMonthName(monthNumber) {
		const monthNames = [
			"janvier", "février", "mars", "avril", "mai", "juin",
			"juillet", "août", "septembre", "octobre", "novembre", "décembre"
		];
		return monthNames[monthNumber - 1] || "Inconnu"; // -1 car le tableau commence à 0
	}
		
    // Fonction pour afficher un groupe de points sur la carte
    function displayGroup(groupIndex) {
        // Supprimer les points actuels
        map.eachLayer(layer => {
            if (layer instanceof L.Marker || layer instanceof L.DivIcon) {
                map.removeLayer(layer);
            }
        });

        // Afficher les nouveaux points
        if (groupIndex < dataGroups.length) {
            const group = Object.values(dataGroups[groupIndex])[0]; // Récupérer le premier groupe
            const firstRow = group[0]; // Première ligne pour obtenir année et période
            const annee = firstRow.annee;
            const periode = firstRow.periode;
			const granularity = document.getElementById('granularity').value; // Mettre à jour la granularité en JavaScript
			
			// Mise à jour de la boîte d'informations avec nom de mois si granularité = mois
			
			if (granularity === "mois") {
				const mois = getMonthName(periode);
				document.getElementById('infoBo').textContent = `Année : ${annee} / Mois : ${mois} (${periode})`;
			} else if (granularity === "semaine") {
				document.getElementById('infoBo').textContent = `Année : ${annee} / Semaine : ${periode}`;
			} else if (granularity === "annee") {
				document.getElementById('infoBo').textContent = `Année : ${annee}`;
			} else {
				document.getElementById('infoBo').textContent = `Année : ${annee} / Jour : ${periode}`;
			}
			
			
			const selectedMetric = document.querySelector('input[name="data_type"]:checked').value;
   
			const metricMapping = {
                temperature: {
                    label: "températures moyennes °C",
                    unit: "°C",
                    getValue: row => parseFloat(row.avg_temperature),
                    getColor: value => value > 30 ? 'red' : value > 25 ? 'orange' : value > 20 ? 'yellow' : value > 15 ? 'skyblue' : value > 10 ? 'lightblue' : value > 5 ? 'blue' : 'darkblue',
                    getColorText: value => value > 30 ? 'white' : value > 25 ? 'black' : value > 20 ? 'black' : value > 15 ? 'black' : value > 10 ? 'black' : value > 5 ? 'white' : 'white',
                    displayValue: value => `${value.toFixed(1)}`
                },
                humidity: {
                    label: "humidités Moyennes %",
                    unit: "%",
                    getValue: row => parseFloat(row.avg_humidity),
                    getColor: value => value > 90 ? 'darkblue' : value > 80 ? 'blue' : value > 70 ? 'lightblue' : value > 60 ? 'skyblue' : value > 50 ? 'skyblue' : 'yellow',
                    getColorText: value => value > 90 ? 'white' : value > 80 ? 'white' : value > 70 ? 'white' : value > 60 ? 'black' : value > 50 ? 'black' : 'black',
                    displayValue: value => `${value.toFixed(1)}`
                },
                precipitation: {
                    label: "précipitations moyennes mm",
                    unit: "mm",
                    getValue: row => parseFloat(row.avg_precipitation),
                    getColor: value => value > 10 ? 'darkblue' : value > 8 ? 'blue' : value > 5 ? 'lightblue' : value > 2 ? 'skyblue' : 'white',
                    getColorText: value => value > 10 ? 'white' : value > 8 ? 'white' : value > 5 ? 'white' : value > 2 ? 'black' : 'black',
                    displayValue: value => `${value.toFixed(1)}`
                }
            };

            const selectedConfig = metricMapping[selectedMetric];
			// Mise à jour du titre de la carte
            document.getElementById("mapTitle").innerText = `Carte des ${selectedConfig.label}`;

            // Suppression des anciens marqueurs
            map.eachLayer(layer => {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
            group.forEach(row => {
				const latitude = parseFloat(row.latitude);
				const longitude = parseFloat(row.longitude);
				const localisation = row.localisation;
				const value = selectedConfig.getValue(row);
				

                if (!isNaN(latitude) && !isNaN(longitude) && !isNaN(value)) {
                            const color = selectedConfig.getColor(value);
                            const textColor = selectedConfig.getColorText(value);selectedConfig.getColor(value);

                            const icon = L.divIcon({
                                html: `<div style="
                                    background-color: ${color};
                                    width: 35px;
                                    height: 35px;
                                    border-radius: 50%;
                                    border: 2px solid white;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: ${textColor};
                                    font-size: 12px;
                                    font-weight: bold;">
                                    ${selectedConfig.displayValue(value)}
                                </div>`,
                                className: '',
                                iconSize: [35, 35],
                                iconAnchor: [22.5, 22.5]
                            });

                            L.marker([latitude, longitude], { icon })
                                .addTo(map)
                                .bindPopup(`
                                    <b>${localisation}</b><br>
                                    ${selectedConfig.label} : ${value.toFixed(2)} ${selectedConfig.unit}
                                `);
                        }
            
			});
        }
    
	}
	
	// Charger les régions (GeoJSON)
	fetch("https://raw.githubusercontent.com/gregoiredavid/france-geojson/master/regions.geojson")
	  .then(response => response.json())
	  .then(data => {
		L.geoJSON(data, {
		  
		  style: {
			color: "#00008B", // Bleu foncé
			weight: 1,
			fillOpacity: 0.25,
		  },
		  onEachFeature: (feature, layer) => {
			const name = feature.properties.nom;
			layer.bindPopup(`<strong>Région :</strong> ${name}`);
		  }
		}).addTo(map);
	  });
	
   

    // Gestion du bouton précédent
    document.getElementById('prevButton').addEventListener('click', () => {
        if (currentGroupIndex > 0) {
            currentGroupIndex--;
            displayGroup(currentGroupIndex);
        } else {
            alert("Pas de données précédentes.");
        }
    });

    // Gestion du bouton suivant
    document.getElementById('nextButton').addEventListener('click', () => {
        if (currentGroupIndex + 1 < dataGroups.length) {
            currentGroupIndex++;
            displayGroup(currentGroupIndex);
        } else {
            alert("Pas de données supplémentaires.");
        }
    });

    // Afficher le premier groupe au chargement
    displayGroup(currentGroupIndex);
</script>


</body>
</html>
