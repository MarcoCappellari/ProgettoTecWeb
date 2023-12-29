-- Inserisci dati nella tabella Utente
INSERT INTO Utente (username, nome, cognome, mail, permessi, password) VALUES 
('user1', 'Mario', 'Rossi', 'mario@email.com', 1, 'password123'),
('user2', 'Luca', 'Bianchi', 'luca@email.com', 0, 'securepass');

-- Inserisci dati nella tabella Film
INSERT INTO Film (id, nome, regista, durata, locandina, trama) VALUES 
(1, 'Il Gladiatore', 'Benjamin Renner', 92, NULL, 'Trama del film...'),
(2, 'Prendi il volo', 'Ridley Scott', 92, NULL, 'La famiglia Mallard è intrappolata nella sua routine. Mentre papà Mack è felice di mantenere la sua famiglia al sicuro navigando all infinito nel loro stagno del New England, mamma Pam è intenzionata a dare una scossa alla loro vita e mostrare ai loro figli - il figlio adolescente Dax e la papera Gwen - il mondo intero.'),
(3, 'Inception', 'Christopher Nolan', 148, NULL, 'Trama del film...');

-- Inserisci dati nella tabella Attori
INSERT INTO Attori (id, nome, cognome, anni, genere) VALUES 
(1, 'Russell', 'Crowe', 57, 'Maschile'),
(2, 'Carol', 'Kane', 71, 'Femminile'),
(3, 'Danny', ' DeVito', 79, 'Maschile'),
(4, 'Leonardo', 'DiCaprio', 47, 'Maschile');

-- Inserisci dati nella tabella Genere
INSERT INTO Genere (nome_genere) VALUES 
('Azione'),
('Animazione'),
('Drammatico');

-- Inserisci dati nella tabella Sala
INSERT INTO Sala (id, nome) VALUES 
(1, 'Sala 1'),
(2, 'Sala 2'),
(3, 'Sala 3');

-- Inserisci dati nella tabella Posto
INSERT INTO Posto (fila, numero_posto, id_sala) VALUES 
('A', 1, 1),
('A', 2, 1),
('A', 3, 1),
('A', 4, 1),
('A', 5, 1),
('A', 6, 1),
('A', 7, 1),
('A', 8, 1),
('A', 9, 1),
('A', 10, 1),
('B', 1, 1),
('B', 2, 1),
('B', 3, 1),
('B', 4, 1),
('B', 5, 1),
('B', 6, 1),
('B', 7, 1),
('B', 8, 1),
('B', 9, 1),
('B', 10, 1),
('C', 1, 1),
('C', 2, 1),
('C', 3, 1),
('C', 4, 1),
('C', 5, 1),
('C', 6, 1),
('C', 7, 1),
('C', 8, 1),
('C', 9, 1),
('C', 10, 1),
('D', 1, 1),
('D', 2, 1),
('D', 3, 1),
('D', 4, 1),
('D', 5, 1),
('D', 6, 1),
('D', 7, 1),
('D', 8, 1),
('D', 9, 1),
('D', 10, 1),
('E', 1, 1),
('E', 2, 1),
('E', 3, 1),
('E', 4, 1),
('E', 5, 1),
('E', 6, 1),
('E', 7, 1),
('E', 8, 1),
('E', 9, 1),
('E', 10, 1),
('A', 1, 2),
('A', 2, 2),
('A', 3, 2),
('A', 4, 2),
('A', 5, 2),
('A', 6, 2),
('A', 7, 2),
('A', 8, 2),
('A', 9, 2),
('A', 10, 2),
('B', 1, 2),
('B', 2, 2),
('B', 3, 2),
('B', 4, 2),
('B', 5, 2),
('B', 6, 2),
('B', 7, 2),
('B', 8, 2),
('B', 9, 2),
('B', 10, 2),
('C', 1, 2),
('C', 2, 2),
('C', 3, 2),
('C', 4, 2),
('C', 5, 2),
('C', 6, 2),
('C', 7, 2),
('C', 8, 2),
('C', 9, 2),
('C', 10, 2),
('D', 1, 2),
('D', 2, 2),
('D', 3, 2),
('D', 4, 2),
('D', 5, 2),
('D', 6, 2),
('D', 7, 2),
('D', 8, 2),
('D', 9, 2),
('D', 10, 2),
('E', 1, 2),
('E', 2, 2),
('E', 3, 2),
('E', 4, 2),
('E', 5, 2),
('E', 6, 2),
('E', 7, 2),
('E', 8, 2),
('E', 9, 2),
('E', 10, 2),
('A', 1, 3),
('A', 2, 3),
('A', 3, 3),
('A', 4, 3),
('A', 5, 3),
('A', 6, 3),
('A', 7, 3),
('A', 8, 3),
('A', 9, 3),
('A', 10, 3),
('B', 1, 3),
('B', 2, 3),
('B', 3, 3),
('B', 4, 3),
('B', 5, 3),
('B', 6, 3),
('B', 7, 3),
('B', 8, 3),
('B', 9, 3),
('B', 10, 3),
('C', 1, 3),
('C', 2, 3),
('C', 3, 3),
('C', 4, 3),
('C', 5, 3),
('C', 6, 3),
('C', 7, 3),
('C', 8, 3),
('C', 9, 3),
('C', 10, 3),
('D', 1, 3),
('D', 2, 3),
('D', 3, 3),
('D', 4, 3),
('D', 5, 3),
('D', 6, 3),
('D', 7, 3),
('D', 8, 3),
('D', 9, 3),
('D', 10, 3),
('E', 1, 3),
('E', 2, 3),
('E', 3, 3),
('E', 4, 3),
('E', 5, 3),
('E', 6, 3),
('E', 7, 3),
('E', 8, 3),
('E', 9, 3),
('E', 10, 3);





