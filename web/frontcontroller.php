<?php
require __DIR__ . '/../src/controller/Controller.php';
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'Controller';
// Action par défaut si non spécifiée
$action = isset($_GET['action']) ? $_GET['action'] : 'acceuil';
// Construction du nom complet de la classe
$controllerClassName = "../src/controller/" . ucfirst($controller);
// Vérification si la classe existe
if (class_exists($controller)) {
    // Vérifier si la méthode demandée existe dans la classe
    if (method_exists($controller, $action)) {
        // Appeler dynamiquement la méthode
        $controller::$action();
    } else {
        // Action non reconnue : appeler l'erreur de la classe actuelle
        $controller::error("Action '$action' non reconnue dans le contrôleur $controller");
    }
} else {
    // Contrôleur non reconnu : appeler l'erreur du contrôleur par défaut
    $Controller::$action();
}
?>
