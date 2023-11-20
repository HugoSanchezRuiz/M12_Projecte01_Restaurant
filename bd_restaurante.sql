-- Tabla de Usuarios
CREATE TABLE tbl_camarero (
    id_camarero INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    contra VARCHAR(100) NOT NULL
);

-- Tabla de Sales
CREATE TABLE tbl_sala (
    id_sala INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    tipo_sala ENUM('terraza', 'comedor', 'privada') NOT NULL,
    capacidad INT NOT NULL
);

-- Tabla de Taules
CREATE TABLE tbl_mesa (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT,
    id_sala INT,
    capacidad INT NOT NULL,
    ocupada BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_sala) REFERENCES tbl_sala(id_sala)
);

-- Tabla de Ocupacions
CREATE TABLE tbl_ocupacion (
    id_ocupacion INT PRIMARY KEY AUTO_INCREMENT,
    id_mesa INT,
    id_camarero INT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa),
    FOREIGN KEY (id_camarero) REFERENCES tbl_camarero(id_camarero)
);
