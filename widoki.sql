-- Widok 1: Zawodnicy z zespołem
CREATE OR REPLACE VIEW widok_zawodnicy_z_zespolem AS
SELECT
  z.id_zawodnika,
  z.imie,
  z.nazwisko,
  z.pozycja,
  zes.nazwa AS nazwa_zespolu
FROM zawodnik z
LEFT JOIN zespol zes ON z.id_zespolu = zes.id_zespolu;

-- Widok 2: Średnie statystyki zawodnika
CREATE OR REPLACE VIEW widok_srednie_statystyki_zawodnika AS
SELECT
  z.id_zawodnika,
  z.imie || ' ' || z.nazwisko AS zawodnik_nazwa,
  ROUND(AVG(s.punkty)::numeric,2) AS srednie_punkty,
  ROUND(AVG(s.asysty)::numeric,2) AS srednie_asysty,
  ROUND(AVG(s.zbiorki)::numeric,2) AS srednie_zbiorki
FROM zawodnik z
JOIN statystyki_meczu s ON z.id_zawodnika = s.id_zawodnika
GROUP BY z.id_zawodnika, z.imie, z.nazwisko;

-- Widok 3: Wyniki meczów
CREATE OR REPLACE VIEW widok_wyniki_meczy AS
SELECT m.id_meczu, m.data_meczu,
  g.nazwa AS gospodarz, m.wynik_gospodarza,
  z.nazwa AS gosc, m.wynik_goscia
FROM mecz m
JOIN zespol g ON m.id_gospodarza = g.id_zespolu
JOIN zespol z ON m.id_goscia = z.id_zespolu;