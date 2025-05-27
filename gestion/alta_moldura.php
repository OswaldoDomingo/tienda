<?php
//Conectar con la base de datos
require_once("../includes/conexion.php"); // Use the central mysqli connection

// Ensure $mysqli is available
if (!isset($mysqli) || $mysqli->connect_errno) {
    echo "<p style='color:red;'>Error de conexi&oacute;n a la base de datos: " . ($mysqli->connect_error ?: 'No se pudo conectar') . "</p>";
    // Stop further execution if DB connection isn't available
    exit;
}
?>

<style>
    table {border: 1px solid black; float: left; margin: 20px;}
</style>


<!-- ALTA REFERENCIA DE MOLDURA -->
<form action='bd_alta_molduras.php' method='POST'>
    <table>
        <tr><td  colspan='2'>Referencia de moldura</td></tr>
        <tr><td>id_referencia</td>	<td><input type='text' name='id_referencia'></td></tr>
        <tr><td>clave</td>	<td><input type='text' name='clave'></td></tr>
        <tr><td>precio</td><td><input type='text' name='precio'></td></tr>
        <tr><td>tipo_id</td><td><select name='tipo_id'>
                    <?php
                    $sql_tipo = "SELECT id_tipo, nombre_tipo FROM tipo ORDER BY id_tipo DESC";
                    if ($result_tipo = $mysqli->query($sql_tipo)) {
                        while ($row_tipo = $result_tipo->fetch_assoc()){
                            echo "<option value='".htmlspecialchars($row_tipo['id_tipo'])."'>".htmlspecialchars($row_tipo['id_tipo'])." - ".htmlspecialchars($row_tipo['nombre_tipo'])."</option>";
                        }
                        $result_tipo->free();
                    } else {
                        echo "<option value=''>Error: ".htmlspecialchars($mysqli->error)."</option>";
                    }
                    ?>
                </select></td></tr>
        <tr><td>modelo_id</td><td><select name='modelo_id'>
                    <?php
                    $sql_modelo = "SELECT id_modelo, nombre_modelo FROM modelo ORDER BY id_modelo"; // Assuming nombre_modelo exists for display
                    if ($result_modelo = $mysqli->query($sql_modelo)) {
                        while ($row_modelo = $result_modelo->fetch_assoc()){
                            // Displaying id_modelo - nombre_modelo if available, otherwise just id_modelo
                            $display_modelo = htmlspecialchars($row_modelo['id_modelo']);
                            if (isset($row_modelo['nombre_modelo']) && !empty($row_modelo['nombre_modelo'])) {
                                $display_modelo .= " - ".htmlspecialchars($row_modelo['nombre_modelo']);
                            }
                            echo "<option value='".htmlspecialchars($row_modelo['id_modelo'])."'>".$display_modelo."</option>";
                        }
                        $result_modelo->free();
                    } else {
                        echo "<option value=''>Error: ".htmlspecialchars($mysqli->error)."</option>";
                    }
                    ?>
                </select></td></tr>
        <tr><td>color_id</td><td><select name='color_id'>
                    <?php
                    $sql_color = "SELECT id_color, nombre_color FROM color ORDER BY id_color";
                    if ($result_color = $mysqli->query($sql_color)) {
                        while ($row_color = $result_color->fetch_assoc()){
                            echo "<option value='".htmlspecialchars($row_color['id_color'])."'>".htmlspecialchars($row_color['id_color'])." - ".htmlspecialchars($row_color['nombre_color'])."</option>";
                        }
                        $result_color->free();
                    } else {
                        echo "<option value=''>Error: ".htmlspecialchars($mysqli->error)."</option>";
                    }
                    ?>
                </select></td></tr>
        <tr><td>clase_id</td><td><select name='clase_id'>
                    <?php
                    $sql_clase = "SELECT id_clase, nombre_clase FROM clase ORDER BY id_clase";
                    if ($result_clase = $mysqli->query($sql_clase)) {
                        while ($row_clase = $result_clase->fetch_assoc()){
                            echo "<option value='".htmlspecialchars($row_clase['id_clase'])."'>".htmlspecialchars($row_clase['id_clase'])." - ".htmlspecialchars($row_clase['nombre_clase'])."</option>";
                        }
                        $result_clase->free();
                    } else {
                        echo "<option value=''>Error: ".htmlspecialchars($mysqli->error)."</option>";
                    }
                    ?>
                </select></td></tr>
        <tr><td>Color (texto)</td><td><input type='text' name='color_texto' title="Color descriptivo para la moldura, ej: 'Roble oscuro con veta'"></td></tr>
        <tr><td>Descuento</td><td><input type='text' name='descuento' value="0.00" pattern="[0-9]+(\.[0-9]{1,2})?" title="Ej: 5.50"></td></tr>
        <tr><td>Almacen ID</td><td><input type='text' name='almacen_id' value="1"></td></tr>
        <tr><td>Fecha Creaci&oacute;n</td><td><input type='text' name='fecha_creacion' value='<?php echo date('Y-m-d H:i:s'); ?>' readonly></td></tr>
        <tr><td>Dar de alta</td><td><input type='submit' name='alta_referencia' value='Dar alta'></td></tr>
    </table>
