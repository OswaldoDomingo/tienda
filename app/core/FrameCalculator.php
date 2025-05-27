<?php
class FrameCalculator {
    /**
     * Calcula el precio y la descripción para un marco a medida basado en los datos POST.
     *
     * @param array $postData Normalmente, $_POST, contiene todos los campos del formulario.
     * @return array Un array con 'precio_final', 'descripcion_final' y 'error' (null si no hay error).
     */
    public static function calculateCustomFrameDetails(array $postData) {
        $details = [
            'precio_final' => 0.0,
            'descripcion_final' => '',
            'error' => null
        ];

        // Extrae y sanea/valida las variables relevantes de $postData
        // Esto refleja la lógica que antes estaba en marcos-a-medida/carro.php

        $tipo_id = isset($postData['tipo_id']) ? (int)$postData['tipo_id'] : null; // Para contexto, no se usa directamente en el cálculo de precio aquí
        $cantidad = isset($postData['cantidad']) ? (int)$postData['cantidad'] : 0;
        $referencia = isset($postData['referencia']) ? trim($postData['referencia']) : '';
        $formato = isset($postData['formato']) ? (int)$postData['formato'] : null; // 1:Tira, 2:Cortado, 3:Montado

        // Precios desde campos ocultos o datos del producto
        $kit_price = isset($postData['kit']) ? (float)$postData['kit'] : 0.0;
        $tira_price = isset($postData['tira']) ? (float)$postData['tira'] : 0.0;
        $cortado_price = isset($postData['cortado']) ? (float)$postData['cortado'] : 0.0;
        // El código original usaba $_POST['montado'] directamente. Debe venir de $postData.
        $montado_price = isset($postData['montado']) ? (float)$postData['montado'] : 0.0;

        // Dimensiones
        $ancho_cm = isset($postData['ancho_obra']) ? (float)$postData['ancho_obra'] : 0;
        $alto_cm = isset($postData['alto_obra']) ? (float)$postData['alto_obra'] : 0;
        // 'inglete' es el ancho de la moldura, crucial para calcular el material necesario.
        $inglete_cm = isset($postData['inglete']) ? (float)$postData['inglete'] : 0;

        if ($cantidad <= 0) {
            $details['error'] = "La cantidad debe ser mayor a cero.";
            return $details;
        }
        if ($formato != 1 && ($ancho_cm <= 0 || $alto_cm <= 0)) { // Se requieren dimensiones para cortado/montado
            $details['error'] = "Ancho y alto deben ser mayores a cero para marcos cortados o montados.";
            return $details;
        }
        if ($formato != 1 && $inglete_cm <= 0) { // Se requiere ancho de moldura para cortado/montado
            $details['error'] = "El ancho de la moldura (inglete) debe ser mayor a cero para marcos cortados o montados.";
            return $details;
        }

        $ancho_m = $ancho_cm / 100.0;
        $alto_m = $alto_cm / 100.0;
        $inglete_m = $inglete_cm / 100.0; // Ancho de la moldura en metros

        // Este era el cálculo original para los metros lineales necesarios.
        // Suma el ancho de la moldura una vez al perímetro.
        // Para formato 'tira' (formato=1), este $medida_lineal no se usa directamente para el precio,
        // ya que tira_price suele ser para una longitud estándar (ej: 2.5m).
        // Sin embargo, puede usarse en la descripción o si tira_price fuera por metro.
        $medida_lineal = ($ancho_m * 2) + ($alto_m * 2) + $inglete_m;

        $metrocuadrado = $ancho_m * $alto_m;
        $preciocristal = 0;
        $cristal_text = "";
        // Se asume que 'cristal' en $postData es un valor que indica selección, no el precio en sí.
        if (!empty($postData['cristal']) && $postData['cristal'] != 0) { // Ej: 'cristal' puede ser 'true' o '1'
            // Tarifa de ejemplo del código original (33.33 antes de IVA, luego 1.21 IVA).
            // Idealmente, esto debería venir de una configuración o base de datos de productos.
            $precio_cristal_m2_con_iva = 33.33 * 1.21;
            $preciocristal = $metrocuadrado * $precio_cristal_m2_con_iva;
            $cristal_text = " con frontal polyglass";
            if ($metrocuadrado > 1.5) { // Tope para tamaños grandes
                $preciocristal = 0;
                $cristal_text .= " (precio a consultar para este tamaño)";
            }
        }

        $preciocpluma = 0;
        $trasera_text = "";
        // Se asume que 'trasera' en $postData es un valor que indica selección.
        if (!empty($postData['trasera']) && $postData['trasera'] != 0) {
            // Tarifa de ejemplo del código original (9.39 antes de IVA, luego 1.21 IVA).
            $precio_trasera_m2_con_iva = 9.39 * 1.21;
            $preciocpluma = $metrocuadrado * $precio_trasera_m2_con_iva;
            $trasera_text = " con trasera";
            if ($metrocuadrado > 1.5) { // Tope para tamaños grandes
                $preciocpluma = 0;
                $trasera_text .= " (precio a consultar para este tamaño)";
            }
        }

        $kits_text = ($kit_price > 0) ? " con kit de montaje" : "";

        $precio_base_calculado = 0;
        $descripcion_base = "";

        switch ($formato) {
            case 1: // Tira
                // Se asume que $tira_price es el precio de una tira de longitud estándar (ej: 2.5m o 3m)
                // El código original hacía `$precio_valor = $tira + $kit;`
                $precio_base_calculado = $tira_price + $kit_price;
                $descripcion_base = "Tiras de moldura" . $kits_text; // La descripción puede necesitar la longitud estándar, ej: "Tira de 2.5m"
                break;
            case 2: // Cortado
                $precio_base_calculado = ($cortado_price * $medida_lineal) + $kit_price + $preciocristal + $preciocpluma;
                $descripcion_base = "Marco cortado medidas {$ancho_cm}x{$alto_cm}cm" . $cristal_text . $trasera_text . $kits_text;
                break;
            case 3: // Montado
                $precio_base_calculado = ($montado_price * $medida_lineal) + $kit_price + $preciocristal + $preciocpluma;
                $descripcion_base = "Marco montado medidas {$ancho_cm}x{$alto_cm}cm" . $cristal_text . $trasera_text . $kits_text;
                break;
            default:
                $details['error'] = "Formato de marco no v&aacute;lido seleccionado.";
                return $details;
        }

        // Precio final por unidad (un marco o una tira)
        $details['precio_final'] = round($precio_base_calculado, 2);
        $details['descripcion_final'] = $referencia . ": " . $descripcion_base;

        return $details;
    }
}
?>