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

CREATE TABLE pregunta_sugerida (
    id_pregunta_sugerida INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    pregunta VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    fecha_sugerencia DATETIME DEFAULT NOW(),
    estado VARCHAR(50) DEFAULT 'PENDIENTE', -- PENDIENTE / ACEPTADA / RECHAZADA
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE respuesta_sugerida (
    id_respuesta_sugerida INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta_sugerida INT NOT NULL,
    respuesta VARCHAR(255) NOT NULL,
    es_correcta BOOLEAN DEFAULT 0,
    FOREIGN KEY (id_pregunta_sugerida) REFERENCES pregunta_sugerida(id_pregunta_sugerida)
);

CREATE TABLE pregunta_reportada (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta INT NOT NULL,
    id_usuario INT NOT NULL,
    id_partida INT NOT NULL,
    motivo TEXT NOT NULL,
    fecha_reporte DATETIME DEFAULT NOW(),
    estado VARCHAR(50) DEFAULT 'PENDIENTE', -- PENDIENTE / REVISADA
    habilitada BOOLEAN DEFAULT 1,            -- 1 = habilitada, 0 = inhabilitada
    FOREIGN KEY (id_pregunta) REFERENCES pregunta(id_pregunta),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_partida) REFERENCES partida(id_partida)
);

CREATE TABLE respuesta_reportada (
    id_respuesta_reportada INT AUTO_INCREMENT PRIMARY KEY,
    id_reporte INT NOT NULL,
    id_respuesta INT NOT NULL,
    FOREIGN KEY (id_reporte) REFERENCES pregunta_reportada(id_reporte),
    FOREIGN KEY (id_respuesta) REFERENCES respuesta(id_respuesta)
);



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

-- P46
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién fue el creador del Imperio Romano?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Augusto', 46, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Julio César', 46), ('Nerón', 46), ('Trajano', 46);

-- P47
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué año terminó la Segunda Guerra Mundial?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('1945', 47, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('1939', 47), ('1950', 47), ('1940', 47);

-- P48
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién fue el libertador de Chile?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Bernardo O’Higgins', 48, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('José de San Martín', 48), ('Simón Bolívar', 48), ('Manuel Rodríguez', 48);

-- P49
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué muro cayó en 1989?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Muro de Berlín', 49, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Muro de China', 49), ('Muro de Jerusalén', 49), ('Muro de Adriano', 49);

-- P50
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué guerra enfrentó al norte y sur de Estados Unidos?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Guerra Civil', 50, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Guerra Fría', 50), ('Primera Guerra Mundial', 50), ('Guerra de Vietnam', 50);

-- P51
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el país más pequeño del mundo?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Ciudad del Vaticano', 51, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Mónaco', 51), ('San Marino', 51), ('Liechtenstein', 51);

-- P52
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es la montaña más alta de América?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Aconcagua', 52, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Chimborazo', 52), ('Huascarán', 52), ('McKinley', 52);

-- P53
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país tiene más islas en el mundo?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Suecia', 53, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Filipinas', 53), ('Indonesia', 53), ('Noruega', 53);

-- P54
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué río pasa por Egipto?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Nilo', 54, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Tigris', 54), ('Eúfrates', 54), ('Amazonas', 54);

-- P55
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es la capital de Canadá?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Ottawa', 55, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Toronto', 55), ('Vancouver', 55), ('Montreal', 55);

-- P56
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién pintó “Guernica”?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Pablo Picasso', 56, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Salvador Dalí', 56), ('Joan Miró', 56), ('Diego Velázquez', 56);

-- P57
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién escribió “Romeo y Julieta”?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('William Shakespeare', 57, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Charles Dickens', 57), ('Oscar Wilde', 57), ('Jane Austen', 57);

-- P58
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué técnica utilizaba Miguel Ángel para pintar?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Fresco', 58, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Óleo', 58), ('Acuarela', 58), ('Temple', 58);

-- P59
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué instrumento musical tiene teclas blancas y negras?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Piano', 59, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Violín', 59), ('Arpa', 59), ('Saxofón', 59);

-- P60
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué científico descubrió la penicilina?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Alexander Fleming', 60, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Louis Pasteur', 60), ('Isaac Newton', 60), ('Marie Curie', 60);

-- P61
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué planeta está más cerca del sol?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Mercurio', 61, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Venus', 61), ('Tierra', 61), ('Marte', 61);

-- P62
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué tipo de sangre es el donante universal?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('O negativo', 62, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('A positivo', 62), ('B negativo', 62), ('AB positivo', 62);

-- P63
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué instrumento mide la presión atmosférica?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Barómetro', 63, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Termómetro', 63), ('Anemómetro', 63), ('Higrómetro', 63);

-- P64
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué parte del cuerpo humano produce insulina?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Páncreas', 64, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Hígado', 64), ('Riñón', 64), ('Estómago', 64);

-- P65
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué deporte se juega con una raqueta y volante?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Bádminton', 65, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Tenis', 65), ('Ping pong', 65), ('Squash', 65);

-- P66
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuántos minutos dura un partido de fútbol?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('90', 66, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('80', 66), ('100', 66), ('60', 66);

-- P67
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué país se originaron los Juegos Olímpicos?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Grecia', 67, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Italia', 67), ('Francia', 67), ('Egipto', 67);

-- P68
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué selección ganó el Mundial 2010?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('España', 68, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Alemania', 68), ('Argentina', 68), ('Brasil', 68);

-- P69
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué deporte practica Lionel Messi?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Fútbol', 69, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Baloncesto', 69), ('Tenis', 69), ('Golf', 69);

