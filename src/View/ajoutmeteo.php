<div class="container mt-5">
    <h1 class="text-center mb-4">Météo Information</h1>
    <form method="get" class="bg-light p-4 rounded shadow">
        <fieldset>
            <legend class="mb-3">Recherche météo :</legend>

            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="id_meteo" value="<?= $_GET['id_meteo']?>">

            <div class="mb-3">
                <label for="ville" class="form-label">station :</label>
                <input list="browsers"  id="villes" name="villes" class="form-control" required >
            </div>
            <div class="mb-3">
            <label for="ville" class="form-label">filtre :</label><br>
            <label for="ville" class="form-label">temperature :</label>
            <input type="checkbox" class="form-check-input" id="save" name="temperature" value="temperature">
            <label for="ville" class="form-label">direction du vent :</label>
            <input type="checkbox" class="form-check-input" id="save" name="direction_du_vent" value="direction_du_vent">
            <label for="ville" class="form-label">vitesse du vent :</label>
            <input type="checkbox" class="form-check-input" id="save" name="vitesse_du_vent" value="vitesse_du_vent">
            <label for="ville" class="form-label">humidité :</label>
            <input type="checkbox" class="form-check-input" id="save" name="humidite" value="humidite">
            <label for="ville" class="form-label">Precipitation sur 24 heures:</label>
            <input type="checkbox" class="form-check-input" id="save" name="precipitation_sur_24_heures" value="precipitation_sur_24_heures">
            <label for="ville" class="form-label">Phénomènes spéciaux :</label>
            <input type="checkbox" class="form-check-input" id="save" name="phenomene_special" value="phenomene_special">

            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="date" class="form-label">Date :</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>

            </div>




            <button type="submit" name="ok" class="btn btn-primary">Valider</button>
        </fieldset>
    </form>
    <datalist id="browsers">
    <?php
    require_once '../src/Model/modelbd.php';
    $ville = ModelBD::Ville();
    foreach ($ville as $row) {
        $row = htmlspecialchars($row, ENT_QUOTES, 'UTF-8');
        echo "<option value='$row'>";    
    }
   ?>
</datalist>
