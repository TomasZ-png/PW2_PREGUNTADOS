CREATE DATABASE IF NOT EXISTS preguntados_db;

USE preguntados_db;

-- TABLA USUARIO
CREATE TABLE usuario(
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    anio_nacimiento INT,
    sexo VARCHAR(20),
    foto_perfil VARCHAR(255),
    rol VARCHAR(50) NOT NULL,
    puntaje_global INT
);

-- INSERTO DE USUARIO ADMIN (ID: 1)
INSERT INTO usuario (nombre_completo, correo, password, rol)
VALUES ('admin', 'admin@test.com', '$2y$10$IFiN1ghfvGdg2vFHf7.wcethB0wCbUXCDXHAO0XCr4wGEmcrmn/5m', 'ADMIN');
-- INSERTO UN JUGADOR POR DEFECTO (ID: 2)
INSERT INTO usuario (nombre_completo, correo, password, rol)
VALUES ('jugador', 'jugador@test.com', '$2y$10$IFiN1ghfvGdg2vFHf7.wcethB0wCbUXCDXHAO0XCr4wGEmcrmn/5m', 'JUGADOR');

-- TABLA PARTIDA (CORREGIDA para incluir id_jugador y campos de seguimiento)
CREATE TABLE partida(
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador INT NOT NULL, -- <<<<<< ESTA ES LA COLUMNA FALTANTE
    estado_partida VARCHAR(10), -- Se cambia 'resultado' a 'estado_partida'
    puntaje_final DOUBLE, -- Se cambia 'puntajeGanado' a 'puntaje_final'
    categorias_ganadas VARCHAR(255), -- Nuevo campo para registrar categorías completadas
    fecha_creacion DATE,
    fecha_fin DATETIME, -- Nuevo campo para registrar el fin
    FOREIGN KEY (id_jugador) REFERENCES usuario(id_usuario)
);

-- TABLA CATEGORIA
CREATE TABLE categoria_pregunta(
   id_categoria INT AUTO_INCREMENT PRIMARY KEY,
   categoria VARCHAR(50)
);

-- TABLA PREGUNTA (Se eliminó 'respuesta_correcta')
CREATE TABLE pregunta(
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255),
    dificultad VARCHAR(50),
    categoria VARCHAR(100),
    puntaje INT,
    cant_acertadas INT,
    cant_erroneas INT,
    fecha_creacion DATE
);

-- TABLA RESPUESTA (CORREGIDA para marcar la correcta)
CREATE TABLE respuesta(
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    respuesta VARCHAR(255),
    id_pregunta INT,
    es_correcta BOOLEAN NOT NULL DEFAULT 0, -- <<<<<< ESTE CAMPO MARCA LA RESPUESTA CORRECTA
    FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta)
);

-- CATEGORIAS
INSERT INTO categoria_pregunta (categoria) VALUES ('HISTORIA'), ('GEOGRAFIA'), ('ARTE'), ('CIENCIA'), ('DEPORTES');

-- INSERTS DE PREGUNTAS (Tus inserts de pregunta son correctos)
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
        ('¿Quién fue el primer presidente de Argentina?', '-', 'HISTORIA', 5, CURDATE()), -- ID 1
        ('¿En qué año comenzó la Revolución Francesa?', '-', 'HISTORIA', 5, CURDATE()), -- ID 2
        ('¿Qué imperio construyó Machu Picchu?', '-', 'HISTORIA', 5, CURDATE()), -- ID 3
        ('¿Quién descubrió América?', '-', 'HISTORIA', 5, CURDATE()), -- ID 4
        ('¿Qué país fue gobernado por Napoleón Bonaparte?', '-', 'HISTORIA', 5, CURDATE()), -- ID 5
        ('¿Cuál es el río más largo del mundo?', '-', 'GEOGRAFIA', 5, CURDATE()), -- ID 6
        ('¿Dónde se encuentra el monte Everest?', '-', 'GEOGRAFIA', 5, CURDATE()), -- ID 7
        ('¿Cuál es la capital de Australia?', '-', 'GEOGRAFIA', 5, CURDATE()), -- ID 8
        ('¿Qué océano baña las costas de Chile?', '-', 'GEOGRAFIA', 5, CURDATE()), -- ID 9
        ('¿Qué país tiene forma de bota?', '-', 'GEOGRAFIA', 5, CURDATE()), -- ID 10
        ('¿Quién pintó la Mona Lisa?', '-', 'ARTE', 5, CURDATE()), -- ID 11
        ('¿Qué estilo artístico usaba Picasso?', '-', 'ARTE', 5, CURDATE()), -- ID 12
        ('¿Qué instrumento tocaba Beethoven?', '-', 'ARTE', 5, CURDATE()), -- ID 13
        ('¿En qué país nació Frida Kahlo?', '-', 'ARTE', 5, CURDATE()), -- ID 14
        ('¿Qué obra representa a un hombre gritando en un puente?', '-', 'ARTE', 5, CURDATE()), -- ID 15
        ('¿Cuál es el planeta más grande del sistema solar?', '-', 'CIENCIA', 5, CURDATE()), -- ID 16
        ('¿Qué gas respiramos principalmente?', '-', 'CIENCIA', 5, CURDATE()), -- ID 17
        ('¿Quién formuló la teoría de la relatividad?', '-', 'CIENCIA', 5, CURDATE()), -- ID 18
        ('¿Qué órgano bombea la sangre?', '-', 'CIENCIA', 5, CURDATE()), -- ID 19
        ('¿Qué célula transporta oxígeno en la sangre?', '-', 'CIENCIA', 5, CURDATE()), -- ID 20
        ('¿Cuántos jugadores tiene un equipo de fútbol?', '-', 'DEPORTES', 5, CURDATE()), -- ID 21
        ('¿Quién ganó el Mundial 2022?', '-', 'DEPORTES', 5, CURDATE()), -- ID 22
        ('¿Qué deporte se juega en Wimbledon?', '-', 'DEPORTES', 5, CURDATE()), -- ID 23
        ('¿Qué país inventó el judo?', '-', 'DEPORTES', 5, CURDATE()), -- ID 24
        ('¿Qué atleta tiene más medallas olímpicas?', '-', 'DEPORTES', 5, CURDATE()); -- ID 25

