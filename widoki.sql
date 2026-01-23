DROP VIEW IF EXISTS widok_zawodnicy_z_zespolem;
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

DROP VIEW IF EXISTS widok_srednie_statystyki_zawodnika;
-- Widok 2: Średnie statystyki zawodnika
CREATE OR REPLACE VIEW widok_srednie_statystyki_zawodnika AS
SELECT
  z.id_zawodnika,
  z.imie || ' ' || z.nazwisko AS zawodnik_nazwa,
  zes.nazwa AS nazwa_zespolu,
  COUNT(s.id_meczu) AS liczba_meczow,
  COALESCE(ROUND(AVG(s.punkty)::numeric, 2), 0) AS srednie_punkty,
  COALESCE(ROUND(AVG(s.asysty)::numeric, 2), 0) AS srednie_asysty,
  COALESCE(ROUND(AVG(s.zbiorki)::numeric, 2), 0) AS srednie_zbiorki,
  COALESCE(ROUND(AVG(s.minuty)::numeric, 2), 0) AS srednie_minuty
FROM zawodnik z
LEFT JOIN zespol zes ON z.id_zespolu = zes.id_zespolu
LEFT JOIN statystyki_meczu s ON z.id_zawodnika = s.id_zawodnika
GROUP BY z.id_zawodnika, zes.nazwa;

DROP VIEW IF EXISTS widok_wyniki_meczy;
-- Widok 3: Wyniki meczów
CREATE OR REPLACE VIEW widok_wyniki_meczy AS
SELECT m.id_meczu, m.data_meczu,
  s.rok_rozpoczecia || '/' || s.rok_zakonczenia AS sezon,
  g.nazwa AS gospodarz, m.wynik_gospodarza,
  z.nazwa AS gosc, m.wynik_goscia
FROM mecz m
JOIN zespol g ON m.id_gospodarza = g.id_zespolu
JOIN zespol z ON m.id_goscia = z.id_zespolu
LEFT JOIN sezon s ON m.id_sezonu = s.id_sezonu;
