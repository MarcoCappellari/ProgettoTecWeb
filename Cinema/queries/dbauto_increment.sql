-- Utente
CREATE TABLE Utente (
    mail VARCHAR(50) PRIMARY KEY,
    username VARCHAR(50) unique NOT NULL,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    permessi BOOLEAN NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Film
CREATE TABLE Film (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titolo VARCHAR(100),
    regista VARCHAR(100),
    durata INT,
    locandina VARCHAR(100),
    trama TEXT
);

-- Genere
CREATE TABLE Genere (
    nome VARCHAR(50) PRIMARY KEY
);

-- Sala
CREATE TABLE Sala (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    TecVideo VARCHAR(50),
    TecAudio VARCHAR(50)
);

-- Posto
CREATE TABLE Posto (
    fila VARCHAR(1),
    numero_posto INT,
    id_sala INT,
    PRIMARY KEY (fila, numero_posto, id_sala),
    FOREIGN KEY (id_sala) REFERENCES Sala(id)
);

-- Proiezione
CREATE TABLE Proiezione (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_film INT,
    ora TIME,
    id_sala INT,
    data DATE,
    FOREIGN KEY (id_film) REFERENCES Film(id),
    FOREIGN KEY (id_sala) REFERENCES Sala(id)
);


-- Conforme
CREATE TABLE Classificazione (
    id_film INT,
    nome_genere VARCHAR(50),
    PRIMARY KEY (id_film, nome_genere),
    FOREIGN KEY (id_film) REFERENCES Film(id),
    FOREIGN KEY (nome_genere) REFERENCES Genere(nome)
);

-- Biglietto
CREATE TABLE Biglietto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_proiezione INT,
    id_utente VARCHAR(50),
    fila VARCHAR(1),
    numero_posto INT,
    id_sala INT,
    FOREIGN KEY (id_proiezione) REFERENCES Proiezione(id),
    FOREIGN KEY (id_utente) REFERENCES Utente(mail),
    FOREIGN KEY (fila, numero_posto, id_sala) REFERENCES Posto(fila, numero_posto, id_sala)
);

-- Recensioni
CREATE TABLE Recensioni (
    id INT PRIMARY KEY AUTO_INCREMENT,
    testo TEXT,
    data_creazione TIMESTAMP,
    id_utente VARCHAR(50),
    FOREIGN KEY (id_utente) REFERENCES Utente(mail)
);
