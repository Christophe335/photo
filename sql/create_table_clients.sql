-- Table des clients
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    ville VARCHAR(50) NOT NULL,
    pays VARCHAR(50) DEFAULT 'France',
    newsletter TINYINT(1) DEFAULT 0,
    actif TINYINT(1) DEFAULT 0,
    token_activation VARCHAR(64),
    token_reset VARCHAR(64),
    token_reset_expiration DATETIME,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_clients_email ON clients(email);
CREATE INDEX idx_clients_actif ON clients(actif);
CREATE INDEX idx_clients_token_activation ON clients(token_activation);
CREATE INDEX idx_clients_token_reset ON clients(token_reset);

-- Table des sessions clients (optionnel, pour une gestion avancée des sessions)
CREATE TABLE IF NOT EXISTS client_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    session_id VARCHAR(128) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE INDEX idx_client_sessions_client_id ON client_sessions(client_id);
CREATE INDEX idx_client_sessions_expires_at ON client_sessions(expires_at);