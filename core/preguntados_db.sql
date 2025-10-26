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

CREATE TABLE partida(
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    resultado VARCHAR(10),
    puntajeGanado DOUBLE,
    fecha_creacion DATE
);

CREATE TABLE categoria_pregunta(
   id_categoria INT AUTO_INCREMENT PRIMARY KEY,
   categoria VARCHAR(50)
);

CREATE TABLE pregunta(
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255),
    dificultad VARCHAR(50),
    categoria VARCHAR(100),
    puntaje INT,
    respuesta_correcta INT,
    cant_acertadas INT,
    cant_erroneas INT,
    fecha_creacion DATE
);

CREATE TABLE respuesta(
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    respuesta VARCHAR(255),
    id_pregunta INT,
    FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
);