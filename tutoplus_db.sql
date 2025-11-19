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

-- TABLE RENDEZ-VOUS
CREATE TABLE rendez_vous (
    id_rdv INT AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT NOT NULL,
    id_tuteur INT NOT NULL,

    date_rdv DATE NOT NULL,
    heure TIME NOT NULL,
    duree DECIMAL(4,2) DEFAULT 1.50,
    statut ENUM('en_attente', 'confirmé', 'annulé') DEFAULT 'en_attente',

    salle VARCHAR(50) DEFAULT 'Biblio B-101',
    commentaire TEXT,

    FOREIGN KEY (id_etudiant) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (id_tuteur) REFERENCES tuteurs(id_tuteur) ON DELETE CASCADE,

    INDEX idx_date (date_rdv),
    INDEX idx_etudiant (id_etudiant),
    INDEX idx_tuteur (id_tuteur)
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
('Doe', 'John', 'Bases de données', 'john.doe@gmail.ca', '514-555-1003');

-- ======================================================================
-- Insertions RENDEZ-VOUS
-- ======================================================================

INSERT INTO rendez_vous (id_etudiant, id_tuteur, date_rdv, heure, duree, statut, salle, commentaire)
VALUES
(1, 1, '2025-11-17', '09:30:00', 1.50, 'confirmé', 'Biblio B-101', 'Révision Java');

INSERT INTO rendez_vous (id_etudiant, id_tuteur, date_rdv, heure, duree, statut, salle, commentaire)
VALUES
(1, 2, '2025-11-19', '14:00:00', 1.50, 'confirmé', 'Salle A-204', 'TP HTML/CSS');

INSERT INTO rendez_vous (id_etudiant, id_tuteur, date_rdv, heure, duree, statut, salle, commentaire)
VALUES
(2, 3, '2025-11-21', '10:15:00', 1.50, 'en_attente', 'Local C-310', 'Exercices SQL');

INSERT INTO rendez_vous (id_etudiant, id_tuteur, date_rdv, heure, duree, statut, salle, commentaire)
VALUES
(3, 1, '2025-11-20', '13:00:00', 1.50, 'confirmé', 'Biblio B-101', 'Préparation examen POO');
