<?php
session_start();
$cliente = session_id();
$id = $_REQUEST['id']; // Tipo de producto
$fd = $_REQUEST['fd']; // Familia del tipo de producto
require_once("../includes/conexion.php");
$mysqli = new mysqli('localhost', 'usuario', 'contraseña', 'basededatos');
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}
// FUNCIÓN QUE ELIMINA ACENTOS YA QUE TWITTER NO LOS SOPORTA
// function elimina_acentos($cadena){
// $tofind = "�����������������������������������������������������";
// $replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
// return(strtr($cadena,$tofind,$replac,$replac));
// }

// Inicializa variables para el título y meta para evitar errores si las consultas fallan
$nombre_tipo_titulo = "Artículos"; // Título por defecto
$nombre_familia_meta = "General"; // Meta por defecto
//Crear conexión a la base de datos


// Consulta para el título (tabla tipo)
$sql_titulo = "SELECT nombre_tipo FROM tipo WHERE id_tipo = ?";
if ($stmt_titulo = $mysqli->prepare($sql_titulo)) {
    $stmt_titulo->bind_param("i", $id);
    if ($stmt_titulo->execute()) {
        $result_titulo = $stmt_titulo->get_result();
        if ($row_titulo = $result_titulo->fetch_assoc()) {
            $nombre_tipo_titulo = $row_titulo['nombre_tipo'];
        }
        $result_titulo->free();
    } else {
        error_log("Error al ejecutar la consulta de título (articulos.php): " . $stmt_titulo->error);
    }
    $stmt_titulo->close();
} else {
    error_log("Error al preparar la consulta de título (articulos.php): " . $mysqli->error);
}

// Consulta para la meta (tabla familia)
$sql_meta = "SELECT nombre_familia FROM familia WHERE id_familia = ?";
if ($stmt_meta = $mysqli->prepare($sql_meta)) {
    $stmt_meta->bind_param("i", $fd);
    if ($stmt_meta->execute()) {
        $result_meta = $stmt_meta->get_result();
        if ($row_meta = $result_meta->fetch_assoc()) {
            $nombre_familia_meta = $row_meta['nombre_familia'];
        }
        $result_meta->free();
    } else {
        error_log("Error al ejecutar la consulta de meta (articulos.php): " . $stmt_meta->error);
    }
    $stmt_meta->close();
} else {
    error_log("Error al preparar la consulta de meta (articulos.php): " . $mysqli->error);
}

// Consulta principal de artículos (tabla articulo)
// Columnas específicas según el nuevo esquema, ordenadas por referencia_articulo
$_pagi_sql_template = "SELECT id_articulo, familia_id, tipo_id, referencia_articulo, descripcion, imagen, precio, descuento, fecha_creacion
                       FROM articulo
                       WHERE tipo_id = ? AND familia_id = ?
                       ORDER BY referencia_articulo ASC";
