<?php
require_once "Model.php";
class ModelBD {


    static function recherche($ville,$date,$region = null){
        $ville =  strtoupper($ville);
        $vowels = array(" ");
        $villes = str_replace($vowels, "%20", $ville);
        if ($ville){

        $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?order_by=date&limit=100&refine=nom%3A$ville";
            if ($date) {
                $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?order_by=date&limit=100&refine=date%3A".$date[0].$date[1].$date[2].$date[3]."%2F".$date[5].$date[6]."%2F".$date[8].$date[9]."&refine=nom%3A$ville";
        }
    }elseif($region){
        $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&refine=nom_reg%3A$region";
                if ($date) {
                    $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?limit=100&refine=date%3A".$date[0].$date[1].$date[2].$date[3]."%2F".$date[5].$date[6]."%2F".$date[8].$date[9]."&refine=nom_reg%3A$region";
            }
    }else{
        return "Veuillez entrer un nom de ville ou une région valide";
    }

    $info = array();
    $json_content = file_get_contents($url);
    $data = json_decode($json_content, true);
    if($data["results"]==NULL){
        echo"Aucun résultat pour cette recherche";
    }
    foreach($data["results"] as $row){ 	
    	
		self::updateDataStation(Model::getPdo(), $row); 
        $info[] =array ('nom'=> $row["nom"],
        'date' => $row["date"],
        'tc' => $row["tc"],
        'dd' => $row["dd"],
        'ff' => $row["ff"],
        'u' => $row["u"],
        'rr24' => $row["rr24"],
        'phenspe1' => $row["phenspe1"],
    );
        }
        return $info;
    }
    static function rechercheinter($ville, $date, $inter, $region = '') {
        // Convertir les dates en objets DateTime
        $dateDebut = new DateTime($date);
        $dateFin = new DateTime($inter);
    
        // Vérifier que les dates sont valides
        if ($dateDebut > $dateFin) {
            return "La date de début doit être antérieure à la date de fin.";
        }
    
        $info = array(); // Tableau pour stocker les résultats
    
        // Parcourir chaque date de l'intervalle
        while ($dateDebut <= $dateFin) {
            $currentDate = $dateDebut->format('Y/m/d'); // Formater la date actuelle
    
            // Construire l'URL en fonction des paramètres fournis
            if ($ville) {
                $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?order_by=date&limit=100&refine=date%3A$currentDate&refine=nom%3A$ville";
            } elseif ($region) {
                $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/donnees-synop-essentielles-omm/records?order_by=date&limit=100&refine=date%3A$currentDate&refine=nom_reg%3A$region";
            } else {
                return "Veuillez entrer un nom de ville ou une région valide";
            }
    
            // Récupérer et traiter les données de l'URL
            $json_content = file_get_contents($url);
            $data = json_decode($json_content, true);
    
            if (isset($data["results"]) && !empty($data["results"])) {
                foreach ($data["results"] as $row) {		
                	self::updateDataStation(Model::getPdo(), $row); 
                    $info[] =array (
                    'nom'=> $row["nom"],
                    'date' => $row["date"],
                    'tc' => $row["tc"],
                    'dd' => $row["dd"],
                    'ff' => $row["ff"],
                    'u' => $row["u"],
                    'rr24' => $row["rr24"],
                    'phenspe1' => $row["phenspe1"],
                );
                }
            } else {
                echo "Aucun résultat pour la date : $currentDate\n";
            }
    
            // Passer à la date suivante
            $dateDebut->modify('+1 day');
        }
    
        return $info; // Retourner toutes les informations collectées
    }
    static function créeutils($id,$nom,$prenom,$mdp,$ville){
        $sq =  "Select nom_utilisateur,prenom_utilisateur from utilisateur";
        $stmt = Model::getPdo()->prepare($sq);
        $stmt->execute();
            if (self::exist($id) && $id!=0)  {
                return false;
            }
        $sql="INSERT INTO utilisateur (id_utilisateur,nom_utilisateur,prenom_utilisateur,mot_de_passe,nom_ville) VALUES (:id,:nom,:prenom,:mdp,:ville)";
        $stmt= Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
            "nom" => $nom,
            "prenom" => $prenom,
            "mdp" => password_hash($mdp, PASSWORD_DEFAULT),
            "ville" => $ville
        );
        $stmt->execute($values);
        return true;
    }
    static function update($id,$NOuvNom,$NOuvPrenom,$mdp,$ville){
        self::exist($id);
            $sql="UPDATE utilisateur SET nom_utilisateur=:nom,prenom_utilisateur=:prenom,mot_de_passe=:mdp,nom_ville=:ville WHERE id_utilisateur=:id";
            $stmt = Model::getPdo()->prepare($sql);	
            $values = array(
            "nom" => $NOuvNom,
            "prenom" => $NOuvPrenom,
            "mdp" => password_hash($mdp, PASSWORD_DEFAULT),
            "ville" => $ville,
            'id' => $id,
        );
        $stmt->execute($values);

        
    }
    static function exist($id){
        $sql = "SELECT * FROM utilisateur WHERE Id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id
        );
        $stmt->execute($values);
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function connexion($id,$mdp){
        $sql = "SELECT mot_de_passe FROM utilisateur WHERE id_utilisateur=:id" ;
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id
        );
        $stmt->execute($values);
        if (password_verify($mdp, $stmt->fetch()['mot_de_passe'])) {
            return true;
        } else {
            return false;
        }
    } 

    static function getVille($id){
        $sql = "SELECT nom_ville FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $row = $stmt->fetch();
        return $row['nom_ville'];
    }
    static function getpwd($id){
        $sql = "SELECT mot_de_passe FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $row = $stmt->fetch();
        return $row['mot_de_passe'];    
    }
    static function getnom($id){
        $sql = "SELECT nom_utilisateur FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $row = $stmt->fetch();
        return $row['nom_utilisateur'];
    }
    static function getprenom($id){
        $sql = "SELECT prenom_utilisateur FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $row = $stmt->fetch();
        return $row['prenom_utilisateur'];
    }
    static function ville(){
        $sql = "SELECT nom_ville FROM ville";
        $stmt = Model::getPdo()->prepare($sql);
        $stmt->execute();
        foreach ($stmt->fetchAll() as $row) {
            $ville[] = $row['nom_ville'];
        }
        return $ville;
    }
    static function region(){
        $sql = "SELECT nom_region FROM region";
        $stmt = Model::getPdo()->prepare($sql);
        $stmt->execute();
        foreach ($stmt->fetchAll() as $row) {
            $region[] = $row['nom_region'];
        }
        return $region;
    }
    static function getrole($id){
        if (self::exist($id)){
        $sql = "SELECT role FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $row = $stmt->fetch();
        return $row['role'];
    }else{
    return 0;
    }
    }
    static function getutils(){
        $sql = "SELECT * FROM utilisateur";
        $stmt = Model::getPdo()->prepare($sql);
        $stmt->execute();
        foreach ($stmt->fetchAll() as $row) {
            $utils[] = $row;
        }
        return $utils;
    }
    static function deleteutils($id){
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $sql = "DELETE FROM tableau_admin WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);

    }
    static function getutilsbyid($id){
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "id" => $id,
        );
        $stmt->execute($values);
        $row = $stmt->fetch();
        return $row;
    }
    static function adminupdate($id,$nom,$prenom,$ville,$role){
        $sql = "UPDATE utilisateur SET nom_utilisateur=:nom,prenom_utilisateur=:prenom,nom_ville=:ville,role=:role WHERE id_utilisateur=:id";
        $stmt = Model::getPdo()->prepare($sql);
        $values = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "ville" => $ville,
            "role" => $role,
            "id" => $id,
        );
        $stmt->execute($values);
    }

