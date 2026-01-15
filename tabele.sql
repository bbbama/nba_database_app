-- Utworzenie schematu (opcjonalnie)
-- CREATE SCHEMA nba;

-- Tabela: zespol
CREATE TABLE zespol (
  id_zespolu      SERIAL PRIMARY KEY,
  nazwa           VARCHAR(100) NOT NULL UNIQUE,
  miasto          VARCHAR(100),
  rok_zalozenia   SMALLINT CHECK (rok_zalozenia > 1850 AND rok_zalozenia <= EXTRACT(YEAR FROM CURRENT_DATE)),
  trener_glowny   VARCHAR(100)
);

-- Tabela: zawodnik
CREATE TABLE zawodnik (
  id_zawodnika       SERIAL PRIMARY KEY,
  imie            VARCHAR(50) NOT NULL,
  nazwisko        VARCHAR(50) NOT NULL,
  pozycja         VARCHAR(30),
  data_urodzenia  DATE,
  id_zespolu      INTEGER REFERENCES zespol(id_zespolu) ON DELETE SET NULL
);

-- Tabela: mecz
CREATE TABLE mecz (
  id_meczu        SERIAL PRIMARY KEY,
  data_meczu      DATE NOT NULL,
  id_gospodarza   INTEGER NOT NULL REFERENCES zespol(id_zespolu) ON DELETE CASCADE,
  id_goscia       INTEGER NOT NULL REFERENCES zespol(id_zespolu) ON DELETE CASCADE,
  wynik_gospodarza SMALLINT CHECK (wynik_gospodarza >= 0),
  wynik_goscia     SMALLINT CHECK (wynik_goscia >= 0),
  CONSTRAINT roznorodne_druzyny CHECK (id_gospodarza <> id_goscia)
);

-- Tabela asocjacyjna: statystyki_meczu (N:M zawodnik <-> mecz)
CREATE TABLE statystyki_meczu (
  id_statystyki   SERIAL PRIMARY KEY,
  id_meczu        INTEGER NOT NULL REFERENCES mecz(id_meczu) ON DELETE CASCADE,
  id_zawodnika    INTEGER NOT NULL REFERENCES zawodnik(id_zawodnika) ON DELETE CASCADE,
  minuty          SMALLINT CHECK (minuty >= 0),
  punkty          SMALLINT CHECK (punkty >= 0),
  asysty          SMALLINT CHECK (asysty >= 0),
  zbiorki         SMALLINT CHECK (zbiorki >= 0),
  UNIQUE (id_meczu, id_zawodnika)
);

-- Indeksy przydatne do zapytań
CREATE INDEX idx_zawodnik_zespol ON zawodnik(id_zespolu);
CREATE INDEX idx_statystyki_meczu_mecz ON statystyki_meczu(id_meczu);
CREATE INDEX idx_statystyki_meczu_zawodnik ON statystyki_meczu(id_zawodnika);

--------------------------------------------------------------------------------
-- DODATKOWE ENCJE
--------------------------------------------------------------------------------

-- 1. Kontuzje zawodników
CREATE TABLE kontuzja (
  id_kontuzji     SERIAL PRIMARY KEY,
  id_zawodnika    INTEGER NOT NULL REFERENCES zawodnik(id_zawodnika) ON DELETE CASCADE,
  typ_kontuzji    VARCHAR(100),
  data_rozpoczecia DATE,
  data_zakonczenia DATE,
  status          VARCHAR(20) CHECK (status IN ('aktywna','wyleczona','nieznany'))
);

-- 2. Kontrakty zawodników
CREATE TABLE kontrakt (
  id_kontraktu    SERIAL PRIMARY KEY,
  id_zawodnika    INTEGER NOT NULL REFERENCES zawodnik(id_zawodnika) ON DELETE CASCADE,
  id_zespolu      INTEGER NOT NULL REFERENCES zespol(id_zespolu) ON DELETE CASCADE,
  data_poczatek   DATE NOT NULL,
  data_koniec     DATE NOT NULL,
  wynagrodzenie_roczne NUMERIC(12,2) CHECK (wynagrodzenie_roczne >= 0)
);

-- 3. Areny
CREATE TABLE arena (
  id_arena        SERIAL PRIMARY KEY,
  nazwa           VARCHAR(100) NOT NULL,
  miasto          VARCHAR(100),
  pojemnosc       INTEGER CHECK (pojemnosc > 0),
  rok_otwarcia    SMALLINT CHECK (rok_otwarcia > 1850 AND rok_otwarcia <= EXTRACT(YEAR FROM CURRENT_DATE))
);

-- Modyfikacja tabeli zespol, aby uwzględnić główną halę
ALTER TABLE zespol ADD COLUMN id_arena INTEGER REFERENCES arena(id_arena) ON DELETE SET NULL;

-- 4. Sezony
CREATE TABLE sezon (
  id_sezonu       SERIAL PRIMARY KEY,
  rok_rozpoczecia SMALLINT CHECK (rok_rozpoczecia > 1900 AND rok_rozpoczecia <= EXTRACT(YEAR FROM CURRENT_DATE)),
  rok_zakonczenia  SMALLINT CHECK (rok_zakonczenia >= rok_rozpoczecia AND rok_zakonczenia <= EXTRACT(YEAR FROM CURRENT_DATE))
);

-- Powiązanie meczu z sezonem
ALTER TABLE mecz ADD COLUMN id_sezonu INTEGER REFERENCES sezon(id_sezonu) ON DELETE SET NULL;

-- 5. Tabela ligowa
CREATE TABLE tabela_ligowa (
  id_tabeli       SERIAL PRIMARY KEY,
  id_sezonu       INTEGER NOT NULL REFERENCES sezon(id_sezonu) ON DELETE CASCADE,
  id_zespolu      INTEGER NOT NULL REFERENCES zespol(id_zespolu) ON DELETE CASCADE,
  liczba_zwyciestw SMALLINT DEFAULT 0 CHECK (liczba_zwyciestw >= 0),
  liczba_porazek   SMALLINT DEFAULT 0 CHECK (liczba_porazek >= 0),
  miejsce_w_tabeli SMALLINT CHECK (miejsce_w_tabeli > 0),
  UNIQUE(id_sezonu, id_zespolu)
);

-- 6. Trenerzy i sztab
CREATE TABLE trener (
  id_trenera      SERIAL PRIMARY KEY,
  imie            VARCHAR(50) NOT NULL,
  nazwisko        VARCHAR(50) NOT NULL,
  rola            VARCHAR(50) CHECK (rola IN ('glowny','asystent','przygotowanie fizyczne')),
  id_zespolu      INTEGER REFERENCES zespol(id_zespolu) ON DELETE SET NULL
);

-- 7. Nagrody zawodników
CREATE TABLE nagroda (
  id_nagrody      SERIAL PRIMARY KEY,
  id_zawodnika    INTEGER NOT NULL REFERENCES zawodnik(id_zawodnika) ON DELETE CASCADE,
  nazwa_nagrody   VARCHAR(100) NOT NULL,
  rok             SMALLINT CHECK (rok > 1900 AND rok <= EXTRACT(YEAR FROM CURRENT_DATE))
);
