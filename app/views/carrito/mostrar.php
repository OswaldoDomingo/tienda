<?php
// Esta vista espera que el controlador defina $items (array), $totalGeneral (float) y $mensaje (string|null).
// Asegúrese de usar htmlspecialchars para toda salida dinámica proveniente de la base de datos o entrada del usuario.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Tienda Enmarcación</title>
    <!-- Adjust path to CSS if needed, assuming public/index.php is the entry point -->
    <link rel="stylesheet" type="text/css" href="../../css/estiloflex.css">
    <style>
        /* Basic styling for the cart page */
        .cart-container { max-width: 960px; margin: 20px auto; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; }
        .cart-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .cart-message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .cart-message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .cart-table th, .cart-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .cart-table th { background-color: #f2f2f2; }
        .cart-table img { max-width: 60px; height: auto; } /* If you add images */
        .cart-actions input[type="number"] { width: 60px; padding: 5px; }
        .cart-actions input[type="submit"] { padding: 5px 10px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .cart-actions input[type="submit"]:hover { background-color: #0056b3; }
        .cart-summary { text-align: right; margin-bottom: 20px; }
        .cart-summary strong { font-size: 1.2em; }
        .cart-buttons a, .cart-buttons button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .button-primary { background-color: #28a745; color: white; border: 1px solid #28a745;}
        .button-secondary { background-color: #6c757d; color: white; border: 1px solid #6c757d;}
        .button-danger { background-color: #dc3545; color: white; border: 1px solid #dc3545;}
        .button-link { background-color: transparent; color: #007bff; border: none; text-decoration: underline;}
    </style>
</head>
<body>
<div class="cart-container">
    <h1>Carrito de Compras</h1>

    <?php if (isset($mensaje) && !empty($mensaje)): ?>
        <div class="cart-message <?php echo (strpos(strtolower($mensaje), 'error') === false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <p>Su carrito está vacío.</p>
        <div class="cart-buttons">
            <a href="../../public/index.php" class="button-primary">Continuar Comprando</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
            <tr>
                <th>Referencia</th>
                <th>Descripción</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total Línea</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['referencia_producto']); ?></td>
                    <td><?php echo htmlspecialchars($item['descripcion_producto']); ?></td>
                    <td><?php echo number_format((float)$item['precio_unitario'], 2, ',', '.'); ?> &euro;</td>
                    <td class="cart-actions">
                        <form action="../../public/index.php?controller=carrito&action=actualizar" method="POST" style="display:inline;">
                            <input type="hidden" name="id_carro_item" value="<?php echo htmlspecialchars($item['id_carro']); ?>">
                            <input type="number" name="cantidad" value="<?php echo htmlspecialchars($item['cantidad']); ?>" min="0" step="any">
                            <input type="submit" value="Actualizar">
                        </form>
                    </td>
                    <td><?php echo number_format((float)$item['total_linea'], 2, ',', '.'); ?> &euro;</td>
                    <td>
                        <a href="../../public/index.php?controller=carrito&action=eliminar&id_carro_item=<?php echo htmlspecialchars($item['id_carro']); ?>"
                           onclick="return confirm('¿Está seguro de eliminar este artículo?');" class="button-link">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <strong>Total General: <?php echo number_format((float)$totalGeneral, 2, ',', '.'); ?> &euro;</strong>
        </div>

        <div class="cart-buttons">
            <a href="../../public/index.php" class="button-secondary">Continuar Comprando</a>
            <a href="../../public/index.php?controller=carrito&action=vaciar"
               onclick="return confirm('¿Está seguro de vaciar todo el carrito?');" class="button-danger">Vaciar Carrito</a>
            <button type="button" class="button-primary" onclick="alert('Funcionalidad de pago no implementada.');">Proceder al Pago</button>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
