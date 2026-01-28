-- Plik: apply_views.sql
-- Cel: Aktualizacja i stworzenie widoków bazodanowych oraz modyfikacja tabeli 'tabela_ligowa'.
-- Wersja 2: Dodano 'DROP VIEW IF EXISTS', aby uniknąć błędów przy zmianie kolejności/nazw kolumn.

--------------------------------------------------------------------------------
-- 1. Nowy, dynamiczny widok tabeli ligowej
--------------------------------------------------------------------------------
DROP VIEW IF EXISTS widok_tabeli_ligowej;
CREATE VIEW widok_tabeli_ligowej AS
SELECT
    tl.id_sezonu,
    s.rok_rozpoczecia || '/' || s.rok_zakonczenia AS sezon,
    tl.id_zespolu,
    z.nazwa AS nazwa_zespolu,
    z.miasto,
    tl.liczba_zwyciestw,
    tl.liczba_porazek,
    CASE
        WHEN (tl.liczba_zwyciestw + tl.liczba_porazek) = 0 THEN 0.000
        ELSE ROUND(tl.liczba_zwyciestw::numeric / (tl.liczba_zwyciestw + tl.liczba_porazek), 3)
    END AS procent_zwyciestw,
    RANK() OVER (
        PARTITION BY tl.id_sezonu
        ORDER BY
            CASE
                WHEN (tl.liczba_zwyciestw + tl.liczba_porazek) = 0 THEN 0.000
                ELSE tl.liczba_zwyciestw::numeric / (tl.liczba_zwyciestw + tl.liczba_porazek)
            END DESC,
            tl.liczba_zwyciestw DESC
    ) AS miejsce_w_tabeli
FROM tabela_ligowa tl
JOIN sezon s ON tl.id_sezonu = s.id_sezonu
JOIN zespol z ON tl.id_zespolu = z.id_zespolu;

-- Usunięcie zbędnej kolumny po pomyślnym utworzeniu widoku
ALTER TABLE tabela_ligowa DROP COLUMN IF EXISTS miejsce_w_tabeli;


--------------------------------------------------------------------------------
-- 2. Ulepszony widok średnich statystyk zawodnika
--------------------------------------------------------------------------------
DROP VIEW IF EXISTS widok_srednie_statystyki_zawodnika;
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
CREATE VIEW widok_wyniki_meczy AS
SELECT
  m.id_meczu,
  m.data_meczu,
  s.rok_rozpoczecia || '/' || s.rok_zakonczenia AS sezon,
  gosp.id_zespolu as id_gospodarza,
  gosp.nazwa AS gospodarz,
  m.wynik_gospodarza,
  gosc.id_zespolu as id_goscia,
  gosc.nazwa AS gosc,
  m.wynik_goscia
FROM mecz m
JOIN zespol gosp ON m.id_gospodarza = gosp.id_zespolu
JOIN zespol gosc ON m.id_goscia = gosc.id_zespolu
LEFT JOIN sezon s ON m.id_sezonu = s.id_sezonu
ORDER BY m.data_meczu DESC;

DROP VIEW IF EXISTS widok_zawodnicy_z_zespolem;
CREATE VIEW widok_zawodnicy_z_zespolem AS
SELECT
  z.id_zawodnika,
  z.imie,
  z.nazwisko,
  z.pozycja,
  z.data_urodzenia,
  z.id_zespolu,
  COALESCE(zes.nazwa, 'Wolny agent') AS nazwa_zespolu
FROM zawodnik z
LEFT JOIN zespol zes ON z.id_zespolu = zes.id_zespolu
ORDER BY z.nazwisko, z.imie;
