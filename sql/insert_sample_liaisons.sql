-- Insert d'exemple pour personnalisation
CREATE TABLE IF NOT EXISTS personnalisation_liaisons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produit_ref VARCHAR(100) NOT NULL,
  ref_pre_encollage VARCHAR(100) DEFAULT NULL,
  ref_impression VARCHAR(100) DEFAULT NULL,
  type VARCHAR(32) NOT NULL DEFAULT 'imprime',
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO personnalisation_liaisons (produit_ref, ref_impression) VALUES
('WAUSCLA0001','TFM210x297'),
('WAUSCLA0002','TFM210x297');
