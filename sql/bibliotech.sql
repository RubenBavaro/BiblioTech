SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ruolo ENUM('studente','bibliotecario') NOT NULL,
    totp_secret VARCHAR(32) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    failed_attempts INT NOT NULL DEFAULT 0,
    last_failed_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255) NOT NULL,
    autore VARCHAR(255) NOT NULL,
    copie_totali INT NOT NULL CHECK (copie_totali >= 0),
    copie_disponibili INT NOT NULL CHECK (copie_disponibili >= 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_copie_coerenza 
        CHECK (copie_disponibili <= copie_totali)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE prestiti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    id_libro INT NOT NULL,
    data_prestito DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_restituzione DATETIME NULL,
    quantita INT NOT NULL DEFAULT 1 CHECK (quantita > 0),
    CONSTRAINT fk_prestito_utente
        FOREIGN KEY (id_utente)
        REFERENCES utenti(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_prestito_libro
        FOREIGN KEY (id_libro)
        REFERENCES libri(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE sessioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    session_token VARCHAR(128) NOT NULL UNIQUE,
    login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    logout_time DATETIME NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    CONSTRAINT fk_sessione_utente
        FOREIGN KEY (id_utente)
        REFERENCES utenti(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    token VARCHAR(128) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reset_utente
        FOREIGN KEY (id_utente)
        REFERENCES utenti(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE INDEX idx_prestiti_utente ON prestiti(id_utente);
CREATE INDEX idx_prestiti_libro ON prestiti(id_libro);
CREATE INDEX idx_sessioni_utente ON sessioni(id_utente);
CREATE INDEX idx_reset_utente ON password_reset_tokens(id_utente);



-- Password originale: 'Studente123!'
-- Hash bcrypt: $2a$12$umpKwHrD1O8kTVoXFbeVZe2TCzxBVN4LfoZ16TeXL7.7AIx6Wc68i

INSERT INTO utenti 
(email, password, ruolo, totp_secret, is_active, failed_attempts)
VALUES
('studente1@test.it',
 '$2a$12$umpKwHrD1O8kTVoXFbeVZe2TCzxBVN4LfoZ16TeXL7.7AIx6Wc68i',
 'studente',
 'JBSWY3DPEHPK3PXP',
 1,
 0),

('studente2@test.it',
 '$2a$12$umpKwHrD1O8kTVoXFbeVZe2TCzxBVN4LfoZ16TeXL7.7AIx6Wc68i',
 'studente',
 'KRSXG5DSNFXGOIDM',
 1,
 0),

('studente3@test.it',
 '$2a$12$umpKwHrD1O8kTVoXFbeVZe2TCzxBVN4LfoZ16TeXL7.7AIx6Wc68i',
 'studente',
 'MZXW6YTBOIYTEMZT',
 1,
 0),

-- Password originale: 'Biblioteca123!'
-- Hash bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

('bibliotecario@test.it',
 '$2a$12$vEKOOqOYxwsSFYiWJD9pdOJHmNmW3TA1HxDdlB2dFoS8rx4..0Urm',
 'bibliotecario',
 'NB2W45DFOIZA4YTF',
 1,
 0);




INSERT INTO libri 
(titolo, autore, copie_totali, copie_disponibili)
VALUES
('1984', 'George Orwell', 5, 4),  -- 5 totali, 4 disponibili (1 in prestito)
('Il Nome della Rosa', 'Umberto Eco', 4, 4),
('I Promessi Sposi', 'Alessandro Manzoni', 6, 6),
('Il Signore degli Anelli', 'J.R.R. Tolkien', 3, 2),  -- 3 totali, 2 disponibili (1 in prestito)
('La Divina Commedia', 'Dante Alighieri', 7, 7);


INSERT INTO prestiti
(id_utente, id_libro, data_prestito)
VALUES
(1, 1, NOW()),  -- Studente1 prende in prestito "1984" (libro ID 1)
(2, 4, NOW());  -- Studente2 prende in prestito "Il Signore degli Anelli" (libro ID 4)

SELECT '✅ Database BiblioTech creato con successo!' AS Messaggio;

SELECT 'UTENTI CREATI:' AS Info;
SELECT email, ruolo FROM utenti ORDER BY ruolo, id;

SELECT '' AS '';
SELECT 'CREDENZIALI DI ACCESSO:' AS Info;
SELECT 'studente1@test.it - Password: Studente123!' AS Utente_1;
SELECT 'studente2@test.it - Password: Studente123!' AS Utente_2;
SELECT 'studente3@test.it - Password: Studente123!' AS Utente_3;
SELECT 'bibliotecario@test.it - Password: Biblioteca123!' AS Bibliotecario;

SELECT '' AS '';
SELECT 'TOTP SECRETS:' AS Info;
SELECT 'studente1@test.it → JBSWY3DPEHPK3PXP' AS TOTP_1;
SELECT 'studente2@test.it → KRSXG5DSNFXGOIDM' AS TOTP_2;
SELECT 'studente3@test.it → MZXW6YTBOIYTEMZT' AS TOTP_3;
SELECT 'bibliotecario@test.it → NB2W45DFOIZA4YTF' AS TOTP_4;

SELECT '' AS '';
SELECT 'LIBRI NEL CATALOGO:' AS Info;
SELECT COUNT(*) AS Totale_Libri FROM libri;
SELECT SUM(copie_totali) AS Copie_Totali FROM libri;
SELECT SUM(copie_disponibili) AS Copie_Disponibili FROM libri;