<?php
class Controller {

    private static function afficheVue(string $cheminVue, array $parametres = []): void {

        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__. "/../View/$cheminVue"; // Charge la vue
    }
    public static function acceuil(){
        session_start();
        self::afficheVue("View.php", ["pagetitle" => "Accueil", "cheminVueBody" => "acc.php"]);
    }
    public static function pagerech(){
        session_start();
            self::afficheVue("View.php", ["pagetitle" => "recherche", "cheminVueBody" => "recherche.php"]);
    }
    public static function rech(){
        require __DIR__. "/../model/ModelBD.php";
         session_start();
        $villes = $_GET['villes']?? '';
        $date = $_GET['date']?? "";
        $region = $_GET['regions'] ?? '';
        $save = $_GET['save']?? "";   
        $inter = $_GET['inter']?? "0000-00-00";
        $id = $_SESSION['user']['id'] ?? "";
        $filtre = array (
        'tc' => $_GET['tc']?? "",
        'ff'=> $_GET['ff']?? "",
        'dd' => $_GET['dd']?? "",
        'u' => $_GET['u']?? "",
        'rr24' => $_GET['rr24']?? "",
        'phenspe1' => $_GET['phenspe1']?? "",
        'coordonnees' => $_GET['coordonnees']?? "",
        );


        $villes =  strtoupper($villes);
        $vowels = array(" ");
        $villes = str_replace($vowels, "%20", $villes);
        $region = str_replace($vowels, "%20", $region);
        $region =  ucfirst($region);
        if ($inter && $inter!= "0000-00-00"){
            $resultat=ModelBD::rechercheinter($villes, $date ,$inter , $region);


        }else{
        $resultat= ModelBD::recherche($villes, $date, $region);
        }
        ModelBD::ajoutaction($id, "Recherche", "Recherche sur la ville : $villes, la date : $date, la région : $region");
        return self::afficheVue("View.php",  ["resultat" => $resultat, "pagetitle" => "Recherche", "cheminVueBody" => "resultat.php","filtre"=> $filtre]);
    }
    Public static function creacompte(){
        require_once __DIR__. "/../model/ModelBD.php";
        $ville = ModelBD::ville();
        self::afficheVue("view.php", ["pagetitle" => "Créer un compte", "cheminVueBody" => "creacompte.php", 'message' => '', "ville" => $ville]);
    }

