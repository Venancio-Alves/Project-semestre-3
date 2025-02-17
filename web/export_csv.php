<?php
// Inclure les données
require_once __DIR__ . '/../src/Model/ModelBD.php';
$dataStation = ModelBD::getAveragedData('ville', '', 'jour', null, null);

// Définir l'en-tête du fichier CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="export_donnees.csv"');

// Ouvrir le flux de sortie pour écrire le CSV
$output = fopen('php://output', 'w');

// Écrire les en-têtes des colonnes
fputcsv($output, ['Année', 'Période', 'Localisation', 'Latitude', 'Longitude', 'Température Moyenne', 'Précipitations Moyennes', 'Humidité Moyenne']);

// Écrire les données
foreach ($dataStation as $row) {
    fputcsv($output, [
        $row['annee'],
        $row['periode'],
        $row['localisation'],
        $row['latitude'],
        $row['longitude'],
        $row['avg_temperature'],
        $row['avg_precipitation'],
        $row['avg_humidity']
    ]);
}

// Fermer le flux
fclose($output);
exit;
