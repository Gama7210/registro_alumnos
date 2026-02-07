
CREATE DATABASE IF NOT EXISTS registro_alumnos CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE registro_alumnos;

CREATE TABLE IF NOT EXISTS carreras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  abreviatura VARCHAR(10) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_carrera_nombre (nombre)
);

CREATE TABLE IF NOT EXISTS turnos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  inicial CHAR(1) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_turno_nombre (nombre)
);

CREATE TABLE IF NOT EXISTS grados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  grado INT NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_grado_valor (grado)
);

CREATE TABLE IF NOT EXISTS grupos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  carrera_id INT NOT NULL,
  turno_id INT NOT NULL,
  grado INT NOT NULL,
  seq INT NOT NULL,
  codigo VARCHAR(50) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (carrera_id) REFERENCES carreras(id),
  FOREIGN KEY (turno_id) REFERENCES turnos(id),
  UNIQUE KEY uq_grupo_codigo (codigo)
);

CREATE TABLE IF NOT EXISTS alumnos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  ape_paterno VARCHAR(100) NOT NULL,
  ape_materno VARCHAR(100) DEFAULT NULL,
  grupo_id INT NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (grupo_id) REFERENCES grupos(id)
);




INSERT IGNORE INTO turnos (nombre, inicial, activo) VALUES
  ('Matutino','M',1),
  ('Vespertino','V',1),
  ('Mixto','X',1);

INSERT IGNORE INTO grados (grado, activo) VALUES
  (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1);

INSERT IGNORE INTO carreras (nombre, abreviatura, activo) VALUES
  ('Ingeniería en Computación','ICP',1),
  ('Ingeniería Industrial','II',1),
  ('Ingeniería en Sistemas Computacionales','ISC',1),
  ('Ingeniería Mecatrónica','IME',1),
  ('Licenciatura en Administración','LAD',1),
  ('Licenciatura en Contaduría','LCO',1),
  ('Licenciatura en Derecho','LDE',1),
  ('Licenciatura en Pedagogía','LPD',1),
  ('Licenciatura en Psicología','LPS',1),
  ('Licenciatura en Comunicación','LCM',1),
  ('Licenciatura en Mercadotecnia','LME',1),
  ('Licenciatura en Negocios Internacionales','LNI',1),
  ('Licenciatura en Relaciones Públicas','LRP',1),
  ('Licenciatura en Arquitectura','LAR',1),
  ('Licenciatura en Diseño Gráfico','LDG',1),
  ('Licenciatura en Gastronomía','LGS',1),
  ('Licenciatura en Nutrición','LNT',1),
  ('Licenciatura en Cultura Física y Educación del Deporte','LCFD',1),
  ('Licenciatura en Enfermería','LEN',1),
  ('Licenciatura en Relaciones Internacionales','LRI',1);


select *from alumnos;

