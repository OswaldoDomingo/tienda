<?php
require_once("./includes/conexion.php");

// Ensure $mysqli is available and connection is alive
if (!isset($mysqli) || $mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . (isset($mysqli->connect_errno) ? $mysqli->connect_errno : 'N/A') . ") " . (isset($mysqli->connect_error) ? $mysqli->connect_error : 'N/A') . PHP_EOL;
    exit;
}

echo "Successfully connected to MySQL." . PHP_EOL;

$inserted_ids = [];

// --- Insert into `tipo` table ---
$tipos = ["Madera", "Aluminio", "Lienzos Impresos"];
$sql_tipo = "INSERT INTO tipo (nombre_tipo) VALUES (?)";
if ($stmt_tipo = $mysqli->prepare($sql_tipo)) {
    foreach ($tipos as $index => $nombre) {
        $stmt_tipo->bind_param("s", $nombre);
        if ($stmt_tipo->execute()) {
            $inserted_id = $stmt_tipo->insert_id;
            $inserted_ids['tipo'][$nombre] = $inserted_id;
            echo "Inserted into tipo: $nombre (ID: $inserted_id)" . PHP_EOL;
        } else {
            echo "Error inserting into tipo ($nombre): " . $stmt_tipo->error . PHP_EOL;
        }
    }
    $stmt_tipo->close();
} else {
    echo "Error preparing tipo insert: " . $mysqli->error . PHP_EOL;
}

// --- Insert into `familia` table ---
$familias = ["Serie Clásica", "Serie Moderna", "Fotografía Artística"];
$sql_familia = "INSERT INTO familia (nombre_familia) VALUES (?)";
if ($stmt_familia = $mysqli->prepare($sql_familia)) {
    foreach ($familias as $index => $nombre) {
        $stmt_familia->bind_param("s", $nombre);
        if ($stmt_familia->execute()) {
            $inserted_id = $stmt_familia->insert_id;
            $inserted_ids['familia'][$nombre] = $inserted_id;
            echo "Inserted into familia: $nombre (ID: $inserted_id)" . PHP_EOL;
        } else {
            echo "Error inserting into familia ($nombre): " . $stmt_familia->error . PHP_EOL;
        }
    }
    $stmt_familia->close();
} else {
    echo "Error preparing familia insert: " . $mysqli->error . PHP_EOL;
}

// --- Insert into `modelo` table ---
$sql_modelo = "INSERT INTO modelo (nombre_modelo, ancho_modelo, alto_modelo, inglete_modelo, proveedor_modelo, kit_modelo) VALUES (?, ?, ?, ?, ?, ?)";
if ($stmt_modelo = $mysqli->prepare($sql_modelo)) {
    $nombre_modelo = "Estándar Convexo";
    $ancho_modelo = 3.0;
    $alto_modelo = 2.0;
    $inglete_modelo = 1.5;
    $proveedor_modelo = "Proveedor A";
    $kit_modelo = "No";
    $stmt_modelo->bind_param("sdddss", $nombre_modelo, $ancho_modelo, $alto_modelo, $inglete_modelo, $proveedor_modelo, $kit_modelo);
    if ($stmt_modelo->execute()) {
        $inserted_id = $stmt_modelo->insert_id;
        $inserted_ids['modelo'][$nombre_modelo] = $inserted_id;
        echo "Inserted into modelo: $nombre_modelo (ID: $inserted_id)" . PHP_EOL;
    } else {
        echo "Error inserting into modelo ($nombre_modelo): " . $stmt_modelo->error . PHP_EOL;
    }
    $stmt_modelo->close();
} else {
    echo "Error preparing modelo insert: " . $mysqli->error . PHP_EOL;
}

// --- Insert into `color` table ---
$sql_color = "INSERT INTO color (nombre_color) VALUES (?)";
if ($stmt_color = $mysqli->prepare($sql_color)) {
    $nombre_color = "Nogal Oscuro";
    $stmt_color->bind_param("s", $nombre_color);
    if ($stmt_color->execute()) {
        $inserted_id = $stmt_color->insert_id;
        $inserted_ids['color'][$nombre_color] = $inserted_id;
        echo "Inserted into color: $nombre_color (ID: $inserted_id)" . PHP_EOL;
    } else {
        echo "Error inserting into color ($nombre_color): " . $stmt_color->error . PHP_EOL;
    }
    $stmt_color->close();
} else {
    echo "Error preparing color insert: " . $mysqli->error . PHP_EOL;
}

