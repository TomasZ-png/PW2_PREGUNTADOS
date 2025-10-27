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

-- CATEGORIAS
INSERT INTO categoria_pregunta (categoria) VALUES ('HISTORIA'), ('GEOGRAFIA'), ('ARTE'), ('CIENCIA'), ('DEPORTES');

-- HISTORIA
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
        ('¿Quién fue el primer presidente de Argentina?', '-', 'HISTORIA', 5, CURDATE()),
        ('¿En qué año comenzó la Revolución Francesa?', '-', 'HISTORIA', 5, CURDATE()),
        ('¿Qué imperio construyó Machu Picchu?', '-', 'HISTORIA', 5, CURDATE()),
        ('¿Quién descubrió América?', '-', 'HISTORIA', 5, CURDATE()),
        ('¿Qué país fue gobernado por Napoleón Bonaparte?', '-', 'HISTORIA', 5, CURDATE());

-- GEOGRAFÍA
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
        ('¿Cuál es el río más largo del mundo?', '-', 'GEOGRAFIA', 5, CURDATE()),
        ('¿Dónde se encuentra el monte Everest?', '-', 'GEOGRAFIA', 5, CURDATE()),
        ('¿Cuál es la capital de Australia?', '-', 'GEOGRAFIA', 5, CURDATE()),
        ('¿Qué océano baña las costas de Chile?', '-', 'GEOGRAFIA', 5, CURDATE()),
        ('¿Qué país tiene forma de bota?', '-', 'GEOGRAFIA', 5, CURDATE());

-- ARTE
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
        ('¿Quién pintó la Mona Lisa?', '-', 'ARTE', 5, CURDATE()),
        ('¿Qué estilo artístico usaba Picasso?', '-', 'ARTE', 5, CURDATE()),
        ('¿Qué instrumento tocaba Beethoven?', '-', 'ARTE', 5, CURDATE()),
        ('¿En qué país nació Frida Kahlo?', '-', 'ARTE', 5, CURDATE()),
        ('¿Qué obra representa a un hombre gritando en un puente?', '-', 'ARTE', 5, CURDATE());

-- CIENCIA
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
        ('¿Cuál es el planeta más grande del sistema solar?', '-', 'CIENCIA', 5, CURDATE()),
        ('¿Qué gas respiramos principalmente?', '-', 'CIENCIA', 5, CURDATE()),
        ('¿Quién formuló la teoría de la relatividad?', '-', 'CIENCIA', 5, CURDATE()),
        ('¿Qué órgano bombea la sangre?', '-', 'CIENCIA', 5, CURDATE()),
        ('¿Qué célula transporta oxígeno en la sangre?', '-', 'CIENCIA', 5, CURDATE());

-- DEPORTES
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
        ('¿Cuántos jugadores tiene un equipo de fútbol?', '-', 'DEPORTES', 5, CURDATE()),
        ('¿Quién ganó el Mundial 2022?', '-', 'DEPORTES', 5, CURDATE()),
        ('¿Qué deporte se juega en Wimbledon?', '-', 'DEPORTES', 5, CURDATE()),
        ('¿Qué país inventó el judo?', '-', 'DEPORTES', 5, CURDATE()),
        ('¿Qué atleta tiene más medallas olímpicas?', '-', 'DEPORTES', 5, CURDATE());


-- Pregunta 1
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Bernardino Rivadavia', 1), -- ✅
                                                   ('Domingo Sarmiento', 1),
                                                   ('Juan Manuel de Rosas', 1),
                                                   ('Manuel Belgrano', 1);
UPDATE pregunta SET respuesta_correcta = 1 WHERE id_pregunta = 1;

-- Pregunta 2
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('1789', 2), -- ✅
                                                   ('1776', 2),
                                                   ('1804', 2),
                                                   ('1750', 2);
UPDATE pregunta SET respuesta_correcta = 5 WHERE id_pregunta = 2;

-- Pregunta 3
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Imperio Inca', 3), -- ✅
                                                   ('Imperio Maya', 3),
                                                   ('Imperio Azteca', 3),
                                                   ('Imperio Romano', 3);
UPDATE pregunta SET respuesta_correcta = 9 WHERE id_pregunta = 3;

-- Pregunta 4
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Cristóbal Colón', 4), -- ✅
                                                   ('Américo Vespucio', 4),
                                                   ('Fernando de Magallanes', 4),
                                                   ('Marco Polo', 4);
UPDATE pregunta SET respuesta_correcta = 13 WHERE id_pregunta = 4;

-- Pregunta 5
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Francia', 5), -- ✅
                                                   ('España', 5),
                                                   ('Italia', 5),
                                                   ('Alemania', 5);
UPDATE pregunta SET respuesta_correcta = 17 WHERE id_pregunta = 5;


-- Pregunta 6
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Amazonas', 6), -- ✅
                                                   ('Nilo', 6),
                                                   ('Yangtsé', 6),
                                                   ('Mississippi', 6);
UPDATE pregunta SET respuesta_correcta = 21 WHERE id_pregunta = 6;

-- Pregunta 7
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Nepal', 7), -- ✅
                                                   ('India', 7),
                                                   ('China', 7),
                                                   ('Pakistán', 7);