static function accepter($id){
    $sql = "UPDATE utilisateur SET role='utilisateur' WHERE id_utilisateur=:id";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
    );
    $stmt->execute($values);
}
static function refuser($id){
    $sql = "DELETE FROM utilisateur WHERE id_utilisateur=:id";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
    );
    $stmt->execute($values);



}
static function ajoutaction($id,$action,$details_action){
    $sql = "INSERT INTO tableau_admin (id_utilisateur,action,details_action) VALUES (:id,:action,:details_action)";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
        "action" => $action,
        "details_action" => $details_action
    );
    $stmt->execute($values);
}
static function getactions(){
    $sql = "SELECT * FROM tableau_admin";
    $stmt = Model::getPdo()->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        return 'pas encore d\'actions';
    }else{
    foreach ($stmt->fetchAll() as $row) {
        $actions[] = $row;
    }
    return $actions;
}
}
static function deleteaction($id){
    $sql = "DELETE FROM tableau_admin WHERE id_action=:id";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
    );
    $stmt->execute($values);
}
static function suptout(){
    $sql = "delete from tableau_admin";
    $stmt = Model::getPdo()->prepare($sql);
    $stmt->execute();
}

static function creautilsadmin($id,$nom,$prenom,$mdp,$ville){
    $sql = "INSERT INTO utilisateur (id_utilisateur,nom_utilisateur,prenom_utilisateur,mot_de_passe,nom_ville,role) VALUES (:id,:nom,:prenom,:mdp,:ville,:role)";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
        "nom" => $nom,
        "prenom" => $prenom,
        "mdp" => password_hash($mdp, PASSWORD_DEFAULT),
        "ville" => $ville,
        "role" => "utilisateur"
    );
    $stmt->execute($values);
}
static function villedetails($ville){
    $sql = "SELECT * FROM ville WHERE nom_ville=:ville";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "ville" => $ville,
    );
    $stmt->execute($values);
    $row = $stmt->fetch();
    return $row;
}
static function getvillestat($ville){
    $date2 = (new DateTime())->modify('-1 day')->format('Y-m-d');
    $date1 = (new DateTime())->modify('-10 days')->format('Y-m-d');
    $ville =  strtoupper($ville);
    $vowels = array(" ");
    $ville = str_replace($vowels, "%20", $ville);
    $donner = self::rechercheinter($ville, $date1, $date2);
    return $donner;
}
static function createmeteo($nom,$def,$id){
    $sql = "INSERT INTO méteoteque (id_utilisateur,nom,definition) VALUES (:id,:nom,:def)";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "nom" => $nom,
        "def" => $def,
        "id" => $id,
    );
    $stmt->execute($values);
}
static function getmeteo($id){
    $sql = "SELECT * FROM méteoteque WHERE id_utilisateur=:id";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
    );
    $stmt->execute($values);
    $rows = $stmt->fetchAll();
    return $rows;
}
static function deletemeteo($id){
    $sql = "DELETE FROM méteoteque WHERE id=:id";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
    );
    $stmt->execute($values);
    $sql = "DELETE FROM donnéemeteoteque WHERE id_meteoteque=:id";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id" => $id,
    );
    $stmt->execute($values);
}

