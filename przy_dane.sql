INSERT INTO zespol (nazwa, miasto, rok_zalozenia, trener_glowny)
VALUES
 ('Los Angeles Lakers','Los Angeles',1947,'Darvin Ham'),
 ('Brooklyn Nets','Brooklyn',1967,'Jacque Vaughn'),
 ('Chicago Bulls','Chicago',1966,'Billy Donovan');

INSERT INTO gracz (imie, nazwisko, pozycja, data_urodzenia, id_zespolu)
VALUES
 ('LeBron','James','SF','1984-12-30', 1),
 ('Anthony','Davis','C','1993-03-11', 1),
 ('Kevin','Durant','SF','1988-09-29', 2),
 ('Zach','LaVine','SG','1995-03-10', 3);

INSERT INTO mecz (data_meczu, id_gospodarza, id_goscia, wynik_gospodarza, wynik_goscia)
VALUES ('2025-11-01', 1, 2, 112, 108);

INSERT INTO statystyki_meczu (id_meczu, id_gracza, minuty, punkty, asysty, zbiorki)
VALUES 
 (1, 1, 36, 28, 8, 7),
 (1, 2, 34, 22, 3, 12),
 (1, 3, 38, 30, 5, 6);
