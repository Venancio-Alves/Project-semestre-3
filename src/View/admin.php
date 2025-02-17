<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <style>
        /* Style général */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #f4f4f9, #e0e7ff);
            color: #333;
        }

        /* Conteneur principal */
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Tableau des données */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #2563eb;
            color: #ffffff;
        }

        table td {
            background-color: #f9fafb;
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

        /* Boutons */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #2563eb;
            color: #ffffff;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
        }

        .btn:hover {
            background: #1e3a8a;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-container label {
            font-weight: bold;
            margin-right: 10px;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="submit"] {
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .form-container input[type="submit"] {
            background-color: #2563eb;
            color: #ffffff;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Utilisateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID utilisateur</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once __DIR__ . "/../Model/ModelBD.php";
                foreach ($utils as $util) {
                    if ($util["id_utilisateur"] != $_SESSION["user"]["id"]) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($util["id_utilisateur"]) . "</td>";
                        echo "<td>" . htmlspecialchars($util["nom_utilisateur"]) . "</td>";
                        echo "<td>" . htmlspecialchars($util["prenom_utilisateur"]) . "</td>";
                        echo "<td>
                            <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=delete&id=" . htmlspecialchars($util["id_utilisateur"]) . "'>Supprimer</a> | 
                            <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=adminupdate&id=" . htmlspecialchars($util["id_utilisateur"]) . "'>Modifier</a>
                        </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <hr />

        <h2>Utilisateurs en attente</h2>
        <table>
            <thead>
                <tr>
                    <th>ID utilisateur</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($utils as $util) {
                    if (ModelBD::getRole($util["id_utilisateur"]) == 'attente') {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($util["id_utilisateur"]) . "</td>";
                        echo "<td>" . htmlspecialchars($util["nom_utilisateur"]) . "</td>";
                        echo "<td>" . htmlspecialchars($util["prenom_utilisateur"]) . "</td>";
                        echo "<td>
                            <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=accepter&id=" . htmlspecialchars($util["id_utilisateur"]) . "'>Accepter</a> | 
                            <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=refuser&id=" . htmlspecialchars($util["id_utilisateur"]) . "'>Refuser</a>
                        </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <hr />

        <h2>Actions</h2>
        
<?php
// Vérifier si aucune action n'est disponible
if ($actions == 'pas encore d\'actions') {
    echo $actions;
} else {
    // Vérifier si un utilisateur existe dans les paramètres et dans la base de données
    $id_utilisateur = $_GET["id_utilisateur"] ?? '';
    $action_type = $_GET["act"] ?? '';
    $user_exists = ModelBD::exist($id_utilisateur);

    foreach ($actions as $action) {
        // Filtrer par utilisateur si l'utilisateur existe et est spécifié
        if ($user_exists && $id_utilisateur !== '' && $action['id_utilisateur'] != $id_utilisateur) {
            continue;
        }

        // Filtrer par type d'action si spécifié
        if ($action_type !== '' && $action['action'] != $action_type) {
            continue;
        }

        // Afficher l'action correspondante
        echo "<p>id utilisateur : " . htmlspecialchars($action["id_utilisateur"]) .
             " | action : " . htmlspecialchars($action["action"]) .
             " | details action : " . htmlspecialchars($action["details_action"]) .
             " | date : " . htmlspecialchars($action["date"]) .
             " <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=deleteaction&id=" . htmlspecialchars($action["id_action"]) . "'>supprimer</a></p>";
    }
}
?>

        
        <br />
        <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=suptout' class="btn">Supprimer toutes les actions</a>

        <hr />

        <h2>Filtrer les actions</h2>
        <form method="GET" class="form-container">
            <input type="hidden" name="action" value="admin">
            <label for="id_utilisateur">ID utilisateur :</label>
            <input type="number" name="id_utilisateur" id="id_utilisateur" class="form-control">
            <label for="action">Action :</label>
            <input list="actions" name="act" class="form-control">
            <input type="submit" value="Filtrer" class="btn">
        </form>
        <datalist id="actions">
            <option value="Connexion"></option>
            <option value="Déconnexion"></option>
            <option value="Modification"></option>
            <option value="Créer_un_compte"></option>
            <option value="Recherche"></option>
            <option value="Suppression admin"></option>
            <option value="Modification admin"></option>
            <option value="Création admin"></option>
            <option value="Création météoteque"></option>
            <option value="Ajout donnée dans météoteque"></option>
            <option value="Suppression météoteque"></option>
            <option value="Suppression donnée dans météoteque"></option>

        </datalist>
        <br />
        <a href='http://localhost/sae/web/frontcontroller.php?controller=controller&action=creeradmin' class="btn">Ajouter un utilisateur</a>
    </div>
</body>
</html>