static function ajouter($id_meteo,$station,$date,$temperature,$temp_max,$temp_min,$humidite,$vitesse_vent,$direction_vent,$precipitation,$phenomene)
{
    $sql = "INSERT INTO donnéemeteoteque (station,date,temperature,temp_max,temp_min,humidité,vitesse_vent,direction_vent,precipitation,phenomene_speciaux,id_meteoteque) VALUES (:station,:date,:temperature,:temp_max,:temp_min,:humidite,:vitesse_vent,:direction_vent,:precipitation,:phenomene,:id_meteo)";

    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "station" => $station,
        "date" => $date,
        "temperature" => $temperature,
        "temp_max" => $temp_max,
        "temp_min" => $temp_min,
        "humidite" => $humidite,
        "vitesse_vent" => $vitesse_vent,
        "direction_vent" => $direction_vent,
        "precipitation" => $precipitation,
        "phenomene" => $phenomene,
        "id_meteo" => $id_meteo
    );
    $stmt->execute($values);
}
static function getdonnée($id_meteo)
{
    $sql = "SELECT * FROM donnéemeteoteque WHERE id_meteoteque=:id_meteo";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id_meteo" => $id_meteo,
    );
    $stmt->execute($values);
    $rows = $stmt->fetchAll();
    return $rows;
}
static function deletedonne($id_donnee)
{
    $sql = "DELETE FROM donnéemeteoteque WHERE id=:id_donnee";
    $stmt = Model::getPdo()->prepare($sql);
    $values = array(
        "id_donnee" => $id_donnee,
    );
    $stmt->execute($values);
}

