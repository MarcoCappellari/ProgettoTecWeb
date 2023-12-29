
-- Ricrea le tabelle nell'ordine corretto
CREATE TABLE Utente (
    mail VARCHAR(50)  PRIMARY KEY,
    username VARCHAR(50),
    nome VARCHAR(50),
    cognome VARCHAR(50),
    permessi BOOLEAN,
    password VARCHAR(255)
);

CREATE TABLE Film (
    id INT PRIMARY KEY,
    nome VARCHAR(100),
    regista VARCHAR(100),
    durata INT,
    locandina BLOB,
    trama TEXT
);

CREATE TABLE Attori (
    id INT PRIMARY KEY,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    anni INT,
    genere VARCHAR(10)
);

CREATE TABLE Genere (
    nome_genere VARCHAR(50) PRIMARY KEY
);

CREATE TABLE Sala (
    id INT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE Posto (
    fila VARCHAR(1),
    numero_posto INT,
    id_sala INT,
    PRIMARY KEY (fila, numero_posto, id_sala),
    FOREIGN KEY (id_sala) REFERENCES Sala(id)
);

CREATE TABLE Riproduzione (
    id INT PRIMARY KEY,
    id_film INT,
    ora TIME,
    data DATE,
    FOREIGN KEY (id_film) REFERENCES Film(id)
);

CREATE TABLE Assegnazione (
    id_riproduzione INT,
    fila VARCHAR(1),
    numero_posto INT,
    disponibile BOOLEAN,
    id_sala INT,
    PRIMARY KEY (id_riproduzione, fila, numero_posto,id_sala),
    FOREIGN KEY (id_riproduzione) REFERENCES Riproduzione(id),
    FOREIGN KEY (fila, numero_posto,id_sala) REFERENCES Posto(fila, numero_posto,id_sala)
);

CREATE TABLE Partecipano (
    id_film INT,
    id_attore INT,
    PRIMARY KEY (id_film, id_attore),
    FOREIGN KEY (id_film) REFERENCES Film(id),
    FOREIGN KEY (id_attore) REFERENCES Attori(id)
);

CREATE TABLE Conforme (
    id_film INT,
    nome_genere VARCHAR(50),
    PRIMARY KEY (id_film, nome_genere),
    FOREIGN KEY (id_film) REFERENCES Film(id),
    FOREIGN KEY (nome_genere) REFERENCES Genere(nome_genere)
);

CREATE TABLE Biglietto (
    id INT PRIMARY KEY,
    id_riproduzione INT,
    id_utente VARCHAR(50),
    FOREIGN KEY (id_riproduzione) REFERENCES Riproduzione(id),
    FOREIGN KEY (id_utente) REFERENCES Utente(mail)
);

CREATE TABLE Recensioni (
    id INT PRIMARY KEY,
    testo TEXT,
    data_creazione TIMESTAMP,
    id_utente VARCHAR(50),
    FOREIGN KEY (id_utente) REFERENCES Utente(mail)
    
);