-- P70
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país tiene como bandera una hoja de arce?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Canadá', 70, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Suiza', 70), ('Dinamarca', 70), ('Austria', 70);

-- P71
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué científico propuso las leyes del movimiento?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Isaac Newton', 71, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Galileo Galilei', 71), ('Albert Einstein', 71), ('Nikola Tesla', 71);

-- P72
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién fue el primer ser humano en el espacio?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Yuri Gagarin', 72, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Neil Armstrong', 72), ('Buzz Aldrin', 72), ('Laika', 72);

-- P73
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué país se encuentra la Torre de Pisa?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Italia', 73, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Francia', 73), ('España', 73), ('Alemania', 73);

-- P74
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué músico es conocido como "El Rey del Rock"?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Elvis Presley', 74, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Frank Sinatra', 74), ('John Lennon', 74), ('Bob Dylan', 74);

-- P75
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país ganó la primera Copa del Mundo de fútbol?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Uruguay', 75, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Argentina', 75), ('Italia', 75), ('Brasil', 75);

-- P76
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país es conocido como la tierra del sol naciente?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Japón', 76, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('China', 76), ('Corea del Sur', 76), ('Tailandia', 76);

-- P77
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién pintó la Mona Lisa?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Leonardo da Vinci', 77, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Miguel Ángel', 77), ('Rafael', 77), ('Donatello', 77);

-- P78
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué elemento químico tiene el símbolo O?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Oxígeno', 78, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Oro', 78), ('Osmio', 78), ('Ozono', 78);

-- P79
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el océano más grande del mundo?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Pacífico', 79, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Atlántico', 79), ('Índico', 79), ('Ártico', 79);

-- P80
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién desarrolló la teoría de la relatividad?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Albert Einstein', 80, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Isaac Newton', 80), ('Galileo Galilei', 80), ('Stephen Hawking', 80);

-- P81
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el idioma más hablado del mundo?', '-', 'CULTURA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Inglés', 81, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Mandarín', 81), ('Español', 81), ('Hindi', 81);

-- P82
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué gas respiramos para vivir?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Oxígeno', 82, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Dióxido de carbono', 82), ('Hidrógeno', 82), ('Helio', 82);

-- P83
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué instrumento mide la temperatura?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Termómetro', 83, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Barómetro', 83), ('Higrómetro', 83), ('Anemómetro', 83);

-- P84
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién fue el primer hombre en pisar la Luna?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Neil Armstrong', 84, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Buzz Aldrin', 84), ('Yuri Gagarin', 84), ('Alan Shepard', 84);

-- P85
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué continente no tiene serpientes?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Antártida', 85, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Europa', 85), ('Oceanía', 85), ('Asia', 85);

-- P86
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién escribió "Cien años de soledad"?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Gabriel García Márquez', 86, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Mario Vargas Llosa', 86), ('Julio Cortázar', 86), ('Pablo Neruda', 86);

-- P87
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué país se encuentra el Taj Mahal?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('India', 87, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Pakistán', 87), ('Bangladés', 87), ('Nepal', 87);

-- P88
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué planeta es conocido como el planeta rojo?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Marte', 88, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Júpiter', 88), ('Venus', 88), ('Saturno', 88);

-- P89
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué famoso científico formuló las leyes de la gravedad?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Isaac Newton', 89, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Albert Einstein', 89), ('Galileo Galilei', 89), ('Copérnico', 89);

-- P90
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué año llegó Cristóbal Colón a América?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('1492', 90, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('1500', 90), ('1485', 90), ('1512', 90);

-- P91
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el país más grande del mundo?', '-', 'GEOGRAFIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Rusia', 91, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('China', 91), ('Canadá', 91), ('Estados Unidos', 91);

-- P92
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué jugador es conocido como “O Rei”?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Pelé', 92, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Maradona', 92), ('Zidane', 92), ('Ronaldinho', 92);

-- P93
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué animal es el símbolo nacional de Australia?', '-', 'CULTURA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Canguro', 93, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Koala', 93), ('Emú', 93), ('Dingo', 93);

-- P94
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué sustancia da el color verde a las plantas?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Clorofila', 94, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Celulosa', 94), ('Glucosa', 94), ('Savia', 94);

-- P95
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Quién escribió “Don Quijote de la Mancha”?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Miguel de Cervantes', 95, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Lope de Vega', 95), ('Francisco de Quevedo', 95), ('Góngora', 95);

-- P96
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿En qué deporte se utiliza una red alta y una pelota ligera?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Vóley', 96, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Tenis', 96), ('Balonmano', 96), ('Bádminton', 96);

-- P97
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país inventó el papel?', '-', 'HISTORIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('China', 97, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Egipto', 97), ('India', 97), ('Grecia', 97);

-- P98
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Cuál es el metal más ligero?', '-', 'CIENCIA', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Litio', 98, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Aluminio', 98), ('Sodio', 98), ('Magnesio', 98);

-- P99
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué país organizó los Juegos Olímpicos de 2016?', '-', 'DEPORTES', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Brasil', 99, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('China', 99), ('Reino Unido', 99), ('Japón', 99);

-- P100
INSERT INTO pregunta (pregunta, dificultad, categoria, puntaje, fecha_creacion) VALUES
('¿Qué instrumento usa cuerdas y arco para producir sonido?', '-', 'ARTE', 5, CURDATE());
INSERT INTO respuesta (respuesta, id_pregunta, es_correcta) VALUES ('Violín', 100, 1);
INSERT INTO respuesta (respuesta, id_pregunta) VALUES ('Guitarra', 100), ('Arpa', 100), ('Chelo', 100);
