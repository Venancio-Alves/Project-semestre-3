
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MétéoApp - Consultation des Données Climatiques</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #6dd5fa, #2980b9);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .header {
        background-color: transparent;
        /* Supprimer la couleur pour mieux s’intégrer */
        backdrop-filter: none;
        /* Supprimer le flou */
        padding: 20px 0;
        text-align: center;
        box-shadow: none;
        /* Supprimer l’ombre */
        margin-bottom: 20px;
        /* Ajoute un espace sous le logo */
    }

    .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 2.5em;
        font-weight: 600;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .logo i {
        margin-right: 10px;
        font-size: 1.5em;
    }

    .weather-card {
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .weather-card:hover {
        transform: translateY(-5px);
    }

    h1 {
        color: #34495e;
        margin-bottom: 30px;
        font-size: 1.8em;
        font-weight: 600;
    }

    .weather-icon {
        font-size: 6em;
        color: #f39c12;
        margin-bottom: 30px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    p {
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 30px;
        color: #555;
    }

    .btn {
        display: inline-block;
        padding: 12px 30px;
        background-color: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 30px;
        transition: all 0.3s ease;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn:hover {
        background-color: #2980b9;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
		
    }
	
    </style>
</head>


<body>
    <!-- En-tête centré -->
    <div class="header">
        <div class="logo">
            <i class="fas fa-temperature-high"></i>
            MétéoApp
        </div>
    </div>

    <!-- Carte au centre -->
    <div class="weather-card">
        <h1>Consultation des Données Climatiques</h1>
        <div class="weather-icon">
            <i class="fas fa-cloud-sun-rain"></i>
        </div>
        <?php
        require_once __DIR__ . '/../Model/ModelBD.php';
        if (ModelBD::getrole($_SESSION['user']['id'] ?? '') == 'attente') {
            echo "<p>Votre demande d'inscription est en cours de traitement. Nous vous remercions pour votre patience.</p>";
			echo "<p>Vous pouvez accèder aux données des stations.</p>";
            echo "<a href='./frontcontroller.php?controller=controller&action=station' class='btn'>Stations</a>";
        } 
        else if (ModelBD::getrole($_SESSION['user']['id'] ?? '') == 'admin') {
            echo "<p>Ravi de vous revoir.</p>";
            echo "<a href='./frontcontroller.php?controller=controller&action=admin' class='btn'>Administration</a>";
        } 
        else if (ModelBD::getrole($_SESSION['user']['id'] ?? '') == 'utilisateur') {
            echo "<p>Merci pour votre contribution. Vous pouvez effectuer de nouvelles recherches de données pour alimenter la météothèque.</p>";
            echo "<a href='./frontcontroller.php?controller=controller&action=pagerech' class='btn'>Recherche</a>";
        } 
		else {
            echo "<p>Nous sommes ravis de vous accueillir sur notre site. Découvrez nos activités et rejoignez notre communauté !</p>";
			echo "<p>Vous pouvez accèder aux données des stations.</p>";
            echo "<a href='./frontcontroller.php?controller=controller&action=station' class='btn'>Stations</a>";
        }
		
        ?>
    </div>
</body>

</html>