// Nota: La lógica de paginación (si existía en $_pagi_sql) no se maneja aquí,
// solo se prepara la plantilla SQL. La ejecución real se realiza abajo.
?>
<!DOCTYPE html>
<html lang="es">
    <head>
 <meta name=viewport content="width=device-width, initial-scale=1">
 <meta name="author" content="oswaldo">
    <?php
        // include ("metarticulos.php"); // Este archivo puede necesitar revisión si usa conexiones o variables antiguas
    ?>
    <link rel="stylesheet" type="text/css" href="../css/estiloflex.css">
    <style>
        <?php
        // Logo del producto - Esta lógica permanece, suponiendo que $id está correctamente saneado (se usa en consultas)
        // Considera mover esto a una función o a un enfoque más mantenible si crece.
        $logo_style = ''; // Por defecto sin estilo
        if ($id==2){ $logo_style="style='background: url(../imagenes/cartonpluma.png) top right no-repeat;'";}
        if ($id==3){ $logo_style="style='background: url(../imagenes/cartondaler.png) top right no-repeat;'";}
        if ($id==3 && $fd==33){ $logo_style="style='background: url(../imagenes/cartonconservacion.png) top right no-repeat;'";}
        if ($id==4){ $logo_style="style='background: url(../imagenes/polyglass.png) top right no-repeat;'";}
        if ($id==5){ $logo_style="style='background: url(../imagenes/marcos.png) top right no-repeat;'";}
        if ($id==6){ $logo_style="style='background: url(../imagenes/cuelgacuadros.png) top right no-repeat;'";}
        if ($id==11){ $logo_style="style='background: url(../imagenes/conservacion.png) top right no-repeat;'";}
        echo $logo_style; // Muestra el estilo si está definido
        ?>
    </style>
    <link rel="stylesheet" href="../java/css/lightbox.css" type="text/css" media="screen" />
    <script type="text/javascript" src="../java/js/prototype.js"></script>
    <script type="text/javascript" src="../java/js/scriptaculous.js?load=effects,builder"></script>
    <script type="text/javascript" src="../java/js/lightbox.js"></script>
 <title><?php echo htmlspecialchars($nombre_familia_meta) . " - " . htmlspecialchars($nombre_tipo_titulo); ?> - Cuadros Domingo</title>
    <!-- BOTÓN +1 GOOGLE. -->
    <script type="text/javascript" src="https://apis.google.com/js/plusone.js">
        {lang: 'es'}
    </script>
    <?php include("../includes/google.inc") ?>
    </head>

    <body>
        <header id="cabecera">
            <?php
            include ("../includes/cabecera.php");
            ?>
    </header>
    <div id="contenedor">
    <nav id="lista">
        <?php
            include ("../includes/listaresponsive.php");
        ?>
    </nav>
    <aside id="derecho">
        <?php
            include ("../includes/derecha.php");
        ?>
    </aside>
    <main id="cuerpo">
        <table id="tabla_articulos">
                <?php
                if ($fd==321){echo "<h3 style='text-align:center;'>Estas ofertas están sujetas a disponibilidad en el almacén</h3>";}
                ?>
                <tr>
                    <td>Imagen</td><td>Descripción</td>
                    <td><!--Compartir --></td><td>Precio</td>
                    <td>Cantidad</td><td>Agregar</td>
                </tr>
                <?php
                    // BITLY
                    require_once("../includes/bitly.php");

                    $url_fin = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

                    $short_url = get_bitly_short_url($url_fin,'cuadrosdomingo','R_2c4140fc7c1d9aa49adcb4d797a4e713');
                    // BITLY (Esta parte se mantiene igual, suponiendo que la función get_bitly_short_url está definida y funciona)
                    // require_once("../includes/bitly.php"); // Asegúrate de que esté correctamente ubicada si se usa
                    // $url_fin = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                    // $short_url = get_bitly_short_url($url_fin,'cuadrosdomingo','R_2c4140fc7c1d9aa49adcb4d797a4e713');

                    // Ejecuta la consulta principal de artículos usando prepared statement
                    if ($stmt_articulos = $mysqli->prepare($_pagi_sql_template)) {
                        $stmt_articulos->bind_param("ii", $id, $fd);
                        if ($stmt_articulos->execute()) {
                            $resultado_articulos = $stmt_articulos->get_result();
                            while ($columna = $resultado_articulos->fetch_assoc()) {
                                echo "<form action='../../public/index.php?controller=carrito&action=agregar' method='POST'>";
                                echo "<input type='hidden' name='es_molde' value='0'>"; // Los artículos no son molduras
                                echo "<tr style='height: 65px;'>";

                                $imagen_path = "imagenes/" . htmlspecialchars($columna['imagen']) . ".jpg";
                                $referencia_text = htmlspecialchars($columna['referencia_articulo']);
                                $descripcion_text = htmlspecialchars($columna['descripcion']);

                                echo "<td style='width:10%;'><a href='" . $imagen_path . "' title='Referencia " . $referencia_text . " - " . $descripcion_text . "' rel='lightbox'><img src='" . $imagen_path . "' title='" . $referencia_text . "' width='70px' alt='" . $descripcion_text ."'><br />Ampliar</a></td>";

                                echo "<input type='hidden' name='cliente' value='" . htmlspecialchars($cliente) . "'>";
                                // Usa referencia_articulo tanto para el valor como para el nombre del campo oculto
                                echo "<input type='hidden' name='referencia_articulo' value='" . $referencia_text . "'>";
                                echo "<td style='text-align:left;'>" . $descripcion_text . "<input type='hidden' name='descripcion' value='" . $descripcion_text . "'></td>";

                                echo "<td style='text-align:left;'>";
                                // Parte de compartir en redes sociales comentada como en el original, se puede modernizar si se necesita
                                /*
                                echo "<a target='_blank'  href='http://www.facebook.com/sharer.php?t=Articulo " . "&u=".$short_url."'><img title='Compartelo en Facebook' alt='Compartelo en Facebook' style='border:0px; margin: 1px 5px;  width: 15px;' src='../imagenes/facebook.png'><img style='border:0px;width: 0px;' src='imagenes/".$columna['imagen'].".jpg' /></a>";
                                echo "<a target='_blank' href='http://twitter.com/home/?status=". urlencode(elimina_acentos($descripcion_text)) ." Cuadros Domingo:+".$short_url."'><img title='Compartelo en Twitter' alt='Compartelo en Twitter' style='border:0px; margin:1px 5px; width: 15px;' src='../imagenes/icotwit.png'  /></a>";
                                echo "<g:plusone size='small'></g:plusone></td>";
                                */
                                echo "</td>"; // Cierra td de iconos sociales

                                // MODIFICANDO PARA DESCUENTOS
                                $precio_base = (float)$columna['precio'];
                                $descuento_percent = (float)$columna['descuento'];
                                $precio_con_iva = $precio_base * 1.21; // Suponiendo 21% de IVA

                                if ($descuento_percent > 0) {
                                    $precio_final = $precio_con_iva - ($precio_con_iva * $descuento_percent / 100);
                                    echo "<td style='width:15%;font-weight:bold;'>Antes <span style='text-decoration:line-through;'>" . number_format($precio_con_iva, 2) . "&euro;</span> <br /><span style='color:red'>" . number_format($descuento_percent, 2) . " %Dto.</span> <br />Ahora " . number_format($precio_final, 2) . " &euro;<br /><span style='color: silver; text-decoration:underline;'>Ahorro de " . number_format($precio_con_iva * ($descuento_percent / 100), 2) . " &euro; </span><input type='hidden' readonly name='precio' value='" . number_format($precio_final, 2, '.', '') . "'></td>";
                                } else {
                                    echo "<td style='width:15%;font-weight:bold;'>" . number_format($precio_con_iva, 2) . " &euro;<input type='hidden' name='precio' value='" . number_format($precio_con_iva, 2, '.', '') . "'></td>";
                                }
                                // MODIFICANDO PARA DESCUENTOS

                                echo "<td><input class='caja' type='text' size='1' name='cantidad'></td>";
                                echo "<td><input class='pedido' type='submit' name='pedir' value='Agregar'></td>";
                                echo "</tr>";
                                echo "</form>";
                            }
                            $resultado_articulos->free();
                        } else {
                            error_log("Error al ejecutar la consulta de artículos (articulos.php): " . $stmt_articulos->error);
                            echo "<tr><td colspan='6'>Error al cargar artículos.</td></tr>";
                        }
                        $stmt_articulos->close();
                    } else {
                        error_log("Error al preparar la consulta de artículos (articulos.php): " . $mysqli->error);
                        echo "<tr><td colspan='6'>Error al preparar la consulta de artículos.</td></tr>";
                    }
                ?>
            </table>
        </main>
        <footer id="pie"><?php include ("../includes/pieresponsive.php"); ?></footer>
    </div>
</body>
</html>