<?php

class CarritoModel {
    private $mysqli;

    public function __construct($mysqli_conn) {
        $this->mysqli = $mysqli_conn;
    }

    /**
     * Añade un artículo al carrito.
     * @param string $sessionId El ID de sesión del usuario.
     * @param string $referenciaProducto Código de referencia del producto.
     * @param bool $esMolde True si el artículo es una moldura, false en caso contrario.
     * @param string $descripcionProducto Descripción del producto.
     * @param float $cantidad Cantidad.
     * @param float $precioUnitario Precio por unidad.
     * @param float $totalLinea Precio total para este artículo (cantidad * precioUnitario).
     * @return bool True si tiene éxito, false en caso de error.
     */
    public function addItem($sessionId, $referenciaProducto, $esMolde, $descripcionProducto, $cantidad, $precioUnitario, $totalLinea) {
        $sql = "INSERT INTO carro (id_cliente_session, referencia_producto, es_molde, descripcion_producto, cantidad, precio_unitario, total_linea, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        if ($stmt = $this->mysqli->prepare($sql)) {
            // Convierte el booleano $esMolde a entero (0 o 1) para la base de datos
            $esMoldeInt = $esMolde ? 1 : 0;

            // Asocia los parámetros: s=string, i=integer, d=double
            $stmt->bind_param("ssisddd", $sessionId, $referenciaProducto, $esMoldeInt, $descripcionProducto, $cantidad, $precioUnitario, $totalLinea);

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                error_log("Error al ejecutar la sentencia addItem: " . $stmt->error . " SQL: " . $sql);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error al preparar la sentencia addItem: " . $this->mysqli->error . " SQL: " . $sql);
            return false;
        }
    }

    /**
     * Recupera todos los artículos del carrito para un ID de sesión dado.
     * @param string $sessionId
     * @return array Array de artículos del carrito, o array vacío si no hay/error.
     */
    public function getItems($sessionId) {
        $items = [];
        $sql = "SELECT id_carro, referencia_producto, es_molde, descripcion_producto, cantidad, precio_unitario, total_linea
                FROM carro WHERE id_cliente_session = ? ORDER BY fecha_creacion ASC";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("s", $sessionId);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    // Convierte es_molde de nuevo a booleano si es necesario, o lo deja como int
                    $row['es_molde'] = (bool)$row['es_molde'];
                    $items[] = $row;
                }
                $result->free();
            } else {
                error_log("Error al ejecutar la sentencia getItems: " . $stmt->error . " SQL: " . $sql);
            }
            $stmt->close();
        } else {
            error_log("Error al preparar la sentencia getItems: " . $this->mysqli->error . " SQL: " . $sql);
        }
        return $items;
    }

    /**
     * Actualiza la cantidad y el total_linea de un artículo específico del carrito.
     * @param int $idCarroItem El ID del artículo en el carrito (id_carro).
     * @param float $nuevaCantidad La nueva cantidad.
     * @param float $nuevoTotalLinea El nuevo total para este artículo.
     * @return bool True si tiene éxito, false en caso de error.
     */
    public function updateItemQuantity($idCarroItem, $nuevaCantidad, $nuevoTotalLinea) {
        $sql = "UPDATE carro SET cantidad = ?, total_linea = ? WHERE id_carro = ?";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("ddi", $nuevaCantidad, $nuevoTotalLinea, $idCarroItem);

            if ($stmt->execute()) {
                $success = $stmt->affected_rows > 0;
                $stmt->close();
                return $success;
            } else {
                error_log("Error al ejecutar la sentencia updateItemQuantity: " . $stmt->error . " SQL: " . $sql);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error al preparar la sentencia updateItemQuantity: " . $this->mysqli->error . " SQL: " . $sql);
            return false;
        }
    }

    /**
     * Elimina un artículo del carrito.
     * @param int $idCarroItem El ID del artículo en el carrito (id_carro).
     * @return bool True si tiene éxito, false en caso de error.
     */
    public function removeItem($idCarroItem) {
        $sql = "DELETE FROM carro WHERE id_carro = ?";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("i", $idCarroItem);

            if ($stmt->execute()) {
                $success = $stmt->affected_rows > 0;
                $stmt->close();
                return $success;
            } else {
                error_log("Error al ejecutar la sentencia removeItem: " . $stmt->error . " SQL: " . $sql);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error al preparar la sentencia removeItem: " . $this->mysqli->error . " SQL: " . $sql);
            return false;
        }
    }

    /**
     * Vacía todos los artículos del carrito para un ID de sesión dado.
     * @param string $sessionId
     * @return bool True si tiene éxito, false en caso de error (o si no se afectaron filas).
     */
    public function clearCart($sessionId) {
        $sql = "DELETE FROM carro WHERE id_cliente_session = ?";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("s", $sessionId);

            if ($stmt->execute()) {
                // affected_rows puede ser 0 si el carrito ya estaba vacío,
                // pero la operación en sí fue exitosa.
                // Por simplicidad, true si execute tuvo éxito.
                $stmt->close();
                return true;
            } else {
                error_log("Error al ejecutar la sentencia clearCart: " . $stmt->error . " SQL: " . $sql);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error al preparar la sentencia clearCart: " . $this->mysqli->error . " SQL: " . $sql);
            return false;
        }
    }

    /**
     * Obtiene el número total de piezas en el carrito para un ID de sesión dado.
     * @param string $sessionId
     * @return int Número total de piezas en el carrito.
     */
    public function getItemCount($sessionId) {
        $total_items = 0;
        $sql = "SELECT SUM(cantidad) as total_items FROM carro WHERE id_cliente_session = ?";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("s", $sessionId);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $total_items = (int)($row['total_items'] ?? 0);
                }
                $result->free();
            } else {
                error_log("Error al ejecutar la sentencia getItemCount: " . $stmt->error . " SQL: " . $sql);
            }
            $stmt->close();
        } else {
            error_log("Error al preparar la sentencia getItemCount: " . $this->mysqli->error . " SQL: " . $sql);
        }
        return $total_items;
    }

    /**
     * Recupera los detalles de un solo artículo del carrito, asegurando que pertenezca a la sesión.
     * @param int $idCarroItem El ID del artículo en el carrito.
     * @param string $sessionId El ID de sesión del usuario para validación.
     * @return array|null Detalles del artículo como array asociativo, o null si no se encuentra o no pertenece a la sesión.
     */
    public function getSingleItemDetails($idCarroItem, $sessionId) {
        $item = null;
        $sql = "SELECT id_carro, referencia_producto, es_molde, descripcion_producto, cantidad, precio_unitario, total_linea
                FROM carro WHERE id_carro = ? AND id_cliente_session = ?";

        if ($stmt = $this->mysqli->prepare($sql)) {
            $stmt->bind_param("is", $idCarroItem, $sessionId);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $row['es_molde'] = (bool)$row['es_molde'];
                    $item = $row;
                }
                $result->free();
            } else {
                error_log("Error al ejecutar la sentencia getSingleItemDetails: " . $stmt->error . " SQL: " . $sql);
            }
            $stmt->close();
        } else {
            error_log("Error al preparar la sentencia getSingleItemDetails: " . $this->mysqli->error . " SQL: " . $sql);
        }
        return $item;
    }
}
?>