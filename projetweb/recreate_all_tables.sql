DROP TABLE IF EXISTS notes, controles, section_documents, documents, sections, plannings, annonces, cours_users, cours, users, formations, reactions, comments, posts;

-- Formations
CREATE TABLE formations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  intitule VARCHAR(50) NOT NULL,
  created_at DATETIME,
  updated_at DATETIME
);

-- Utilisateurs
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(40),
  prenom VARCHAR(40),
  login VARCHAR(30) NOT NULL UNIQUE,
  mdp VARCHAR(60) NOT NULL,
  formation_id INT,
  type VARCHAR(10),
  CONSTRAINT chk_type CHECK (type IN ('etudiant','enseignant','admin')),
  FOREIGN KEY (formation_id) REFERENCES formations(id)
);

-- Cours
CREATE TABLE cours (
  id INT AUTO_INCREMENT PRIMARY KEY,
  intitule VARCHAR(50) NOT NULL,
  user_id INT NOT NULL,
  formation_id INT,
  created_at DATETIME,
  updated_at DATETIME,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (formation_id) REFERENCES formations(id)
);

-- Table pivot cours_users
CREATE TABLE cours_users (
  cours_id INT NOT NULL,
  user_id INT NOT NULL,
  PRIMARY KEY (cours_id, user_id),
  FOREIGN KEY (cours_id) REFERENCES cours(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Annonces
CREATE TABLE annonces (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT NOT NULL,
  titre TEXT NOT NULL,
  contenu TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);

-- Plannings
CREATE TABLE plannings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT NOT NULL,
  date_debut DATETIME,
  date_fin DATETIME,
  FOREIGN KEY (cours_id) REFERENCES cours(id)
);

-- Sections
CREATE TABLE sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT NOT NULL,
  titre TEXT NOT NULL,
  ordre INT DEFAULT 0,
  FOREIGN KEY (cours_id) REFERENCES cours(id)
);

-- Documents
CREATE TABLE documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT NOT NULL,
  section_id INT,
  titre TEXT NOT NULL,
  fichier TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE,
  FOREIGN KEY (section_id) REFERENCES sections(id)
);

-- Section_documents
CREATE TABLE section_documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT,
  titre TEXT NOT NULL
);

-- Contrôles
CREATE TABLE controles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cours_id INT NOT NULL,
  titre VARCHAR(255) NOT NULL,
  description TEXT,
  date_controle DATE NOT NULL,
  coefficient FLOAT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);

-- Notes
CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  controle_id INT NOT NULL,
  user_id INT NOT NULL,
  note FLOAT NOT NULL,
  commentaire TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (controle_id) REFERENCES controles(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX idx_notes_controle_user ON notes (controle_id, user_id);

-- Posts
CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  titre TEXT NOT NULL,
  contenu TEXT NOT NULL,
  reactions_count INT DEFAULT 0,
  comments_count INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  parent_id INT,
  contenu TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Reactions
CREATE TABLE reactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  emoji TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
