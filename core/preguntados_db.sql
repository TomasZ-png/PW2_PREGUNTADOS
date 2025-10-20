CREATE DATABASE IF NOT EXISTS preguntados_db;

use preguntados_db;

CREATE TABLE usuario(
                        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                        nombre VARCHAR(100) NOT NULL,
                        correo VARCHAR(255) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        rol VARCHAR(50) NOT NULL
);

INSERT INTO usuario (nombre, correo, password, rol)
VALUES ('Admin', 'admin@test.com', '1234', 'ADMIN');