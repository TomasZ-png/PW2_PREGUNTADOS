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
    puntaje_global INT,
    -- NUEVO CAMPO PARA EL RANKING
    puntaje_maximo_obtenido INT DEFAULT 0
);

-- INSERTO DE USUARIO ADMIN (ID: 1)
INSERT INTO usuario (nombre_completo, correo, password, rol)
VALUES ('admin', 'admin@test.com', '$2y$10$IFiN1ghfvGdg2vFHf7.wcethB0wCbUXCDXHAO0XCr4wGEmcrmn/5m', 'ADMIN');
-- INSERTO UN JUGADOR POR DEFECTO (ID: 2)
INSERT INTO usuario (nombre_completo, correo, password, rol)
VALUES ('jugador', 'jugador@test.com', '$2y$10$IFiN1ghfvGdg2vFHf7.wcethB0wCbUXCDXHAO0XCr4wGEmcrmn/5m', 'JUGADOR');

-- INSERTO UN EDITOR POR DEFECTO (ID: 3)
INSERT INTO usuario (nombre_completo, correo, password, rol)
VALUES ('editor', 'editor@test.com', '$2y$10$IFiN1ghfvGdg2vFHf7.wcethB0wCbUXCDXHAO0XCr4wGEmcrmn/5m', 'EDITOR');

-- TABLA PARTIDA (MODIFICADA para Partida Infinita)
CREATE TABLE partida(
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador INT NOT NULL,
    estado_partida VARCHAR(25), -- 'ACTIVA' o 'PERDIDA, POR TIEMPO'
    puntaje_final INT DEFAULT 0,
    -- COLUMNA MODIFICADA: Guarda IDs de preguntas jugadas (separados por comas)
    preguntas_jugadas VARCHAR(1000) DEFAULT '',
    fecha_creacion DATE,
    fecha_fin DATETIME,
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

-- agrego 20 preguntas

-- P26
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué año llegó el hombre a la Luna?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('1969', 26, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('1972', 26), ('1959', 26), ('1980', 26);

-- P27
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué civilización construyó las pirámides de Egipto?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Egipcia', 27, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Maya', 27), ('Inca', 27), ('Romana', 27);

-- P28
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país tiene la Torre Eiffel?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Francia', 28, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Italia', 28), ('España', 28), ('Alemania', 28);

-- P29
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el país más grande del mundo?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Rusia', 29, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('China', 29), ('Canadá', 29), ('Estados Unidos', 29);

-- P30
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué océano se encuentra entre África y Australia?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Océano Índico', 30, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Océano Atlántico', 30), ('Océano Pacífico', 30), ('Océano Ártico', 30);

-- P31
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién pintó "La noche estrellada"?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Vincent van Gogh', 31, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Claude Monet', 31), ('Salvador Dalí', 31), ('Diego Rivera', 31);

-- P32
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué museo se encuentra "La Mona Lisa"?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Museo del Louvre', 32, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Museo del Prado', 32), ('Museo de Arte Moderno', 32), ('Galería Uffizi', 32);

-- P33
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué movimiento artístico inició Dalí?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Surrealismo', 33, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Cubismo', 33), ('Impresionismo', 33), ('Barroco', 33);

-- P34
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué elemento químico tiene el símbolo O?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Oxígeno', 34, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Oro', 34), ('Osmio', 34), ('Ozono', 34);

-- P35
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es la velocidad de la luz?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('300.000 km/s', 35, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('150.000 km/s', 35), ('1.000 km/s', 35), ('3.000 km/s', 35);

-- P36
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué planeta es conocido como el planeta rojo?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Marte', 36, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Venus', 36), ('Júpiter', 36), ('Saturno', 36);

-- P37
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuántos huesos tiene el cuerpo humano adulto?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('206', 37, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('210', 37), ('198', 37), ('250', 37);

-- P38
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué deporte practica Roger Federer?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Tenis', 38, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Golf', 38), ('Fútbol', 38), ('Bádminton', 38);

-- P39
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuántos aros tiene el símbolo olímpico?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('5', 39, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('4', 39), ('6', 39), ('7', 39);

-- P40
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué deporte se utiliza un bate y una pelota?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Béisbol', 40, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Fútbol', 40), ('Hockey', 40), ('Tenis', 40);

-- P41
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país ganó el Mundial 2014?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Alemania', 41, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Brasil', 41), ('Argentina', 41), ('España', 41);

-- P42
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el país más poblado del mundo?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('China', 42, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('India', 42), ('Estados Unidos', 42), ('Indonesia', 42);

-- P43
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién escribió "Don Quijote de la Mancha"?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Miguel de Cervantes', 43, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Lope de Vega', 43), ('Garcilaso de la Vega', 43), ('Quevedo', 43);

-- P44
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el metal más ligero?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Litio', 44, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Plata', 44), ('Aluminio', 44), ('Cobre', 44);

-- P45
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué continente está Egipto?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('África', 45, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Asia', 45), ('Europa', 45), ('Oceanía', 45);

-- preguntas