static function meteo() {
    $meteo = []; // Initialiser la variable pour éviter qu'elle soit null
    $sql = "SELECT * FROM méteoteque";
    $stmt = Model::getPdo()->prepare($sql);

    try {
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $meteo[] = $row;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des données météorologiques : " . $e->getMessage());
    }

    return $meteo;
}


	public static function getAveragedData($spatialType, $spatialValue, $granularity, $startDate, $endDate) {
		try {
			$pdo = Model::getPdo();
			
			// Construire la clause WHERE pour les filtres
			$whereClauses = [];
			$params = [];
			
			$filter = "";
			if ($spatialValue != ""){
				// Définir le filtre
				$filter = match ($spatialType) {
					'ville' => ' UPPER(v.nom_ville)=UPPER("'.$spatialValue.'")',
					'departement' => ' UPPER(d.nom_departement)=UPPER("'.$spatialValue.')',
					'region' => ' UPPER(r.nom_region)=UPPER("'.$spatialValue.'")',
					default => '' // Métropole entière
				};
			
			}
			
			// Définir la colonne de regroupement
			$groupByColumn = match ($spatialType) {
				'ville' => 'v.nom_ville',
				'departement' => 'd.nom_departement',
				'region' => 'r.nom_region',
				default => '"Métropole"' // Métropole entière
			};
			
			// Define the latitude column based on the spatial type
			$latitude = match ($spatialType) {
				'ville' => 's.latitude',
				'departement' => 'd.latitude',
				'region' => 'r.latitude',
				default => '46.603354', // france
			};
			
			// Define the latitude column based on the spatial type
			$longitude = match ($spatialType) {
				'ville' => 's.longitude',
				'departement' => 'd.longitude',
				'region' => 'r.longitude',
				default => '1.888334', // france
			};

			if (!empty($startDate)) {
				$whereClauses[] = "date >= :startDate";
				$params[':startDate'] = $startDate;
			}

			if (!empty($endDate)) {
				$whereClauses[] = "date <= :endDate";
				$params[':endDate'] = $endDate;
			}

			$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
			if (!empty($filter)) {
				if (!empty($whereSql)) 
					$whereSql = $whereSql.' AND '.$filter;
				else 
					$whereSql = ' WHERE ' . $filter ;
			}

			// Définir la granularité temporelle
			switch ($granularity) {
				case 'semaine':
					$dateGroup = "YEAR(date) AS annee, WEEK(date) AS periode";
					break;
				case 'mois':
					$dateGroup = "YEAR(date) AS annee, MONTH(date) AS periode";
					break;
				case 'annee':
					$dateGroup = "YEAR(date) AS annee, '' AS periode";
					break;
				default: // jour
					$dateGroup = "YEAR(date) AS annee, DATE(date) AS periode";
			}

			// Requête SQL pour calculer les moyennes
		
			$sql = "
				SELECT 
					$dateGroup ,					
					$groupByColumn AS localisation,
					$latitude AS latitude,
					$longitude AS longitude,
					AVG(ds.tc) AS avg_temperature,
					AVG(ds.rr24) AS avg_precipitation,
					AVG(ds.u) AS avg_humidity
				FROM data_station ds		
				JOIN station s ON ds.numer_sta = s.numer_sta
				LEFT JOIN ville v ON s.nom = v.nom_ville
				JOIN departement d ON s.code_dep = d.code_departement
				JOIN region r ON d.id_region = r.id_region
				$whereSql
				GROUP BY annee, periode, $groupByColumn 
				ORDER BY  annee DESC, periode DESC, $groupByColumn DESC
			";
			//error_log($sql, 3, "C:/xampp/htdocs/sae/logs/app.log");

			$stmt = $pdo->prepare($sql);
			foreach ($params as $key => $value) {
				$stmt->bindValue($key, $value);
			}

			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Erreur dans getAveragedData : " . $e->getMessage().PHP_EOL, 3, "C:/xampp/htdocs/sae/logs/app.log");
			return [];
		}
	}

	
	static function updateDataStation($pdo, $row) {
		try{
			// Préparer les informations nécessaires pour les tables station et data_station
			$numer_sta = $row["numer_sta"]; // Identifiant unique de la station
			$nom_station = $row["nom"]; // Nom de la station
			$latitude = $row["latitude"]; // Latitude
			$longitude = $row["longitude"]; // Longitude
			$altitude = $row["altitude"]; // Altitude
			$libgeo = $row["libgeo"]; // Localisation géographique
			$code_dep = $row["code_dep"]; // Code département

			$date = $row["date"]; // Date et heure de la mesure
			$mois_de_l_annee = date("n", strtotime($date)); // Mois de l'année (1 à 12)
			$pres = $row["pres"]; // Pression atmosphérique
			$dd = $row["dd"]; // Direction du vent
			$ff = $row["ff"]; // Vitesse moyenne du vent
			$tc = $row["tc"]; // Température
			$tn24c = $row["tn24c"]; // Température minimale des dernières 24 heures
			$tx24c = $row["tx24c"]; // Température maximale des dernières 24 heures
			$u = $row["u"]; // Humidité relative
			$rr24 = $row["rr24"]; // Cumul des précipitations sur 24 heures
			$n = $row["n"]; // Nébulosité totale
			
			 
			// Vérifier si la station existe déjà dans la table station
			$query_station = "SELECT COUNT(*) FROM station WHERE numer_sta = :numer_sta";
			$stmt_station = $pdo->prepare($query_station);
			$stmt_station->execute([':numer_sta' => $numer_sta]);
			$station_exists = $stmt_station->fetchColumn() > 0;

			if (!$station_exists) {
				// Insérer dans la table station si l'enregistrement n'existe pas
				$insert_station = "
					INSERT INTO station (numer_sta, nom, latitude, longitude, altitude, libgeo, code_dep)
					VALUES (:numer_sta, :nom, :latitude, :longitude, :altitude, :libgeo, :code_dep)";
				$stmt_insert_station = $pdo->prepare($insert_station);
				$stmt_insert_station->execute([
					':numer_sta' => $numer_sta,
					':nom' => $nom_station,
					':latitude' => $latitude,
					':longitude' => $longitude,
					':altitude' => $altitude,
					':libgeo' => $libgeo,
					':code_dep' => $code_dep
				]);
			}

			// Vérifier si la donnée existe déjà dans la table data_station
			$query_data_station = "
				SELECT COUNT(*) 
				FROM data_station 
				WHERE numer_sta = :numer_sta AND date = :date";
			$stmt_data_station = $pdo->prepare($query_data_station);
			$stmt_data_station->execute([
				':numer_sta' => $numer_sta,
				':date' => $date
			]);
			$data_exists = $stmt_data_station->fetchColumn() > 0;

			if (!$data_exists) {
				// Insérer dans la table data_station si l'enregistrement n'existe pas
				$insert_data_station = "
					INSERT INTO data_station (numer_sta, date, mois_de_l_annee, pres, dd, ff, tc, tn24c, tx24c, u, rr24, n)
					VALUES (:numer_sta, :date, :mois_de_l_annee, :pres, :dd, :ff, :tc, :tn24c, :tx24c, :u, :rr24, :n)";
				$stmt_insert_data_station = $pdo->prepare($insert_data_station);
				$stmt_insert_data_station->execute([
					':numer_sta' => $numer_sta,
					':date' => $date,
					':mois_de_l_annee' => $mois_de_l_annee,
					':pres' => $pres,
					':dd' => $dd,
					':ff' => $ff,
					':tc' => $tc,
					':tn24c' => $tn24c,
					':tx24c' => $tx24c,
					':u' => $u,
					':rr24' => $rr24,
					':n' => $n
				]);
			}
		} catch (Exception $e) {
			error_log("Erreur dans updateDataStation : " . $e->getMessage() .PHP_EOL, 3, "C:/xampp/htdocs/sae/logs/app.log");
			return [];
		}
	}
	// Méthode pour récupérer toutes les stations
    public static function getStations() {
        $query = "SELECT id, nom FROM station";
        $stmt = Model::getPdo()->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer les détails d'une station par son ID
    public static function getStationDetails($stationId) {
        $query = "SELECT * FROM station WHERE id = :id";
        $stmt = Model::getPdo()->prepare($query);
        $stmt->execute([':id' => $stationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer les données climatiques d'une station
    public static function getStationData($numer_sta) {
        $query = "SELECT * FROM data_station WHERE numer_sta = :numer_sta";
        $stmt = Model::getPdo()->prepare($query);
        $stmt->execute([':numer_sta' => $numer_sta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	

	
}


?>