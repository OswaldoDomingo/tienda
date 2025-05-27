<?php
require_once("../includes/conexion.php"); // Use the central mysqli connection

// Ensure $mysqli is available
if (!isset($mysqli) || $mysqli->connect_errno) {
        error_log("MySQLi Connection Error in modi_molduras.php: " . (isset($mysqli->connect_error) ? $mysqli->connect_error : 'Unknown error'));
    // Redirect or display a user-friendly error message
    // For now, just exit, but a real app might redirect to an error page
    exit("Error de conexi&oacute;n a la base de datos.");
}

if (isset($_POST['modificar'])) {
    // UPDATE Logic
    $id_referencia_pk_original = isset($_POST['id_referencia_pk_original']) ? (int)$_POST['id_referencia_pk_original'] : 0;

    $id_referencia_new = isset($_POST['id_referencia']) ? trim($_POST['id_referencia']) : '';
    $clave_referencia_new = isset($_POST['referencia']) ? trim($_POST['referencia']) : null;
    $precio_new = isset($_POST['precio']) ? (float)$_POST['precio'] : 0.0;
    $tipo_id_new = isset($_POST['tipo_id']) ? (int)$_POST['tipo_id'] : null;
    $modelo_id_new = isset($_POST['modelo_id']) ? (int)$_POST['modelo_id'] : null;
    $color_id_new = isset($_POST['color_id']) ? (int)$_POST['color_id'] : null;
    $clase_id_new = isset($_POST['clase_id']) ? (int)$_POST['clase_id'] : null;
    $color_texto_new = isset($_POST['color']) ? trim($_POST['color']) : null; // 'color' from form maps to 'color_texto'
    $descuento_new = isset($_POST['descuento']) ? (float)$_POST['descuento'] : 0.0;
    $almacen_id_new = isset($_POST['almacen_id']) ? (int)$_POST['almacen_id'] : null;
    $fecha_creacion_new = isset($_POST['fecha']) ? trim($_POST['fecha']) : date('Y-m-d H:i:s'); // 'fecha' from form maps to 'fecha_creacion'

    if ($id_referencia_pk_original > 0 && !empty($id_referencia_new)) {
        $sql = "UPDATE referencia SET 
                    id_referencia = ?, 
                    clave_referencia = ?, 
                    precio = ?, 
                    tipo_id = ?, 
                    modelo_id = ?, 
                    color_id = ?, 
                    clase_id = ?, 
                    color_texto = ?, 
                    descuento = ?, 
                    almacen_id = ?, 
                    fecha_creacion = ? 
                WHERE id_referencia_pk = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssdiidsisdsi",
                $id_referencia_new,
                $clave_referencia_new,
                $precio_new,
                $tipo_id_new,
                $modelo_id_new,
                $color_id_new,
                $clase_id_new,
                $color_texto_new,
                $descuento_new,
                $almacen_id_new,
                $fecha_creacion_new,
                $id_referencia_pk_original
            );

            if (!$stmt->execute()) {
                error_log("Error executing UPDATE statement (modi_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            error_log("Error preparing UPDATE statement (modi_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        error_log("Invalid input for UPDATE in modi_molduras.php. id_referencia_pk_original: $id_referencia_pk_original, id_referencia_new: $id_referencia_new");
    }

} elseif (isset($_POST['borrar'])) {
    // DELETE Logic
    $id_referencia_pk_original = isset($_POST['id_referencia_pk_original']) ? (int)$_POST['id_referencia_pk_original'] : 0;

    if ($id_referencia_pk_original > 0) {
        $sql = "DELETE FROM referencia WHERE id_referencia_pk = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $id_referencia_pk_original);

            if (!$stmt->execute()) {
                error_log("Error executing DELETE statement (modi_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            error_log("Error preparing DELETE statement (modi_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        error_log("Invalid id_referencia_pk_original for DELETE in modi_molduras.php: $id_referencia_pk_original");
    }
}

header('Location: borrar_molduras.php');
exit; // Ensure no further code execution after redirect
?>