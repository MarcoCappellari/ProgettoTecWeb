-- Utente
CREATE TABLE Utente (
    mail VARCHAR(50) PRIMARY KEY,
    username VARCHAR(50),
    nome VARCHAR(50),
    cognome VARCHAR(50),
    permessi BOOLEAN,
    password VARCHAR(255)
);

-- Film
CREATE TABLE Film (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    regista VARCHAR(100),
    durata INT,
    locandina BLOB,
    trama TEXT
);

-- Attori
CREATE TABLE Attori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    anni INT,
    genere VARCHAR(10)
);

-- Genere
CREATE TABLE Genere (
    nome_genere VARCHAR(50) PRIMARY KEY
);

-- Sala
CREATE TABLE Sala (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL
);

-- Posto
CREATE TABLE Posto (
    fila VARCHAR(1),
    numero_posto INT,
    id_sala INT,
    PRIMARY KEY (fila, numero_posto, id_sala),
    FOREIGN KEY (id_sala) REFERENCES Sala(id)
);

-- Riproduzione
CREATE TABLE Riproduzione (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_film INT,
    ora TIME,
    data DATE,
    FOREIGN KEY (id_film) REFERENCES Film(id)
);

-- Assegnazione
CREATE TABLE Assegnazione (
    id_riproduzione INT,
    fila VARCHAR(1),
    numero_posto INT,
    disponibile BOOLEAN,
    id_sala INT,
    PRIMARY KEY (id_riproduzione, fila, numero_posto, id_sala),
    FOREIGN KEY (id_riproduzione) REFERENCES Riproduzione(id),
    FOREIGN KEY (fila, numero_posto, id_sala) REFERENCES Posto(fila, numero_posto, id_sala)
);

-- Partecipano
CREATE TABLE Partecipano (
    id_film INT,
    id_attore INT,
    PRIMARY KEY (id_film, id_attore),
    FOREIGN KEY (id_film) REFERENCES Film(id),
    FOREIGN KEY (id_attore) REFERENCES Attori(id)
);

-- Conforme
CREATE TABLE Conforme (
    id_film INT,
    nome_genere VARCHAR(50),
    PRIMARY KEY (id_film, nome_genere),
    FOREIGN KEY (id_film) REFERENCES Film(id),
    FOREIGN KEY (nome_genere) REFERENCES Genere(nome_genere)
);

-- Biglietto
CREATE TABLE Biglietto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_riproduzione INT,
    id_utente VARCHAR(50),
    FOREIGN KEY (id_riproduzione) REFERENCES Riproduzione(id),
    FOREIGN KEY (id_utente) REFERENCES Utente(mail)
);

-- Recensioni
CREATE TABLE Recensioni (
    id INT PRIMARY KEY AUTO_INCREMENT,
    testo TEXT,
    data_creazione TIMESTAMP,
    id_utente VARCHAR(50),
    FOREIGN KEY (id_utente) REFERENCES Utente(mail)
);
