<?php
session_start();

// Define una constante de ruta base si la necesitas para includes/enlaces más adelante, por ejemplo: define('BASE_URL', '/ruta_a_tu_proyecto/public/');
// Por ahora, rutas relativas desde aquí.

require_once '../includes/conexion.php'; // Para $mysqli
require_once '../app/core/Autoloader.php';

// Controlador y acción por defecto
$controllerName = isset($_GET['controller']) ? ucfirst(trim($_GET['controller'])) . 'Controller' : 'DefaultController'; // Ejemplo: HomeController
$actionName = isset($_GET['action']) ? trim($_GET['action']) : 'index';

// Saneamiento básico de entrada para el nombre del controlador
// Solo permite caracteres alfanuméricos y guion bajo para los nombres de los controladores
// Esto previene ataques de recorrido de directorios y asegura nombres de clase válidos.
if (!preg_match('/^[a-zA-Z0-9_]+$/', $controllerName)) {
    // Registrar el intento para revisión de seguridad
    error_log("Nombre de controlador inválido solicitado: " . $_GET['controller']);
    die('Nombre de controlador inválido');
}

$controllerFile = '../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    // El autoloader debería encargarse de esto si el nombre de la clase coincide con el nombre del archivo.
    // require_once $controllerFile; // Esta línea puede comentarse si el autoloader es confiable.
    // Sin embargo, un require_once explícito puede ser más claro para el controlador principal.
    // Para este ejercicio, asumimos que el autoloader lo gestiona.

    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName($mysqli); // Pasa $mysqli al constructor

        if (method_exists($controllerInstance, $actionName)) {
            // Saneamiento básico de entrada para el nombre de la acción (opcional, pero buena práctica)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $actionName)) {
                error_log("Nombre de acción inválido solicitado: " . $_GET['action'] . " para el controlador " . $controllerName);
                die('Nombre de acción inválido');
            }
            $controllerInstance->$actionName();
        } else {
            error_log("Acción '{$actionName}' no encontrada en el controlador '{$controllerName}'");
            // Manejar 404 - acción no encontrada
            header("HTTP/1.0 404 Not Found");
            echo "Error 404: Acci&oacute;n '".$actionName."' no encontrada en el controlador '". $controllerName ."'.";
        }
    } else {
        error_log("Clase del controlador '{$controllerName}' no encontrada en el archivo '{$controllerFile}' (pero el archivo existe). Puede haber un problema con el autoloader o un desajuste en el nombre de la clase.");
        // Manejar 404 - clase del controlador no encontrada
        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Clase del controlador '".$controllerName."' no encontrada.";
    }
} else {
    error_log("Archivo del controlador '{$controllerFile}' no encontrado para el controlador '{$controllerName}'");
    // Manejar 404 - archivo del controlador no encontrado
    if ($controllerName === 'DefaultController' && $actionName === 'index') {
        // Alternativa para el acceso raíz (por ejemplo, navegando a /public/ o /public/index.php sin parámetros)
        // echo "Bienvenido a la p&aacute;gina principal (desde public/index.php)";
        // Intentar cargar el antiguo index.php raíz como alternativa por ahora.
        // Esto es temporal hasta que se cree un HomeController adecuado para manejar la raíz.
        // O mostrar una página de inicio genérica.
        // Por ahora, redirigir al antiguo index.php podría causar un bucle si también intenta redirigir.
        // Asumimos que se creará DefaultController o esta lógica será reemplazada por una página de inicio adecuada.
        echo "P&aacute;gina principal. (DefaultController no implementado)";
        // header('Location: ../index.php'); // Evitar posibles bucles de redirección al antiguo index.php
        // exit;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Controlador '".$controllerName."' no encontrado.";
    }
}
?>
if (!preg_match('/^[a-zA-Z0-9_]+$/', $controllerName)) {
// Log the attempt for security review
error_log("Invalid controller name requested: " . $_GET['controller']);
die('Invalid controller name');
}

$controllerFile = '../app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
// Autoloader should handle this if class name matches file name.
// require_once $controllerFile; // This line can be commented out if autoloader is reliable.
// However, explicit require_once can be clearer for the main controller.
// For this exercise, let's assume autoloader handles it.

if (class_exists($controllerName)) {
$controllerInstance = new $controllerName($mysqli); // Pass $mysqli to constructor

if (method_exists($controllerInstance, $actionName)) {
// Basic input sanitization for action name (optional, but good practice)
if (!preg_match('/^[a-zA-Z0-9_]+$/', $actionName)) {
error_log("Invalid action name requested: " . $_GET['action'] . " for controller " . $controllerName);
die('Invalid action name');
}
$controllerInstance->$actionName();
} else {
error_log("Action '{$actionName}' not found in controller '{$controllerName}'");
// Handle 404 - action not found
header("HTTP/1.0 404 Not Found");
echo "Error 404: Acci&oacute;n '".$actionName."' no encontrada en el controlador '". $controllerName ."'.";
}
} else {
error_log("Controller class '{$controllerName}' not found in file '{$controllerFile}' (but file exists). Autoloader might have an issue or class name mismatch.");
// Handle 404 - controller class not found
header("HTTP/1.0 404 Not Found");
echo "Error 404: Clase del controlador '".$controllerName."' no encontrada.";
}
} else {
error_log("Controller file '{$controllerFile}' not found for controller '{$controllerName}'");
// Handle 404 - controller file not found
if ($controllerName === 'DefaultController' && $actionName === 'index') {
// Fallback for the very root access (e.g., navigating to /public/ or /public/index.php without params)
// echo "Bienvenido a la p&aacute;gina principal (desde public/index.php)";
// Attempt to load the old root index.php as a fallback for now.
// This is temporary until a proper HomeController is made to handle the root.
// Or, display a generic homepage.
// For now, redirecting to the old root index.php might cause a loop if it also tries to redirect.
// Let's assume DefaultController will be created or this logic will be replaced by a proper landing page.
echo "P&aacute;gina principal. (DefaultController no implementado)";
// header('Location: ../index.php'); // Avoid potential redirect loops to old index.php
// exit;
} else {
header("HTTP/1.0 404 Not Found");
echo "Error 404: Controlador '".$controllerName."' no encontrado.";
}
}
?>