UPDATE pregunta SET respuesta_correcta = 25 WHERE id_pregunta = 7;

-- Pregunta 8
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Canberra', 8), -- ✅
                                                   ('Sídney', 8),
                                                   ('Melbourne', 8),
                                                   ('Brisbane', 8);
UPDATE pregunta SET respuesta_correcta = 29 WHERE id_pregunta = 8;

-- Pregunta 9
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Océano Pacífico', 9), -- ✅
                                                   ('Océano Atlántico', 9),
                                                   ('Océano Índico', 9),
                                                   ('Mar Caribe', 9);
UPDATE pregunta SET respuesta_correcta = 33 WHERE id_pregunta = 9;

-- Pregunta 10
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Italia', 10), -- ✅
                                                   ('Grecia', 10),
                                                   ('México', 10),
                                                   ('Portugal', 10);
UPDATE pregunta SET respuesta_correcta = 37 WHERE id_pregunta = 10;


-- Pregunta 11
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Leonardo da Vinci', 11), -- ✅
                                                   ('Miguel Ángel', 11),
                                                   ('Vincent van Gogh', 11),
                                                   ('Pablo Picasso', 11);
UPDATE pregunta SET respuesta_correcta = 41 WHERE id_pregunta = 11;

-- Pregunta 12
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Cubismo', 12), -- ✅
                                                   ('Impresionismo', 12),
                                                   ('Surrealismo', 12),
                                                   ('Realismo', 12);
UPDATE pregunta SET respuesta_correcta = 45 WHERE id_pregunta = 12;

-- Pregunta 13
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Piano', 13), -- ✅
                                                   ('Violín', 13),
                                                   ('Flauta', 13),
                                                   ('Guitarra', 13);
UPDATE pregunta SET respuesta_correcta = 49 WHERE id_pregunta = 13;

-- Pregunta 14
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('México', 14), -- ✅
                                                   ('España', 14),
                                                   ('Argentina', 14),
                                                   ('Colombia', 14);
UPDATE pregunta SET respuesta_correcta = 53 WHERE id_pregunta = 14;

-- Pregunta 15
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('El Grito', 15), -- ✅
                                                   ('La Noche Estrellada', 15),
                                                   ('Guernica', 15),
                                                   ('Las Meninas', 15);
UPDATE pregunta SET respuesta_correcta = 57 WHERE id_pregunta = 15;


-- Pregunta 16
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Júpiter', 16), -- ✅
                                                   ('Saturno', 16),
                                                   ('Marte', 16),
                                                   ('Urano', 16);
UPDATE pregunta SET respuesta_correcta = 61 WHERE id_pregunta = 16;

-- Pregunta 17
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Oxígeno', 17), -- ✅
                                                   ('Nitrógeno', 17),
                                                   ('Dióxido de carbono', 17),
                                                   ('Hidrógeno', 17);
UPDATE pregunta SET respuesta_correcta = 65 WHERE id_pregunta = 17;

-- Pregunta 18
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Albert Einstein', 18), -- ✅
                                                   ('Isaac Newton', 18),
                                                   ('Stephen Hawking', 18),
                                                   ('Marie Curie', 18);
UPDATE pregunta SET respuesta_correcta = 69 WHERE id_pregunta = 18;

-- Pregunta 19
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Corazón', 19), -- ✅
                                                   ('Pulmón', 19),
                                                   ('Riñón', 19),
                                                   ('Hígado', 19);
UPDATE pregunta SET respuesta_correcta = 73 WHERE id_pregunta = 19;

-- Pregunta 20
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Glóbulo rojo', 20), -- ✅
                                                   ('Plaqueta', 20),
                                                   ('Neurona', 20),
                                                   ('Linfocito', 20);
UPDATE pregunta SET respuesta_correcta = 77 WHERE id_pregunta = 20;


-- Pregunta 21
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('11', 21), -- ✅
                                                   ('10', 21),
                                                   ('9', 21),
                                                   ('12', 21);
UPDATE pregunta SET respuesta_correcta = 81 WHERE id_pregunta = 21;

-- Pregunta 22
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Argentina', 22), -- ✅
                                                   ('Francia', 22),
                                                   ('Brasil', 22),
                                                   ('Alemania', 22);
UPDATE pregunta SET respuesta_correcta = 85 WHERE id_pregunta = 22;

-- Pregunta 23
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Tenis', 23), -- ✅
                                                   ('Golf', 23),
                                                   ('Fútbol', 23),
                                                   ('Críquet', 23);
UPDATE pregunta SET respuesta_correcta = 89 WHERE id_pregunta = 23;

-- Pregunta 24
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Japón', 24), -- ✅
                                                   ('China', 24),
                                                   ('Corea del Sur', 24),
                                                   ('India', 24);
UPDATE pregunta SET respuesta_correcta = 93 WHERE id_pregunta = 24;

-- Pregunta 25
INSERT INTO respuesta (respuesta, id_pregunta) VALUES
                                                   ('Michael Phelps', 25), -- ✅
                                                   ('Usain Bolt', 25),
                                                   ('Simone Biles', 25),
                                                   ('Carl Lewis', 25);
UPDATE pregunta SET respuesta_correcta = 97 WHERE id_pregunta = 25;