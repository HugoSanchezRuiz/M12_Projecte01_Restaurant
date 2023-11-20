CREATE DATABASE bd_restaurante;

USE bd_restaurante;

-- Tabla de Camareros -> Esta tabla contiene todos los camareros del restaurante (escalable)
CREATE TABLE tbl_camarero (
    id_camarero INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    contra VARCHAR(100) NOT NULL
);

-- Tabla de Salas -> Esta tabla contiene todas las salas del restaurante (escalable)
CREATE TABLE tbl_sala (
    id_sala INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    tipo_sala ENUM('terraza', 'comedor', 'privada') NOT NULL,
    capacidad INT NOT NULL
);

-- Tabla de Mesas -> Esta tabla contiene todas las mesas del restaurante (escalable)
CREATE TABLE tbl_mesa (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT,
    id_sala INT,
    capacidad INT NOT NULL,
    ocupada BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_sala) REFERENCES tbl_sala(id_sala)
);

-- Tabla de Ocupaciones -> a esta tabla se le hará un insert cuando alguna persona ocupe alguna mesa o la desocupe
CREATE TABLE tbl_ocupacion (
    id_ocupacion INT PRIMARY KEY AUTO_INCREMENT,
    id_mesa INT,
    id_camarero INT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa),
    FOREIGN KEY (id_camarero) REFERENCES tbl_camarero(id_camarero)
);

INSERT INTO tbl_camarero (nombre, contra) VALUES
    ('camarero_1', '1234'),
    ('camarero_2', '1234'),
    ('camarero_3', '1234'),
    ('camarero_4', '1234'),
    ('camarero_5', '1234'),
    ('camarero_6', '1234');

-- Inserción de Salas
INSERT INTO tbl_sala (nombre, tipo_sala, capacidad) VALUES
    ('terraza_1', 'terraza', 4),
    ('terraza_2', 'terraza', 4),
    ('terraza_3', 'terraza', 4),
    ('terraza_4', 'terraza', 4),
    ('comedor_1', 'comedor', 6),
    ('comedor_2', 'comedor', 6),
    ('comedor_3', 'comedor', 6),
    ('sala_privada_1', 'privada', 2),
    ('sala_privada_2', 'privada', 2),
    ('sala_privada_3', 'privada', 2),
    ('sala_privada_4', 'privada', 2);

-- Inserción de Mesas
INSERT INTO tbl_mesa (id_sala, capacidad) VALUES
    (1, 2),
    (1, 2),
    (1, 3),
    (1, 3),
    (2, 2),
    (2, 2),
    (2, 3),
    (2, 3),
    (3, 2),
    (3, 2),
    (3, 3),
    (3, 3),
    (4, 2),
    (4, 2),
    (4, 3),
    (4, 3),
    (5, 6),
    (5, 6),
    (5, 6),
    (5, 6),
    (5, 6),
    (5, 6),
    (6, 6),
    (6, 6),
    (6, 6),
    (6, 6),
    (6, 6),
    (6, 6),
    (7, 4),
    (7, 4),
    (7, 4),
    (7, 4),
    (7, 4),
    (7, 4),
    (8, 10),
    (8, 10),
    (9, 8),
    (9, 8),
    (10, 8),
    (10, 8),
    (11, 15),
    (11, 15);


