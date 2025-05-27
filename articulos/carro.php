<?php
session_start();
//INTRODUCCION DE LOS PRODUCTOS EN LA BASE DE DATOS

include("../includes/conexion.php");
$mysqli = new mysqli('localhost', 'usuario', 'contraseña', 'basededatos');
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}
$cliente=$_POST['cliente'];//NUMERO DE SESION
// Correctly capture 'referencia_articulo' from POST as per the modernized articulos.php form
$referencia_articulo_form = isset($_POST['referencia_articulo']) ? trim($_POST['referencia_articulo']) : NULL;
$descripcion=$_POST['descripcion'];
$precio=$_POST['precio'];
$cantidad=$_POST['cantidad'];
$total=$cantidad*$precio;
$pedido=$_POST['pedir'];
$fecha=date("Y-m-d -- H:i:s");
$carro="SELECT * FROM carro"; // This SELECT query is not used, consider removing if not needed for future functionality.

if (!empty($cantidad) && isset($pedido) && $pedido != ''){
    // Modernized INSERT query using prepared statements
    // Added `es_molde` column with a default value of 0 (FALSE) as it's new in the schema
    $sql = "INSERT INTO carro (id_cliente_session, fecha_creacion, referencia_producto, es_molde, descripcion_producto, cantidad, precio_unitario, total_linea) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        $es_molde_default = 0; // Default value for the new boolean column
        // Bind parameters: s=string, i=integer, d=double
        // Original columns: cliente, fecha, referencia, descripcion, cantidad, precio, total
        // New schema order: id_cliente_session, fecha_creacion, referencia_producto, es_molde, descripcion_producto, cantidad, precio_unitario, total_linea
        $stmt->bind_param("ssisdddd", $cliente, $fecha, $referencia_articulo_form, $es_molde_default, $descripcion, $cantidad, $precio, $total);

        if ($stmt->execute()) {
            // Success
        } else {
            // Error executing statement
            error_log("Error executing prepared statement (carro.php): " . $stmt->error);
            // Optionally, inform the user, but avoid exposing detailed SQL errors
            echo "<p class='carro_articulos'>Hubo un problema al guardar su art&iacute;culo. Por favor, intente de nuevo.</p>";
        }
        $stmt->close();
    } else {
        // Error preparing statement
        error_log("Error preparing statement (carro.php): " . $mysqli->error);
        // Optionally, inform the user
        echo "<p class='carro_articulos'>Hubo un problema al preparar su pedido. Por favor, intente de nuevo.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name="author" content="oswaldo">
    <link rel="stylesheet" type="text/css" href="../css/estiloflex.css">
    <title>Inserci&oacute;n de productos en el carro de la compra</title>
    <?php // include("../includes/google.inc"); // Consider if this include is still needed and valid ?>
</head>
<body>
<div id="contenedor">
    <div id="cabecera">
        <?php include ("../includes/cabecera.php"); // Make sure this path is correct and file exists ?>
    </div>
    <div id="lista">
        <?php include ("../includes/listaresponsive.php"); // Make sure this path is correct and file exists ?>
    </div>
    <div id="derecho">
        <?php include ("../includes/derecha.php"); // Make sure this path is correct and file exists ?>
    </div>
    <div id="cuerpo">
        <?php
        // Display message only if the item was successfully processed (or attempted)
        // The original logic showed messages even if DB operation might have failed silently or if $cantidad was manipulated post-check.
        if (!empty($cantidad) && isset($pedido) && $pedido != ''){ // Re-check condition for displaying message
            if ($stmt && $stmt->affected_rows > 0) { // Check if insert was successful
                echo "<p class='carro_articulos'>Se ha a&ntilde;adido a su pedido:<br /> ";
                echo "Cantidad: ".htmlspecialchars($cantidad)." - Art&iacute;culo: ".htmlspecialchars($descripcion)." Precio: "; printf("%.2f &euro;",floatval($total));
                echo "</p><p class='carro_articulos'><a href='ver_pedido.php'>Ver pedido.</a></p>";
            } else if (empty($stmt->error)) {
                // If there was no $stmt error explicitly set from execute(), but affected_rows is not > 0
                // This case might occur if the prepare failed, or if execute returned false but no specific error was caught before.
                // The error messages for prepare/execute failure are now shown where the error occurs.
                // If $cantidad was 0 or $pedido was empty, the original code would skip the insert block.
                // This section ensures user feedback aligns with attempted operations.
            }
        }
        echo "<p class_carro_articulos'><a href='".htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php')."'>Seguir comprando.</a></p>"; // Added htmlspecialchars and null coalesce
        ?>
    </div>
    <div id="pie"><?php include ("../includes/pieresponsive.php"); // Make sure this path is correct and file exists ?></div>
</div>
</body>
</html>

