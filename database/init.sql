USE bibliotheque_db;

CREATE TABLE IF NOT EXISTS livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE
);

INSERT INTO livres (titre, auteur, disponible) VALUES 
('Le Petit Prince', 'Antoine de Saint-Exupéry', 1),
('1984', 'George Orwell', 1),
('Le Seigneur des Anneaux', 'J.R.R. Tolkien', 0),
('Le Docker pour les Nuls', 'Expert Docker', 1);