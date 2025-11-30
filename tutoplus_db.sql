CREATE DATABASE IF NOT EXISTS tutoplus_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE tutoplus_db;

-- TABLE ÉTUDIANTS
CREATE TABLE etudiants (
    id_etudiant INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    programme VARCHAR(100) DEFAULT 'Techniques de l’informatique',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE TUTEURS
CREATE TABLE tuteurs (
    id_tuteur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    specialite VARCHAR(150),
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE SERVICES (peut avoir plusieurs tuteurs)
CREATE TABLE services (
    id_service INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(150) NOT NULL
);

-- TABLE SERVICE <-> TUTEUR
CREATE TABLE service_tuteurs (
    id_service INT NOT NULL,
    id_tuteur INT NOT NULL,
    PRIMARY KEY (id_service, id_tuteur),
    FOREIGN KEY (id_service) REFERENCES services(id_service) ON DELETE CASCADE,
    FOREIGN KEY (id_tuteur) REFERENCES tuteurs(id_tuteur) ON DELETE CASCADE
);

-- TABLE RENDEZ-VOUS
CREATE TABLE rendez_vous (
    id_rdv INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_tuteur INT NOT NULL,
    id_service INT NULL,
    date_rdv DATE NOT NULL,
    heure TIME NOT NULL,
    duree DECIMAL(4,2) DEFAULT 1.50,
    statut ENUM('en_attente', 'confirmé', 'annulé') DEFAULT 'en_attente',
    salle VARCHAR(50) DEFAULT 'Biblio B-101',
    commentaire TEXT,

    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (id_tuteur) REFERENCES tuteurs(id_tuteur) ON DELETE CASCADE,
    FOREIGN KEY (id_service) REFERENCES services(id_service) ON DELETE SET NULL
);

-- ======================================================================
-- Insertions ÉTUDIANTS 
-- ======================================================================

INSERT INTO etudiants (nom, prenom, email) VALUES
('Agha', 'Adam', 'adam.agha@gmail.ca'),
('Zhao', 'Siyuan', 'siyuan.zhao@gmail.ca'),
('Qin', 'Lucas', 'lucas.qin@gmail.ca');

-- ======================================================================
-- Insertions TUTEURS
-- ======================================================================

INSERT INTO tuteurs (nom, prenom, specialite, email, telephone) VALUES
('Klein', 'Robert', 'Programmation orientée objet', 'robert.klein@gmail.ca', '514-555-1001'),
('Smith', 'Julie', 'Développement Web', 'julie.smith@gmail.ca', '514-555-1002'),
('Doe', 'John', 'Bases de données (SQL)', 'john.doe@gmail.ca', '514-555-1003');

-- ======================================================================
-- Insertions SERVICES
-- ======================================================================

INSERT INTO services (nom_service) VALUES
('Programmation orientée objet'),
('Développement Web'),
('Bases de données (SQL)'),
('Mathématique'),
('Réseautique'),
('Accompagnement sur les TP / Devoirs');

-- Lier services → plusieurs tuteurs
INSERT INTO service_tuteurs VALUES
(1, 1), (1, 2),     -- Service 1 -> tuteurs 1 et 2
(2, 2),             -- Service 2 -> tuteur 2
(3, 1), (3, 3),     -- Service 3 -> tuteurs 1 et 3
(4, 1), (4, 2),
(5, 2),
(6, 3);

-- ======================================================================
-- Insertions RENDEZ-VOUS
-- ======================================================================

INSERT INTO rendez_vous (id_etudiant, id_tuteur, id_service, date_rdv, heure, duree, statut, salle, commentaire)
VALUES
(1, 1, 1, '2025-11-17', '09:30:00', 1.50, 'confirmé', 'Biblio B-101', 'Révision Java'),
(1, 2, 2, '2025-11-19', '14:00:00', 1.50, 'confirmé', 'Salle A-204', 'TP HTML/CSS'),
(2, 3, 3, '2025-11-21', '10:15:00', 1.50, 'en_attente', 'Local C-310', 'Exercices SQL'),
(3, 1, 1, '2025-11-20', '13:00:00', 1.50, 'confirmé', 'Biblio B-101', 'Préparation examen POO');