    Public static function Error(string $message= "") : void {
        $messages = "Problème avec le";
        $messages .= " : " . htmlspecialchars($message);

        self::afficheVue(cheminVue: 'view.php', parametres: ["pagetitle" => "Error", "cheminVueBody" => "error.php", "message" => $messages]);
    }
   
   
public static function comptecree(){
    require __DIR__. "/../model/ModelBD.php";
    ini_set('session.gc_maxlifetime ' , 3600);
    session_start();
    $nom = $_GET['nom'];
    $prenom = $_GET['prenom'];
    $pwd = $_GET['pwd'];
    $ville = $_GET['ville']??"";
    $id = $_GET['id'];
    $a = Modelbd::créeutils($id,$nom, $prenom, $pwd, $ville);
    ModelBD::ajoutaction($id, "Créer_un_compte", "demande Création du compte utilisateur : $nom $prenom");
    if ($a ==False){
        self::afficheVue("view.php", ["pagetitle" => "Créer un compte", "cheminVueBody" => "creacompte.php", "message" => "identifiant déjà pris"]);
    }else{
        $_SESSION['user'] = [
            'prenom' => $prenom,
            'nom' => $nom,
            'id' => $id,
            'pwd' => $pwd,
            'ville' =>  ModelBD::getVille($id),
            'logged_in' => true,
            'role' => ModelBD::getRole($id)

        ];
    self::afficheVue("view.php", ["pagetitle" => "Compte utilisateur", "cheminVueBody" => "redirection.php"]);}

}
public static function Update(){
    session_start();
    self::afficheVue("view.php", ["pagetitle" => "Compte utilisateur", "cheminVueBody" => "update.php"]);
}
Public static function updated(){
    require __DIR__. "/../model/ModelBD.php";
    session_start();
    $pwd = $_GET['nouveau_pwd'];
    $ville = $_GET['nouveau_ville']??"";
    $newNom = $_GET['nouveau_nom'];
    $newPrenom = $_GET['nouveau_Prenom'];
    Modelbd::update($_SESSION['user']['id'],$newNom, $newPrenom, $pwd, $ville);
    ModelBD::ajoutaction($_SESSION['user']['id'], "Modification", "Modification apporté au compte utilisateur : ". $_SESSION['user']['nom'] ."=>".$newNom."| prenom : ".$_SESSION['user']['prenom']."=>".$newPrenom."| ville : ".$_SESSION['user']['ville']."=>".$ville);
    $_SESSION['user'] = [
        'prenom' => $newPrenom,
        'nom' => $newNom,
        'id' => $_SESSION['user']['id'],
        'ville' =>  $ville,
        'pwd' => $pwd,
        'logged_in' => true,
        'role' => ModelBD::getRole($_SESSION['user']['id'])

    ];
    self::afficheVue("view.php", ["pagetitle" => "Compte utilisateur", "cheminVueBody" => "recherche.php"]);



}
public static function compte(){
    require_once __DIR__. "/../model/ModelBD.php";
    if (!ModelBD::exist($_SESSION['user']['id']?? '')){
        session_start();
        }
    self::afficheVue("view.php", ["pagetitle" => "Compte utilisateur", "cheminVueBody" => "comptUtils.php"]);
   
}
public static function connexion(){
    self::afficheVue("view.php", ["pagetitle" => "Connection", "cheminVueBody" => "connection.php" ,"message" =>'']);
}
   
public static function verifeconnexion(){
    require __DIR__. "/../model/ModelBD.php";
    $pwd = $_GET['pwd'];
    $id = $_GET['id'];
    session_start();
    if (ModelBD::connexion($id,  $pwd)){
        ModelBD::ajoutaction($id, "Connexion", "Connexion sur le compte utilisateur : $id date: ".date('Y-m-d H:i:s'));
        $_SESSION['user'] = [
            'prenom' => ModelBD::getPrenom($id),
            'nom' => ModelBD::getnom($id),
            'id' => $id,
            'pwd' => $pwd,
            'ville' =>  ModelBD::getVille($id),
            'logged_in' => true,
            'role' => ModelBD::getRole($id)

        ];
        if (ModelBD::getRole($id) == "admin"){
            self::afficheVue("view.php", ["pagetitle" => "Admin", "cheminVueBody" => "admin.php", "utils" => ModelBD::getutils(),'actions' => ModelBD::getactions()]);
        }else{
            self::afficheVue("view.php", ["pagetitle" => "Compte utilisateur", "cheminVueBody" => "redirection.php"]);

        }


    }else{
        ModelBD::ajoutaction($id, "Connexion", "Echec de connexion sur le compte utilisateur : $id ".date('Y-m-d H:i:s'));
        self::afficheVue("view.php", ["pagetitle" => "Connexion", "cheminVueBody" => "connection.php", "message" => "id d'utilisateur ou mot de passe incorrect"]);
    }
}
public static function deconnexion(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    ModelBD::ajoutaction($_SESSION['user']['id'], "Déconnexion", "Déconnexion du compte utilisateur :". date("Y-m-d H:i:s"));
    session_destroy();
    self::afficheVue("view.php", ["pagetitle" => "Accueil", "cheminVueBody" => "redirection.php"]);
}
public static function admin(){
    require_once __DIR__. "/../model/ModelBD.php";
    if (!ModelBD::exist($_SESSION['user']['id']?? '')){
    session_start();
    }
    $utils = ModelBD::getutils();
    $actions = ModelBD::getactions();
    self::afficheVue("view.php", ["pagetitle" => "Admin", "cheminVueBody" => "admin.php", "utils" => $utils ,'actions' => $actions]);

}
public static function delete(){
    require_once __DIR__. "/../model/ModelBD.php";
    $id = $_GET['id'];
    session_start();
    ModelBD::ajoutaction($_SESSION['user']['id'], "Suppression admin", "Suppression de l'utilisateur : ".$id);
    ModelBD::deleteutils($id);
    self::admin();
}
public static function adminupdate(){
    require_once __DIR__. "/../model/ModelBD.php";
    $id = $_GET['id'];
    $utils = ModelBD::getutilsbyid($id);
    self::afficheVue("view.php", ["pagetitle" => "Admin", "cheminVueBody" => "adminupdate.php", "utils" => $utils]);
}
public static function adminupdated(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    $id = $_GET['id'];
    $nom = $_GET['nom'];
    $prenom = $_GET['prenom'];
    $ville = $_GET['ville'];
    $role = $_GET['role'];
    ModelBD::ajoutaction($_SESSION['user']['id'], "Modification admin", "Modification apporté à l'utilisateur : ".$id." |nom : ". ModelBD::getnom($id) ."=>".$nom. " | prenom : ".ModelBD::getPrenom($id)."=>".$prenom."| ville : ".ModelBD::getVille($id)."=>".$ville."| role : ".ModelBD::getRole($id)."=>".$role);
    ModelBD::adminupdate($id, $nom, $prenom, $ville,$role);
    self::admin();
}
public static function creeradmin(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    $ville = ModelBD::ville();
    self::afficheVue("view.php", ["pagetitle" => "Admin", "cheminVueBody" => "creeradmin.php",'message' => '', 'ville' => $ville]);

}
public static function comptecreeradmin(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    $id = $_GET['id'];
    $nom = $_GET['nom'];
    $prenom = $_GET['prenom'];
    $pwd = $_GET['pwd'];
    $ville = $_GET['ville'];
    
    if (ModelBD::exist($id) == False){
        ModelBD::creautilsadmin($id, $nom, $prenom, $pwd, $ville);
        ModelBD::ajoutaction($_SESSION['user']['id'], "Création admin", "Création de l'utilisateur : $id ");
        self::admin();

    }else{
        $ville = ModelBD::ville();
        ModelBD::ajoutaction($_SESSION['user']['id'], "Création admin", "Echec de la création de l'utilisateur : $id déjà existant");
        self::afficheVue("view.php", ["pagetitle" => "Admin", "cheminVueBody" => "creeradmin.php", "message" => "identifiant déjà pris", 'ville' => $ville]);
        }
}
public static function accepter(){  
    require_once __DIR__. "/../model/ModelBD.php";
    $id = $_GET['id'];
    ModelBD::accepter($id);
    self::admin();
}
public static function refuser(){
    require_once __DIR__. "/../model/ModelBD.php";
    $id = $_GET['id'];
    ModelBD::refuser($id);
    self::admin();
}
public static function deleteaction(){
    require_once __DIR__. "/../model/ModelBD.php";
    $id = $_GET['id'];
    ModelBD::deleteaction($id);
    self::admin();
}
public static function suptout(){
    require_once __DIR__. "/../model/ModelBD.php";
    ModelBD::suptout();
    self::admin();
}
public static function deletehisto(){
    require_once __DIR__. "/../model/ModelBD.php";
    $id = $_GET['id_histo'];
    session_start();
    ModelBD::ajoutaction($_SESSION['user']['id'], "Suppression historique", "Suppression de l'historique : ".$id);
    self::compte();
}

public static function creermeteo(){
    session_start();
    self::afficheVue("view.php", ["pagetitle" => "Créer météo", "cheminVueBody" => "creermeteo.php", "message" => '']);
}

public static function meteocreate(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    $nom = $_GET['nom'];
    $def = $_GET['definition'];
    $id = $_SESSION['user']['id'];
        ModelBD::createmeteo($nom, $def, $id);
        ModelBD::ajoutaction($id, "Création météoteque", "Création de la météoteque : $nom");
        self::afficheVue("view.php", ["pagetitle" => "Créer météo", "cheminVueBody" => "redirectioncompte.php"]);
}
public static function ajoutmeteo(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    self::afficheVue("view.php", ["pagetitle" => "Ajouter météo", "cheminVueBody" => "ajoutmeteo.php", "message" => '']);
}
public static function ajouter(){
    require __DIR__. "/../model/ModelBD.php";
    session_start();
    $id_meteo = $_GET['id_meteo'];
   $villes = $_GET['villes']?? '';
   $date = $_GET['date'];
   $filtre = array (
   'temperature' => $_GET['temperature']?? "",
   'vitesse_vent'=> $_GET['vitesse_du_vent']?? "",
   'direction_vent' => $_GET['direction_du_vent']?? "",
   'humidite' => $_GET['humidite']?? "",
   'precipitations' => $_GET['precipitations_sur_24_heures']?? "",
   'phenomene_speciaux' => $_GET['phenomene_speciaux']?? "",
   );
   $villes =  strtoupper($villes);
   $vowels = array(" ");
   $villes = str_replace($vowels, "%20", $villes);
   $res = ModelBD::recherche($villes, $date);

$i = 0;
$temp = 0;
$tempMax = null;
$tempMin = null;
$vent = 0;
$dvent = 0;
$hum = 0;
$precip = 0;

foreach ($res as $r) {
    $i++;
    
    // Calcul de la température moyenne, maximale et minimale
    if ($filtre['temperature'] != '') {
        $temp += $r['tc']; // Addition des températures
        $tempMax = ($tempMax === null || $r['tc'] > $tempMax) ? $r['tc'] : $tempMax; // Mise à jour du max
        $tempMin = ($tempMin === null || $r['tc'] < $tempMin) ? $r['tc'] : $tempMin; // Mise à jour du min
    }
    
    // Calcul de la vitesse du vent moyenne
    if ($filtre['vitesse_vent'] != '') {
        $vent += $r['ff'];
    }
    
    // Calcul de la direction du vent moyenne
    if ($filtre['direction_vent'] != '') {
        $dvent += $r['dd'];
    }
    
    // Calcul de l'humidité moyenne
    if ($filtre['humidite'] != '') {
        $hum += $r['u'];
    }
    
    // Calcul des précipitations moyennes
    if ($filtre['precipitations'] != '') {
        $precip += $r['rr24'];
    }
    if ($filtre['phenomene_speciaux'] != '') {
        $phe = $r['phenspe1'];
    }

}

if ($i == 0) {
    self::afficheVue("view.php", [
        "pagetitle" => "Ajouter météo", 
        "cheminVueBody" => "ajoutmeteo.php", 
        "message" => "Aucune météo ne correspond à vos critères de recherche"
    ]);
} else {
    // Calcul des moyennes
    $temp = $temp / $i;
    $vent = $vent / $i;
    $dvent = $dvent / $i;
    $hum = $hum / $i;
    $precip = $precip / $i;
    ModelBD::ajouter($id_meteo, $villes,$date, $temp, $tempMax, $tempMin, $vent, $dvent, $hum, $precip,$phe);
    ModelBD::ajoutaction($_SESSION['user']['id'], "Ajout donnée dans météoteque", "Ajout de la donnée dans la météoteque : $id_meteo");
    self::afficheVue("view.php", ["pagetitle" => "Ajouter météo", "cheminVueBody" => "redirectioncompte.php",'filtre' => $filtre]);
}

}
public static function deletemeteo(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    $id_meteo = $_GET['id_meteo'];
    ModelBD::deletemeteo($id_meteo);
    ModelBD::ajoutaction($_SESSION['user']['id'], "Suppression météoteque", "Suppression de la météoteque : $id_meteo");
    self::compte();
}

public static function deletedonne(){
    require_once __DIR__. "/../model/ModelBD.php";
    session_start();
    $id_donne = $_GET['id_donne'];
    ModelBD::deletedonne($id_donne);
    ModelBD::ajoutaction($_SESSION['user']['id'], "Suppression donnée dans météoteque", "Suppression de la donnée dans la météoteque : $id_donne");
    self::compte();
}


public static function consultation(){
    session_start();
    self::afficheVue("view.php", ["pagetitle" => "Consultation", "cheminVueBody" => "consultation.php"]);
}

public static function station(){
    session_start();
    self::afficheVue("view.php", ["pagetitle" => "Stations", "cheminVueBody" => "station.php"]);
}

public static function meteoteque(){
    session_start();
    self::afficheVue("view.php", ["pagetitle" => "Météoteque", "cheminVueBody" => "meteoteque.php"]);
}

}
?>  