-- INSERTS DE RESPUESTAS (Usando el nuevo campo 'es_correcta')

-- P1
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Bernardino Rivadavia', 1, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Domingo Sarmiento', 1), ('Juan Manuel de Rosas', 1), ('Manuel Belgrano', 1);

-- P2
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('1789', 2, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('1776', 2), ('1804', 2), ('1750', 2);

-- P3
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Imperio Inca', 3, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Imperio Maya', 3), ('Imperio Azteca', 3), ('Imperio Romano', 3);

-- P4
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Cristóbal Colón', 4, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Américo Vespucio', 4), ('Fernando de Magallanes', 4), ('Marco Polo', 4);

-- P5
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Francia', 5, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('España', 5), ('Italia', 5), ('Alemania', 5);

-- P6
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Amazonas', 6, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Nilo', 6), ('Yangtsé', 6), ('Mississippi', 6);

-- P7
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Nepal', 7, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('India', 7), ('China', 7), ('Pakistán', 7);

-- P8
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Canberra', 8, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Sídney', 8), ('Melbourne', 8), ('Brisbane', 8);

-- P9
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Océano Pacífico', 9, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Océano Atlántico', 9), ('Océano Índico', 9), ('Mar Caribe', 9);

-- P10
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Italia', 10, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Grecia', 10), ('México', 10), ('Portugal', 10);

-- P11
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Leonardo da Vinci', 11, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Miguel Ángel', 11), ('Vincent van Gogh', 11), ('Pablo Picasso', 11);

-- P12
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Cubismo', 12, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Impresionismo', 12), ('Surrealismo', 12), ('Realismo', 12);

-- P13
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Piano', 13, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Violín', 13), ('Flauta', 13), ('Guitarra', 13);

-- P14
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('México', 14, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('España', 14), ('Argentina', 14), ('Colombia', 14);

-- P15
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('El Grito', 15, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('La Noche Estrellada', 15), ('Guernica', 15), ('Las Meninas', 15);

-- P16
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Júpiter', 16, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Saturno', 16), ('Marte', 16), ('Urano', 16);

-- P17
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Oxígeno', 17, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Nitrógeno', 17), ('Dióxido de carbono', 17), ('Hidrógeno', 17);

-- P18
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Albert Einstein', 18, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Isaac Newton', 18), ('Stephen Hawking', 18), ('Marie Curie', 18);

-- P19
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Corazón', 19, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Pulmón', 19), ('Riñón', 19), ('Hígado', 19);

-- P20
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Glóbulo rojo', 20, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Plaqueta', 20), ('Neurona', 20), ('Linfocito', 20);

-- P21
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('11', 21, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('10', 21), ('9', 21), ('12', 21);

-- P22
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Argentina', 22, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Francia', 22), ('Brasil', 22), ('Alemania', 22);

-- P23
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Tenis', 23, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Golf', 23), ('Fútbol', 23), ('Críquet', 23);

-- P24
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Japón', 24, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('China', 24), ('Corea del Sur', 24), ('India', 24);

-- P25
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Michael Phelps', 25, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Usain Bolt', 25), ('Simone Biles', 25), ('Carl Lewis', 25);