// --- Insert into `clase` table ---
$sql_clase = "INSERT INTO clase (nombre_clase) VALUES (?)";
if ($stmt_clase = $mysqli->prepare($sql_clase)) {
    $nombre_clase = "Perfil Curvo";
    $stmt_clase->bind_param("s", $nombre_clase);
    if ($stmt_clase->execute()) {
        $inserted_id = $stmt_clase->insert_id;
        $inserted_ids['clase'][$nombre_clase] = $inserted_id;
        echo "Inserted into clase: $nombre_clase (ID: $inserted_id)" . PHP_EOL;
    } else {
        echo "Error inserting into clase ($nombre_clase): " . $stmt_clase->error . PHP_EOL;
    }
    $stmt_clase->close();
} else {
    echo "Error preparing clase insert: " . $mysqli->error . PHP_EOL;
}

// Use generated IDs or fall back to assumed IDs (e.g., 1, 2, 3) if any failed or for simplicity
$tipo_madera_id = isset($inserted_ids['tipo']['Madera']) ? $inserted_ids['tipo']['Madera'] : 1;
$tipo_lienzos_id = isset($inserted_ids['tipo']['Lienzos Impresos']) ? $inserted_ids['tipo']['Lienzos Impresos'] : 3;
$familia_fotografia_id = isset($inserted_ids['familia']['Fotografía Artística']) ? $inserted_ids['familia']['Fotografía Artística'] : 3;
$modelo_estandar_id = isset($inserted_ids['modelo']['Estándar Convexo']) ? $inserted_ids['modelo']['Estándar Convexo'] : 1;
$color_nogal_id = isset($inserted_ids['color']['Nogal Oscuro']) ? $inserted_ids['color']['Nogal Oscuro'] : 1;
$clase_perfil_curvo_id = isset($inserted_ids['clase']['Perfil Curvo']) ? $inserted_ids['clase']['Perfil Curvo'] : 1;

// --- Insert into `referencia` table ---
$sql_referencia = "INSERT INTO referencia (id_referencia, clave_referencia, precio, tipo_id, modelo_id, color_id, clase_id, color_texto, descuento, almacen_id, fecha_creacion) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
if ($stmt_referencia = $mysqli->prepare($sql_referencia)) {
    $id_ref = "REF001";
    $clave_ref = "M001-NO";
    $precio_ref = 25.99;
    $color_texto_ref = "Nogal Oscuro Textual";
    $descuento_ref = 0.00;
    $almacen_id_ref = 10;
    $fecha_creacion_ref = date('Y-m-d H:i:s');

    $stmt_referencia->bind_param("ssdiidsisds",
        $id_ref, $clave_ref, $precio_ref, $tipo_madera_id, $modelo_estandar_id,
        $color_nogal_id, $clase_perfil_curvo_id, $color_texto_ref, $descuento_ref,
        $almacen_id_ref, $fecha_creacion_ref);

    if ($stmt_referencia->execute()) {
        echo "Inserted into referencia: $id_ref (ID: " . $stmt_referencia->insert_id . ")" . PHP_EOL;
    } else {
        echo "Error inserting into referencia ($id_ref): " . $stmt_referencia->error . PHP_EOL;
    }
    $stmt_referencia->close();
} else {
    echo "Error preparing referencia insert: " . $mysqli->error . PHP_EOL;
}

// --- Insert into `articulo` table ---
$sql_articulo = "INSERT INTO articulo (familia_id, tipo_id, referencia_articulo, descripcion, imagen, precio, descuento, fecha_creacion) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
if ($stmt_articulo = $mysqli->prepare($sql_articulo)) {
    $ref_art = "ARTIC001";
    $desc_art = "Impresionante paisaje montañoso impreso en lienzo de alta calidad. Perfecto para decorar su salón o oficina.";
    $img_art = "paisaje01";
    $precio_art = 79.50;
    $descuento_art = 10.00;
    $fecha_creacion_art = date('Y-m-d H:i:s');

    $stmt_articulo->bind_param("iisssdds",
        $familia_fotografia_id, $tipo_lienzos_id, $ref_art, $desc_art,
        $img_art, $precio_art, $descuento_art, $fecha_creacion_art);

    if ($stmt_articulo->execute()) {
        echo "Inserted into articulo: $ref_art (ID: " . $stmt_articulo->insert_id . ")" . PHP_EOL;
    } else {
        echo "Error inserting into articulo ($ref_art): " . $stmt_articulo->error . PHP_EOL;
    }
    $stmt_articulo->close();
} else {
    echo "Error preparing articulo insert: " . $mysqli->error . PHP_EOL;
}

$mysqli->close();
echo "Data population script finished." . PHP_EOL;
?>
