CREATE DATABASE IF NOT EXISTS preguntados_db;

USE preguntados_db;

CREATE TABLE usuario(
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    anio_nacimiento INT,
    sexo VARCHAR(20),
    foto_perfil VARCHAR(255),
    rol VARCHAR(50) NOT NULL
);

-- la contraseña del admin es 1234

INSERT INTO usuario (nombre_completo, correo, password, anio_nacimiento, sexo, foto_perfil, rol)
VALUES ('admin', 'admin@test.com', '$2y$10$IFiN1ghfvGdg2vFHf7.wcethB0wCbUXCDXHAO0XCr4wGEmcrmn/5m', '', '', '', 'ADMIN');