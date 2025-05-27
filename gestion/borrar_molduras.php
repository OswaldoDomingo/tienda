<style type="text/css">
    *{font-size: 12px;}
    table {border: 1px solid black; float: left; margin: 5px auto; width: 95%; border-collapse: collapse;} /* Adjusted width and added collapse */
    th, td {border: 1px solid #ccc; padding: 4px; text-align: left;} /* Added padding and alignment */
    td input {width: 90%; box-sizing: border-box;} /* Adjusted input width */
    td input[type='submit'] {width: auto; padding: 2px 5px;} /* Specific width for submit buttons */
</style>
<?php
require_once("../includes/conexion.php"); // Use the central mysqli connection

// Ensure $mysqli is available
if (!isset($mysqli) || $mysqli->connect_errno) {
    echo "<p style='color:red;'>Error de conexi&oacute;n a la base de datos: " . ($mysqli->connect_error ?: 'No se pudo conectar') . "</p>";
    exit; // Stop further execution
}

echo "<table border='1'>";
echo "<thead><tr>
    <th>Imagen</th>
    <th>ID Referencia (Texto)</th>
    <th>Clave Referencia</th>
    <th>Precio</th>
    <th>Tipo ID</th>
    <th>Modelo ID</th>
    <th>Color ID</th>
    <th>Clase ID</th>
    <th>Color (Texto Descriptivo)</th>
    <th>Descuento</th>
    <th>Almacen ID</th>
    <th>Fecha Creaci&oacute;n</th>
    <th>Borrar</th>
    <th>Modificar</th>
    </tr></thead><tbody>";

// Modernized SELECT query
$sql = "SELECT id_referencia_pk, id_referencia, clave_referencia, precio, tipo_id, modelo_id, color_id, clase_id, color_texto, descuento, almacen_id, fecha_creacion 
        FROM referencia 
        ORDER BY id_referencia";

if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        echo "<form action='modi_molduras.php' method='POST'><tr>";
        // Hidden field for the actual primary key id_referencia_pk
        echo "<input type='hidden' name='id_referencia_pk_original' value='" . htmlspecialchars($row['id_referencia_pk']) . "'>";

        // Display and form fields
        echo "<td><a href='../molduras/imagenes/" . htmlspecialchars($row['id_referencia']) . ".jpg' target='_blank'>Ver</a></td>";
        echo "<td><input style='width: 105px;' type='text' name='id_referencia' value='" . htmlspecialchars($row['id_referencia']) . "'></td>";
        echo "<td><input style='width: 105px;' type='text' name='referencia' value='" . htmlspecialchars(isset($row['clave_referencia']) ? $row['clave_referencia'] : '') . "'></td>"; // clave_referencia
        echo "<td><input type='text' name='precio' value='" . htmlspecialchars(isset($row['precio']) ? $row['precio'] : '0.00') . "'></td>";
        echo "<td><input type='text' name='tipo_id' value='" . htmlspecialchars(isset($row['tipo_id']) ? $row['tipo_id'] : '') . "'></td>";
        echo "<td><input type='text' name='modelo_id' value='" . htmlspecialchars(isset($row['modelo_id']) ? $row['modelo_id'] : '') . "'></td>";
        echo "<td><input type='text' name='color_id' value='" . htmlspecialchars(isset($row['color_id']) ? $row['color_id'] : '') . "'></td>";
        echo "<td><input type='text' name='clase_id' value='" . htmlspecialchars(isset($row['clase_id']) ? $row['clase_id'] : '') . "'></td>";
        echo "<td><input type='text' name='color' value='" . htmlspecialchars(isset($row['color_texto']) ? $row['color_texto'] : '') . "'></td>"; // color_texto
        echo "<td><input type='text' name='descuento' value='" . htmlspecialchars(isset($row['descuento']) ? $row['descuento'] : '0.00') . "'></td>";
        echo "<td><input type='text' name='almacen_id' value='" . htmlspecialchars(isset($row['almacen_id']) ? $row['almacen_id'] : '') . "'></td>";
        echo "<td><input style='width: 130px;' type='text' name='fecha' value='" . htmlspecialchars(isset($row['fecha_creacion']) ? $row['fecha_creacion'] : '') . "'></td>"; // fecha_creacion
        echo "<td><input type='submit' name='borrar' value='Borrar' onclick='return confirm(\"¿Est&aacute; seguro de que desea borrar esta referencia?\");'></td>";
        echo "<td><input type='submit' name='modificar' value='Modificar'></td>";
        echo "</tr></form>";
    }
    $result->free(); // Free result set
} else {
    echo "<tr><td colspan='14'>Error al cargar las referencias: " . htmlspecialchars($mysqli->error) . "</td></tr>";
}
echo "</tbody></table> <br />";
?>