-- Inserisci dati nella tabella Riproduzione
INSERT INTO Riproduzione (id, id_film, ora, data) VALUES 
(1, 2, '21:00:00', '2023-12-20'),
(2, 2, '19:00:00', '2023-12-21'),
(3, 2, '23:00:00', '2023-12-20');

-- Inserisci dati nella tabella Assegnazione
INSERT INTO Assegnazione (id_riproduzione, fila, numero_posto, disponibile, id_sala) VALUES 
(1, 'A', 1, 1, 1),
(1, 'A', 2, 0, 1),
(1, 'A', 3, 0, 1),
(1, 'A', 4, 1, 1),
(1, 'A', 5, 0, 1),
(1, 'A', 6, 1, 1),
(1, 'A', 7, 0, 1),
(1, 'A', 8, 1, 1),
(1, 'A', 9, 0, 1),
(1, 'A', 10, 1, 1),
(1, 'B', 1, 0, 1),
(1, 'B', 2, 1, 1),
(1, 'B', 3, 0, 1),
(1, 'B', 4, 1, 1),
(1, 'B', 5, 0, 1),
(1, 'B', 6, 1, 1),
(1, 'B', 7, 0, 1),
(1, 'B', 8, 1, 1),
(1, 'B', 9, 0, 1),
(1, 'B', 10, 1, 1),
(1, 'C', 1, 0, 1),
(1, 'C', 2, 1, 1),
(1, 'C', 3, 0, 1),
(1, 'C', 4, 1, 1),
(1, 'C', 5, 0, 1),
(1, 'C', 6, 1, 1),
(1, 'C', 7, 0, 1),
(1, 'C', 8, 1, 1),
(1, 'C', 9, 0, 1),
(1, 'C', 10, 1, 1),
(1, 'D', 1, 0, 1),
(1, 'D', 2, 1, 1),
(1, 'D', 3, 0, 1),
(1, 'D', 4, 1, 1),
(1, 'D', 5, 0, 1),
(1, 'D', 6, 1, 1),
(1, 'D', 7, 0, 1),
(1, 'D', 8, 1, 1),
(1, 'D', 9, 0, 1),
(1, 'D', 10, 1, 1),
(1, 'E', 1, 0, 1),
(1, 'E', 2, 1, 1),
(1, 'E', 3, 0, 1),
(1, 'E', 4, 1, 1),
(1, 'E', 5, 0, 1),
(1, 'E', 6, 1, 1),
(1, 'E', 7, 0, 1),
(1, 'E', 8, 1, 1),
(1, 'E', 9, 0, 1),
(1, 'E', 10, 1, 1);




-- Inserisci dati nella tabella Partecipano
INSERT INTO Partecipano (id_film, id_attore) VALUES 
(1, 1),
(2, 1),
(2, 2);

-- Inserisci dati nella tabella Conforme
INSERT INTO Conforme (id_film, nome_genere) VALUES 
(1, 'Azione'),
(2, 'Drammatico');

-- Inserisci dati nella tabella Biglietto
INSERT INTO Biglietto (id, id_riproduzione, id_utente) VALUES 
(1, 1, 'mario@email.com'),
(2, 2, 'luca@email.com');

-- Inserisci dati nella tabella Recensioni
INSERT INTO Recensioni (id, testo, data_creazione, id_utente) VALUES 
(1, 'Bel film!', NOW(), 'mario@email.com'),
(2, 'Molto interessante', NOW(), 'luca@email.com');
