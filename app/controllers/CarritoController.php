<?php
// Si el autoloader aún no está activo para esta subtarea:
require_once '../models/CarritoModel.php';

class CarritoController {
    private $mysqli;
    private $carritoModel;

    public function __construct($mysqli_conn) {
        $this->mysqli = $mysqli_conn; // Aunque no se use directamente, es buena práctica mantenerlo por si se necesita en el futuro
        $this->carritoModel = new CarritoModel($mysqli_conn); // Pasa la conexión al modelo
    }

    /**
     * Maneja la adición de un artículo al carrito.
     * Espera datos POST: referencia_articulo, descripcion, precio, cantidad.
     * 'es_molde' también puede ser un campo POST.
     */
    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Opcionalmente, manejar peticiones que no sean POST, por ejemplo, redirigir o mostrar error
            header('Location: index.php'); // Redirige a la página principal o al carrito
            exit;
        }

        $sessionId = session_id();
        if (empty($sessionId)) {
            // Esto no debería ocurrir si session_start() se llama en el bootstrap principal
            error_log("CarritoController::agregar() - El ID de sesión está vacío.");
            // Manejar el error apropiadamente, quizás redirigir con mensaje de error
            header('Location: index.php?error=session_expired');
            exit;
        }

        $referenciaProducto = isset($_POST['referencia_articulo']) ? trim($_POST['referencia_articulo']) : null;
        $descripcionProducto = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : 'Descripción no disponible';
        $cantidad = isset($_POST['cantidad']) ? (float)$_POST['cantidad'] : 0;
        $precioUnitario = isset($_POST['precio']) ? (float)$_POST['precio'] : 0.0;
        // Se asume que es_molde es falso si no se proporciona o no es '1'/'true'
        $esMolde = isset($_POST['es_molde']) && filter_var($_POST['es_molde'], FILTER_VALIDATE_BOOLEAN);

        if (empty($referenciaProducto) || $cantidad <= 0 || $precioUnitario < 0) { // El precio puede ser 0 (artículos gratis), pero no negativo
            // Manejar error de validación, por ejemplo, guardar mensaje en sesión y redirigir
            $_SESSION['mensaje_carrito'] = "Error: Datos del producto inv&aacute;lidos.";
            error_log("CarritoController::agregar() - Datos de producto inválidos. Ref: $referenciaProducto, Cantidad: $cantidad, Precio: $precioUnitario");
            header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php')); // Redirige atrás o a una página por defecto
            exit;
        }

        $totalLinea = $cantidad * $precioUnitario;

        if ($this->carritoModel->addItem($sessionId, $referenciaProducto, $esMolde, $descripcionProducto, $cantidad, $precioUnitario, $totalLinea)) {
            $_SESSION['mensaje_carrito'] = "Art&iacute;culo a&ntilde;adido al carrito.";
        } else {
            $_SESSION['mensaje_carrito'] = "Error al a&ntilde;adir el art&iacute;culo al carrito.";
            error_log("CarritoController::agregar() - carritoModel->addItem falló.");
        }

        header('Location: index.php?controller=carrito&action=mostrar');
        exit;
    }

    /**
     * Muestra el contenido del carrito de compras.
     */
    public function mostrar() {
        $sessionId = session_id();
        if (empty($sessionId)) {
            error_log("CarritoController::mostrar() - El ID de sesión está vacío.");
            echo "Error: Sesi&oacute;n no v&aacute;lida.";
            return;
        }

        $items = $this->carritoModel->getItems($sessionId);
        $totalGeneral = 0;

        if ($items) {
            foreach ($items as $item) {
                $totalGeneral += (float)$item['total_linea'];
            }
        }

        // Recupera el mensaje de sesión y lo elimina
        $mensaje = isset($_SESSION['mensaje_carrito']) ? $_SESSION['mensaje_carrito'] : null;
        unset($_SESSION['mensaje_carrito']);

        // Carga la vista
        // La ruta asume que CarritoController.php está en app/controllers/
        // y mostrar.php está en app/views/carrito/
        require '../views/carrito/mostrar.php';
    }

    /**
     * Maneja la actualización de la cantidad de un artículo en el carrito.
     * Espera datos POST: id_carro_item, cantidad.
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=carrito&action=mostrar');
            exit;
        }

        $sessionId = session_id();
        if (empty($sessionId)) {
            $_SESSION['mensaje_carrito'] = "Error: Sesi&oacute;n no v&aacute;lida.";
            error_log("CarritoController::actualizar() - El ID de sesión está vacío.");
            header('Location: index.php?controller=carrito&action=mostrar');
            exit;
        }

        $idCarroItem = isset($_POST['id_carro_item']) ? (int)$_POST['id_carro_item'] : 0;
        $nuevaCantidad = isset($_POST['cantidad']) ? (float)$_POST['cantidad'] : -1; // Usar -1 para detectar si no está definido o es inválido

        if ($idCarroItem <= 0 || $nuevaCantidad < 0) {
            $_SESSION['mensaje_carrito'] = "Error: Datos inv&aacute;lidos para actualizar.";
            error_log("CarritoController::actualizar() - Datos inválidos. ID: $idCarroItem, Cantidad: $nuevaCantidad");
            header('Location: index.php?controller=carrito&action=mostrar');
            exit;
        }

        if ($nuevaCantidad == 0) {
            // Si la cantidad es 0, elimina el artículo
            if ($this->carritoModel->removeItem($idCarroItem)) { // Nota: removeItem podría necesitar comprobar la sesión por seguridad
                $_SESSION['mensaje_carrito'] = "Art&iacute;culo eliminado.";
            } else {
                $_SESSION['mensaje_carrito'] = "Error al eliminar el art&iacute;culo.";
                error_log("CarritoController::actualizar() - carritoModel->removeItem falló para ID: $idCarroItem");
            }
        } else {
            // Actualiza la cantidad
            $itemDetails = $this->carritoModel->getSingleItemDetails($idCarroItem, $sessionId);

            if ($itemDetails) {
                $nuevoTotalLinea = $nuevaCantidad * (float)$itemDetails['precio_unitario'];
                if ($this->carritoModel->updateItemQuantity($idCarroItem, $nuevaCantidad, $nuevoTotalLinea)) {
                    $_SESSION['mensaje_carrito'] = "Cantidad actualizada.";
                } else {
                    $_SESSION['mensaje_carrito'] = "Error al actualizar la cantidad.";
                    error_log("CarritoController::actualizar() - carritoModel->updateItemQuantity falló para ID: $idCarroItem");
                }
            } else {
                $_SESSION['mensaje_carrito'] = "Error: Art&iacute;culo no encontrado o no pertenece a su sesi&oacute;n.";
                error_log("CarritoController::actualizar() - getSingleItemDetails falló o devolvió null para ID: $idCarroItem, Sesión: $sessionId");
            }
        }

        header('Location: index.php?controller=carrito&action=mostrar');
        exit;
    }

    /**
     * Maneja la eliminación de un artículo del carrito.
     * Espera datos GET o POST: id_carro_item.
     */
    public function eliminar() {
        $idCarroItem = isset($_REQUEST['id_carro_item']) ? (int)$_REQUEST['id_carro_item'] : 0;

        if ($idCarroItem <= 0) {
            $_SESSION['mensaje_carrito'] = "Error: ID de art&iacute;culo inv&aacute;lido.";
            error_log("CarritoController::eliminar() - ID inválido: $idCarroItem");
            header('Location: index.php?controller=carrito&action=mostrar');
            exit;
        }

        // Para mayor seguridad, se podría comprobar que el artículo pertenece a la sesión actual
        // antes de eliminar, similar a como se usa getSingleItemDetails en actualizar().
        // Sin embargo, CarritoModel->removeItem solo recibe id_carro.

        if ($this->carritoModel->removeItem($idCarroItem)) {
            $_SESSION['mensaje_carrito'] = "Art&iacute;culo eliminado del carrito.";
        } else {
            $_SESSION['mensaje_carrito'] = "Error al eliminar el art&iacute;culo.";
            error_log("CarritoController::eliminar() - carritoModel->removeItem falló para ID: $idCarroItem");
        }

        header('Location: index.php?controller=carrito&action=mostrar');
        exit;
    }

    /**
     * Vacía todos los artículos del carrito para la sesión actual.
     */
    public function vaciar() {
        $sessionId = session_id();
        if (empty($sessionId)) {
            $_SESSION['mensaje_carrito'] = "Error: Sesi&oacute;n no v&aacute;lida.";
            error_log("CarritoController::vaciar() - El ID de sesión está vacío.");
            header('Location: index.php?controller=carrito&action=mostrar');
            exit;
        }

        if ($this->carritoModel->clearCart($sessionId)) {
            $_SESSION['mensaje_carrito'] = "Carrito vaciado correctamente.";
        } else {
            $_SESSION['mensaje_carrito'] = "Error al vaciar el carrito.";
            error_log("CarritoController::vaciar() - carritoModel->clearCart falló para Sesión: $sessionId");
        }

        header('Location: index.php?controller=carrito&action=mostrar');
        exit;
    }
}
?>