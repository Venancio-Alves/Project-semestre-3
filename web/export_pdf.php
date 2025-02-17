<?php
require_once __DIR__ . '/../src/Model/ModelBD.php';

require_once __DIR__ . '/../libs/fpdf186/fpdf.php'; // Chemin vers FPDF

$dataStation = ModelBD::getAveragedData('ville', '', 'jour', null, null);

// Initialiser FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Ajouter le titre
$pdf->Cell(0, 10, 'Export des Données Climatiques', 0, 1, 'C');
$pdf->Ln(10);

// Ajouter les en-têtes des colonnes
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 10, 'Année', 1);
$pdf->Cell(20, 10, 'Période', 1);
$pdf->Cell(40, 10, 'Localisation', 1);
$pdf->Cell(25, 10, 'Latitude', 1);
$pdf->Cell(25, 10, 'Longitude', 1);
$pdf->Cell(30, 10, 'Temp. Moy.', 1);
$pdf->Cell(30, 10, 'Précip. Moy.', 1);
$pdf->Cell(30, 10, 'Humidité Moy.', 1);
$pdf->Ln();

// Ajouter les données
$pdf->SetFont('Arial', '', 10);
foreach ($dataStation as $row) {
    $pdf->Cell(20, 10, $row['annee'], 1);
    $pdf->Cell(20, 10, $row['periode'], 1);
    $pdf->Cell(40, 10, $row['localisation'], 1);
    $pdf->Cell(25, 10, $row['latitude'], 1);
    $pdf->Cell(25, 10, $row['longitude'], 1);
    $pdf->Cell(30, 10, $row['avg_temperature'], 1);
    $pdf->Cell(30, 10, $row['avg_precipitation'], 1);
    $pdf->Cell(30, 10, $row['avg_humidity'], 1);
    $pdf->Ln();
}

// Sortie du fichier PDF
$pdf->Output('D', 'export_donnees.pdf');
exit;
