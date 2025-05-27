<?php
//Conectar con la base de datos
require_once("../includes/conexion.php"); // Use the central mysqli connection

// Ensure $mysqli is available
if (!isset($mysqli) || $mysqli->connect_errno) {
    // Redirect or display a user-friendly error message
    // For now, simple echo, but consider a more robust error handling page
    echo "Error de conexi&oacute;n a la base de datos. Por favor, intente m&aacute;s tarde.";
    error_log("MySQLi Connection Error in bd_alta_molduras.php: " . ($mysqli->connect_error ?: 'Error desconocido'));
    exit; // Stop script execution if no DB connection
}

if (isset($_POST['alta_referencia'])) {
    // ALTA REFERENCIA DE MOLDURA - Modernized
    $id_referencia_form = isset($_POST['id_referencia']) ? trim($_POST['id_referencia']) : '';
    $clave_referencia_form = isset($_POST['clave']) ? trim($_POST['clave']) : null; // clave_referencia can be NULL
    $precio_form = isset($_POST['precio']) ? (float)$_POST['precio'] : 0.0;
    $tipo_id_form = isset($_POST['tipo_id']) ? (int)$_POST['tipo_id'] : null;
    $modelo_id_form = isset($_POST['modelo_id']) ? (int)$_POST['modelo_id'] : null;
    $color_id_form = isset($_POST['color_id']) ? (int)$_POST['color_id'] : null;
    $clase_id_form = isset($_POST['clase_id']) ? (int)$_POST['clase_id'] : null;
    // Assuming 'color_texto' from the form in alta_moldura.php (was 'color' originally)
    $color_texto_form = isset($_POST['color_texto']) ? trim($_POST['color_texto']) : null;
    $descuento_form = isset($_POST['descuento']) ? (float)$_POST['descuento'] : 0.0;
    $almacen_id_form = isset($_POST['almacen_id']) ? (int)$_POST['almacen_id'] : null;
    // Assuming 'fecha_creacion' from the form in alta_moldura.php (was 'fecha' originally)
    $fecha_creacion_form = isset($_POST['fecha_creacion']) ? trim($_POST['fecha_creacion']) : date('Y-m-d H:i:s');

    // Basic validation
    if (!empty($id_referencia_form) && $precio_form > 0 && $tipo_id_form !== null && $modelo_id_form !== null) {
        $sql = "INSERT INTO referencia (id_referencia, clave_referencia, precio, tipo_id, modelo_id, color_id, clase_id, color_texto, descuento, almacen_id, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters: s=string, d=double, i=integer
            // id_referencia (s), clave_referencia (s), precio (d), tipo_id (i), modelo_id (i), color_id (i), clase_id (i), color_texto (s), descuento (d), almacen_id (i), fecha_creacion (s)
            $stmt->bind_param("ssdiidsisds",
                $id_referencia_form,
                $clave_referencia_form,
                $precio_form,
                $tipo_id_form,
                $modelo_id_form,
                $color_id_form,
                $clase_id_form,
                $color_texto_form,
                $descuento_form,
                $almacen_id_form,
                $fecha_creacion_form
            );

            if ($stmt->execute()) {
                // Success message or logging
                // echo "Nueva referencia de moldura creada con ID: " . $stmt->insert_id; // $stmt->insert_id refers to id_referencia_pk (auto-increment)
            } else {
                // Error message or logging
                error_log("Error executing statement (alta_referencia bd_alta_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            // Error message or logging
            error_log("Error preparing statement (alta_referencia bd_alta_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        // Invalid input message or logging
        error_log("Invalid input for alta_referencia in bd_alta_molduras.php. id_referencia: $id_referencia_form, precio: $precio_form");
    }
}

// ALTA MODELO DE MOLDURA - Modernized
if (isset($_POST['alta_modelo'])) {
    // $_POST['id_modelo'] from form is used as nombre_modelo
    $nombre_modelo_form = isset($_POST['id_modelo']) ? trim($_POST['id_modelo']) : '';
    $ancho_modelo_form = isset($_POST['ancho_modelo']) ? (float)$_POST['ancho_modelo'] : 0.0;
    $alto_modelo_form = isset($_POST['alto_modelo']) ? (float)$_POST['alto_modelo'] : 0.0;
    $inglete_modelo_form = isset($_POST['inglete_modelo']) ? (float)$_POST['inglete_modelo'] : 0.0;
    $proveedor_modelo_form = isset($_POST['proveedor_modelo']) ? trim($_POST['proveedor_modelo']) : null;
    $kit_modelo_form = isset($_POST['kit_modelo']) ? trim($_POST['kit_modelo']) : null;

    if (!empty($nombre_modelo_form) && $ancho_modelo_form > 0 && $alto_modelo_form > 0 && $inglete_modelo_form > 0) {
        $sql = "INSERT INTO modelo (nombre_modelo, ancho_modelo, alto_modelo, inglete_modelo, proveedor_modelo, kit_modelo) 
                VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters: s=string, d=double
            $stmt->bind_param("sdddss",
                $nombre_modelo_form,
                $ancho_modelo_form,
                $alto_modelo_form,
                $inglete_modelo_form,
                $proveedor_modelo_form,
                $kit_modelo_form
            );

            if (!$stmt->execute()) {
                error_log("Error executing statement (alta_modelo bd_alta_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            error_log("Error preparing statement (alta_modelo bd_alta_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        error_log("Invalid input for alta_modelo in bd_alta_molduras.php. nombre_modelo: $nombre_modelo_form");
    }
}

// ALTA COLOR DE MOLDURA - Modernized
if (isset($_POST['alta_color'])) {
    $nombre_color_form = isset($_POST['nombre_color']) ? trim($_POST['nombre_color']) : '';
    // $_POST['id_color'] from form is ignored as id_color in DB is AUTO_INCREMENT

    if (!empty($nombre_color_form)) {
        $sql = "INSERT INTO color (nombre_color) VALUES (?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $nombre_color_form);
            if (!$stmt->execute()) {
                error_log("Error executing statement (alta_color bd_alta_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            error_log("Error preparing statement (alta_color bd_alta_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        error_log("Invalid input for alta_color in bd_alta_molduras.php. nombre_color is empty.");
    }
}

// ALTA CLASE DE MOLDURA - Modernized
if (isset($_POST['alta_clase'])) {
    $nombre_clase_form = isset($_POST['nombre_clase']) ? trim($_POST['nombre_clase']) : '';
    // $_POST['id_clase'] from form is ignored as id_clase in DB is AUTO_INCREMENT

    if (!empty($nombre_clase_form)) {
        $sql = "INSERT INTO clase (nombre_clase) VALUES (?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $nombre_clase_form);
            if (!$stmt->execute()) {
                error_log("Error executing statement (alta_clase bd_alta_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            error_log("Error preparing statement (alta_clase bd_alta_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        error_log("Invalid input for alta_clase in bd_alta_molduras.php. nombre_clase is empty.");
    }
}

// ALTA TIPO DE MOLDURA - Modernized
if (isset($_POST['alta_tipo'])) {
    $nombre_tipo_form = isset($_POST['nombre_tipo']) ? trim($_POST['nombre_tipo']) : '';
    // $_POST['id_tipo'] from form is ignored as id_tipo in DB is AUTO_INCREMENT

    if (!empty($nombre_tipo_form)) {
        $sql = "INSERT INTO tipo (nombre_tipo) VALUES (?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $nombre_tipo_form);
            if (!$stmt->execute()) {
                error_log("Error executing statement (alta_tipo bd_alta_molduras.php): " . $stmt->error . " SQL: $sql");
            }
            $stmt->close();
        } else {
            error_log("Error preparing statement (alta_tipo bd_alta_molduras.php): " . $mysqli->error . " SQL: $sql");
        }
    } else {
        error_log("Invalid input for alta_tipo in bd_alta_molduras.php. nombre_tipo is empty.");
    }
}

header('Location: alta_moldura.php');
?>