</form>
<!-- ALTA REFERENCIA DE MOLDURA -->


<!-- ALTA MODELO DE MOLDURA -->
<form action='bd_alta_molduras.php' method='POST'>
    <table>
        <tr><td colspan='2'>Modelo de moldura</td></tr>
        <tr><td>id_modelo</td>	<td><input type='text' name='id_modelo'></td></tr>
        <tr><td>ancho_modelo</td><td><input type='text' name='ancho_modelo'></td></tr>
        <tr><td>alto_modelo</td><td><input type='text' name='alto_modelo'></td></tr>
        <tr><td>inglete_modelo</td><td><input type='text' name='inglete_modelo'></td></tr>
        <tr><td>proveedor_modelo</td><td><input type='text' name='proveedor_modelo'></td></tr>
        <tr><td>kit_modelo</td><td><input type='text' name='kit_modelo'></td></tr>
        <tr><td>Dar de alta</td><td><input type='submit' name='alta_modelo' value='Dar alta'></td></tr>
    </table>
</form>
<!-- ALTA MODELO DE MOLDURA -->

<!-- ALTA COLOR DE MOLDURA -->
<form action='bd_alta_molduras.php' method='POST'>
    <table>
        <tr><td colspan='2'>Color de moldura</td></tr>
        <tr><td>id_color</td>	<td><input type='text' name='id_color'></td></tr>
        <tr><td>nombre_color</td><td><input type='text' name='nombre_color'></td></tr>
        <tr><td>Dar de alta</td><td><input type='submit' name='alta_color' value='Dar alta'></td></tr>
    </table>
</form>
<!-- ALTA COLOR DE MOLDURA -->

<!-- ALTA CLASE DE MOLDURA -->
<form action='bd_alta_molduras.php' method='POST'>
    <table>
        <tr><td colspan='2'>Clase de moldura Etnica - Ca&ntilde;a - Plana - Bord&oacute;n</td></tr>
        <tr><td>id_clase</td><td><input type='text' name='id_clase'></td></tr>
        <tr><td>nombre_clase</td><td><input type='text' name='nombre_clase'></td></tr>
        <tr><td>Dar de alta</td><td><input type='submit' name='alta_clase' value='Dar alta'></td></tr>
    </table>
</form>
<!-- ALTA CLASE DE MOLDURA -->

<!-- ALTA TIPO DE MOLDURA -->
<form action='bd_alta_molduras.php' method='POST'>
    <table>
        <tr><td colspan='2'>Tipo de moldura Madera - Aluminio</td></tr>
        <tr><td>id_tipo</td>	<td><input type='text' name='id_tipo'></td></tr>
        <tr><td>nombre_tipo</td><td><input type='text' name='nombre_tipo'></td></tr>
        <tr><td>Dar de alta</td><td><input type='submit' name='alta_tipo' value='Dar alta'></td></tr>
    </table>
</form>
<!-- ALTA TIPO DE MOLDURA -->

