-- Widok 1: Gracze z drużynami
CREATE VIEW widok_gracze_z_druzyna AS
SELECT g.id_gracza, g.imie, g.nazwisko, g.pozycja, z.nazwa AS nazwa_druzyny
FROM gracz g
LEFT JOIN zespol z ON g.id_zespolu = z.id_zespolu;

-- Widok 2: Średnie statystyki
CREATE VIEW widok_srednie_statystyki_gracza AS
SELECT 
  g.id_gracza,
  g.imie || ' ' || g.nazwisko AS gracz,
  ROUND(AVG(s.punkty)::numeric,2) AS srednie_punkty,
  ROUND(AVG(s.asysty)::numeric,2) AS srednie_asysty,
  ROUND(AVG(s.zbiorki)::numeric,2) AS srednie_zbiorki
FROM gracz g
JOIN statystyki_meczu s ON g.id_gracza = s.id_gracza
GROUP BY g.id_gracza, g.imie, g.nazwisko;

-- Widok 3: Wyniki meczów
CREATE VIEW widok_wyniki_meczy AS
SELECT m.id_meczu, m.data_meczu, 
  g.nazwa AS gospodarz, m.wynik_gospodarza,
  z.nazwa AS gosc, m.wynik_goscia
FROM mecz m
JOIN zespol g ON m.id_gospodarza = g.id_zespolu
JOIN zespol z ON m.id_goscia = z.id_zespolu;
