-- Table: tipo
CREATE TABLE `tipo` (
`id_tipo` INT AUTO_INCREMENT,
`nombre_tipo` VARCHAR(255),
PRIMARY KEY (`id_tipo`)
);

-- Table: familia
CREATE TABLE `familia` (
`id_familia` INT AUTO_INCREMENT,
`nombre_familia` VARCHAR(255),
PRIMARY KEY (`id_familia`)
);

-- Table: modelo
CREATE TABLE `modelo` (
`id_modelo` INT AUTO_INCREMENT,
`nombre_modelo` VARCHAR(255),
`ancho_modelo` DECIMAL(10,2),
`alto_modelo` DECIMAL(10,2),
`inglete_modelo` DECIMAL(10,2),
`proveedor_modelo` VARCHAR(255),
`kit_modelo` VARCHAR(50),
PRIMARY KEY (`id_modelo`)
);

-- Table: color
CREATE TABLE `color` (
`id_color` INT AUTO_INCREMENT,
`nombre_color` VARCHAR(255),
PRIMARY KEY (`id_color`)
);

-- Table: clase
CREATE TABLE `clase` (
`id_clase` INT AUTO_INCREMENT,
`nombre_clase` VARCHAR(255),
PRIMARY KEY (`id_clase`)
);

-- Table: referencia (for mouldings/frames)
CREATE TABLE `referencia` (
`id_referencia_pk` INT AUTO_INCREMENT,
`id_referencia` VARCHAR(100) UNIQUE,
`clave_referencia` VARCHAR(100) NULL,
`precio` DECIMAL(10,2),
`tipo_id` INT,
`modelo_id` INT,
`color_id` INT,
`clase_id` INT,
`color_texto` VARCHAR(255) NULL,
`descuento` DECIMAL(5,2) DEFAULT 0.00,
`almacen_id` INT NULL,
`fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
`ultima_modificacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id_referencia_pk`)
-- FOREIGN KEY (`tipo_id`) REFERENCES `tipo`(`id_tipo`),
-- FOREIGN KEY (`modelo_id`) REFERENCES `modelo`(`id_modelo`),
-- FOREIGN KEY (`color_id`) REFERENCES `color`(`id_color`),
-- FOREIGN KEY (`clase_id`) REFERENCES `clase`(`id_clase`)
);

-- Table: articulo (for other products)
CREATE TABLE `articulo` (
`id_articulo` INT AUTO_INCREMENT,
`familia_id` INT,
`tipo_id` INT,
`referencia_articulo` VARCHAR(100) UNIQUE,
`descripcion` TEXT,
`imagen` VARCHAR(255) NULL,
`precio` DECIMAL(10,2),
`descuento` DECIMAL(5,2) DEFAULT 0.00,
`fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
`ultima_modificacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id_articulo`)
-- FOREIGN KEY (`familia_id`) REFERENCES `familia`(`id_familia`),
-- FOREIGN KEY (`tipo_id`) REFERENCES `tipo`(`id_tipo`)
);

-- Table: carro (Shopping Cart)
CREATE TABLE `carro` (
`id_carro` INT AUTO_INCREMENT,
`id_cliente_session` VARCHAR(255),
`fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
`referencia_producto` VARCHAR(100),
`es_molde` BOOLEAN DEFAULT FALSE,
`descripcion_producto` TEXT,
`cantidad` DECIMAL(10,2),
`precio_unitario` DECIMAL(10,2),
`total_linea` DECIMAL(10,2),
PRIMARY KEY (`id_carro`)
);

-- Table: pedidos (Confirmed Orders)
CREATE TABLE `pedidos` (
`id_pedido` INT AUTO_INCREMENT,
`id_cliente_session` VARCHAR(255) NULL,
`correo_cliente` VARCHAR(255),
`nombre_cliente` VARCHAR(255),
`apellido_cliente` VARCHAR(255),
`telefono_cliente` VARCHAR(50) NULL,
`direccion_envio` TEXT NULL,
`localidad_envio` VARCHAR(255) NULL,
`provincia_envio` VARCHAR(255) NULL,
`cp_envio` VARCHAR(20) NULL,
`comentarios_pedido` TEXT NULL,
`fecha_pedido` DATETIME DEFAULT CURRENT_TIMESTAMP,
`gastos_envio` DECIMAL(10,2) DEFAULT 0.00,
`subtotal_pedido` DECIMAL(10,2),
`total_pedido` DECIMAL(10,2),
`estado_pedido` VARCHAR(50) DEFAULT 'Pendiente',
PRIMARY KEY (`id_pedido`)
);

-- Table: pedido_items (Items within a Confirmed Order)
CREATE TABLE `pedido_items` (
`id_pedido_item` INT AUTO_INCREMENT,
`id_pedido` INT,
`referencia_producto` VARCHAR(100),
`es_molde` BOOLEAN DEFAULT FALSE,
`descripcion_producto` TEXT,
`cantidad` DECIMAL(10,2),
`precio_unitario` DECIMAL(10,2),
`total_item` DECIMAL(10,2),
PRIMARY KEY (`id_pedido_item`)
-- FOREIGN KEY (`id_pedido`) REFERENCES `pedidos`(`id_pedido`)
);
