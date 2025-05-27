<?php
require_once("../includes/conexion.php"); // Use the central mysqli connection

$mysqli = new mysqli('localhost', 'usuario', 'contraseña', 'basededatos');
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$familia_sql = "SELECT id_familia, nombre_familia FROM familia ORDER BY id_familia";
$resultado_familia = $mysqli->query($familia_sql);

$tipo_sql = "SELECT id_tipo, nombre_tipo FROM tipo ORDER BY id_tipo";
$resultado_tipo = $mysqli->query($tipo_sql);
?>
<form action="alta_articulo.php" method="POST">
    <table style="border:1px dashed black; margin: 10px auto; width: 100%;">
        <tr>
            <td>Familia</td><td>Tipo</td><td>Referencia</td><td>Descripci&oacute;n</td><td>Precio</td><td>Imagen (URL)</td><td>Descuento (%)</td><td>Fecha Creaci&oacute;n</td>
        </tr>
        <tr>
            <td>
                <?php
                echo "<select name='familia_id'>";
                if ($resultado_familia) {
                    while ($columna = $resultado_familia->fetch_assoc()){ // Changed to fetch_assoc for clarity
                        echo "<option value='".htmlspecialchars($columna['id_familia'])."'>";
                        echo htmlspecialchars($columna['id_familia'])." - ".htmlspecialchars($columna['nombre_familia']);
                        echo "</option>";
                    }
                    $resultado_familia->free(); // Free result set
                } else {
                    echo "<option value=''>Error cargando familias: ".htmlspecialchars($mysqli->error)."</option>";
                }
                echo "</select>";
                ?>
            </td>
            <td>
                <?php
                echo "<select name='tipo_id'>";
                if ($resultado_tipo) {
                    while ($columna = $resultado_tipo->fetch_assoc()){ // Changed to fetch_assoc for clarity
                        echo "<option value='".htmlspecialchars($columna['id_tipo'])."'>";
                        echo htmlspecialchars($columna['id_tipo'])." - ".htmlspecialchars($columna['nombre_tipo']);
                        echo "</option>";
                    }
                    $resultado_tipo->free(); // Free result set
                } else {
                    echo "<option value=''>Error cargando tipos: ".htmlspecialchars($mysqli->error)."</option>";
                }
                echo "</select>";
                ?>
            </td>
            <td><input type="text" name="referencia" size="10" required></td>
            <td><textarea rows="3" cols="20" name="descripcion" required></textarea></td>
            <td><input type="text" name="precio" size="4" pattern="[0-9]+(\.[0-9]{1,2})?" title="Precio v&aacute;lido (e.g., 10.99)" required></td>
            <td><input type="text" name="imagen" size="10"></td>
            <td><input type="text" name="descuento" size="4" pattern="[0-9]+(\.[0-9]{1,2})?" title="Descuento v&aacute;lido (e.g., 5.50)" value="0.00"></td>
            <td><input type="text" name="fecha_creacion" value="<?php echo date('Y-m-d H:i:s'); ?>" size="15" readonly></td>
        </tr>
    </table>
    <input type="submit" name="enviar" value="Alta Art&iacute;culo">
</form>
<?php
// Process form submission
if (isset($_POST['enviar'])) {
    // Ensure $mysqli is available from conexion.php
    if (!isset($mysqli) || $mysqli->connect_errno) {
        echo "<p style='color:red;'>Error de conexi&oacute;n a la base de datos.</p>";
        // Potentially exit or log more formally here
        exit;
    }

    $familia_id = isset($_POST['familia_id']) ? (int)$_POST['familia_id'] : null;
    $tipo_id = isset($_POST['tipo_id']) ? (int)$_POST['tipo_id'] : null;
    $referencia = isset($_POST['referencia']) ? $_POST['referencia'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0.0;
    $imagen = isset($_POST['imagen']) ? $_POST['imagen'] : ''; // Path or URL to image
    $descuento = isset($_POST['descuento']) ? (float)$_POST['descuento'] : 0.0;
    // fecha_creacion will be taken from the form input which defaults to current date and time
    $fecha_creacion = isset($_POST['fecha_creacion']) ? $_POST['fecha_creacion'] : date('Y-m-d H:i:s');

    // Basic validation (can be enhanced)
    if ($familia_id !== null && $tipo_id !== null && !empty($referencia) && !empty($descripcion) && $precio > 0) {

        // SQL for insertion into 'articulo' table
        // Columns: familia_id, tipo_id, referencia_articulo, descripcion, imagen, precio, descuento, fecha_creacion
        // id_articulo is auto-increment. ultima_modificacion has default on update.
        $sql = "INSERT INTO articulo (familia_id, tipo_id, referencia_articulo, descripcion, imagen, precio, descuento, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind parameters: i=integer, s=string, d=double
            $stmt->bind_param("iisssdds", $familia_id, $tipo_id, $referencia, $descripcion, $imagen, $precio, $descuento, $fecha_creacion);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>Art&iacute;culo dado de alta correctamente. ID: ".$stmt->insert_id."</p>";
            } else {
                echo "<p style='color:red;'>Error al dar de alta el art&iacute;culo: " . htmlspecialchars($stmt->error) . "</p>";
                error_log("MySQLi execute error (alta_articulo.php): " . $stmt->error);
            }
            $stmt->close();
        } else {
            echo "<p style='color:red;'>Error al preparar la consulta: " . htmlspecialchars($mysqli->error) . "</p>";
            error_log("MySQLi prepare error (alta_articulo.php): " . $mysqli->error);
        }
    } else {
        echo "<p style='color:orange;'>Por favor, complete todos los campos obligatorios (Familia, Tipo, Referencia, Descripci&oacute;n, Precio).</p>";
    }
}
?>
