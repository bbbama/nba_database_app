-- funkcja: aktualizuj_tabele_ligowa_po_meczu()
-- Cel: Aktualizuje tabelę ligową na podstawie wyniku meczu.
-- Obsługuje dodawanie nowych meczów oraz zmianę wyników istniejących.
CREATE OR REPLACE FUNCTION aktualizuj_tabele_ligowa_po_meczu()
RETURNS TRIGGER AS $$
DECLARE
    host_won BOOLEAN;
    guest_won BOOLEAN;
    old_host_won BOOLEAN;
    old_guest_won BOOLEAN;
    old_id_sezonu INTEGER;
    old_id_gospodarza INTEGER;
    old_id_goscia INTEGER;
BEGIN
    -- Logika dla operacji DELETE (jeśli usuwamy mecz, odwracamy jego wpływ)
    IF TG_OP = 'DELETE' THEN
        old_id_sezonu := OLD.id_sezonu;
        old_id_gospodarza := OLD.id_gospodarza;
        old_id_goscia := OLD.id_goscia;

        IF OLD.wynik_gospodarza IS NOT NULL AND OLD.wynik_goscia IS NOT NULL THEN
            IF OLD.wynik_gospodarza > OLD.wynik_goscia THEN
                old_host_won := TRUE;
                old_guest_won := FALSE;
            ELSE
                old_host_won := FALSE;
                old_guest_won := TRUE;
            END IF;

            -- Odwracanie wpływu starego wyniku na gospodarza
            UPDATE tabela_ligowa
            SET
                liczba_zwyciestw = liczba_zwyciestw - CASE WHEN old_host_won THEN 1 ELSE 0 END,
                liczba_porazek = liczba_porazek - CASE WHEN old_guest_won THEN 1 ELSE 0 END
            WHERE id_sezonu = old_id_sezonu AND id_zespolu = old_id_gospodarza;

            -- Odwracanie wpływu starego wyniku na gościa
            UPDATE tabela_ligowa
            SET
                liczba_zwyciestw = liczba_zwyciestw - CASE WHEN old_guest_won THEN 1 ELSE 0 END,
                liczba_porazek = liczba_porazek - CASE WHEN old_host_won THEN 1 ELSE 0 END
            WHERE id_sezonu = old_id_sezonu AND id_zespolu = old_id_goscia;
        END IF;

        RETURN OLD;
    END IF;

    -- Upewnij się, że mamy ID sezonu i wyniki
    IF NEW.id_sezonu IS NULL OR NEW.wynik_gospodarza IS NULL OR NEW.wynik_goscia IS NULL THEN
        RETURN NEW; -- Nie przetwarzaj, jeśli brakuje danych
    END IF;

    -- Określenie zwycięzcy i przegranego
    IF NEW.wynik_gospodarza > NEW.wynik_goscia THEN
        host_won := TRUE;
        guest_won := FALSE;
    ELSE
        host_won := FALSE;
        guest_won := TRUE;
    END IF;

    -- Logika dla operacji UPDATE (kiedy zmienia się wynik meczu)
    IF TG_OP = 'UPDATE' THEN
        old_id_sezonu := OLD.id_sezonu;
        old_id_gospodarza := OLD.id_gospodarza;
        old_id_goscia := OLD.id_goscia;

        -- Jeśli sezon lub drużyny się zmieniły, lub wynik był już wcześniej, odwracamy wpływ starego wyniku
        IF OLD.id_sezonu IS NOT NULL AND OLD.wynik_gospodarza IS NOT NULL AND OLD.wynik_goscia IS NOT NULL THEN
            IF OLD.wynik_gospodarza > OLD.wynik_goscia THEN
                old_host_won := TRUE;
                old_guest_won := FALSE;
            ELSE
                old_host_won := FALSE;
                old_guest_won := TRUE;
            END IF;

            -- Odwracanie wpływu starego wyniku na gospodarza
            UPDATE tabela_ligowa
            SET
                liczba_zwyciestw = liczba_zwyciestw - CASE WHEN old_host_won THEN 1 ELSE 0 END,
                liczba_porazek = liczba_porazek - CASE WHEN old_guest_won THEN 1 ELSE 0 END
            WHERE id_sezonu = old_id_sezonu AND id_zespolu = old_id_gospodarza;

            -- Odwracanie wpływu starego wyniku na gościa
            UPDATE tabela_ligowa
            SET
                liczba_zwyciestw = liczba_zwyciestw - CASE WHEN old_guest_won THEN 1 ELSE 0 END,
                liczba_porazek = liczba_porazek - CASE WHEN old_host_won THEN 1 ELSE 0 END
            WHERE id_sezonu = old_id_sezonu AND id_zespolu = old_id_goscia;
        END IF;
    END IF;

    -- Aktualizacja/Wstawienie danych dla gospodarza
    INSERT INTO tabela_ligowa (id_sezonu, id_zespolu, liczba_zwyciestw, liczba_porazek)
    VALUES (NEW.id_sezonu, NEW.id_gospodarza, CASE WHEN host_won THEN 1 ELSE 0 END, CASE WHEN guest_won THEN 1 ELSE 0 END)
    ON CONFLICT (id_sezonu, id_zespolu) DO UPDATE SET
        liczba_zwyciestw = tabela_ligowa.liczba_zwyciestw + EXCLUDED.liczba_zwyciestw,
        liczba_porazek = tabela_ligowa.liczba_porazek + EXCLUDED.liczba_porazek;

    -- Aktualizacja/Wstawienie danych dla gościa
    INSERT INTO tabela_ligowa (id_sezonu, id_zespolu, liczba_zwyciestw, liczba_porazek)
    VALUES (NEW.id_sezonu, NEW.id_goscia, CASE WHEN guest_won THEN 1 ELSE 0 END, CASE WHEN host_won THEN 1 ELSE 0 END)
    ON CONFLICT (id_sezonu, id_zespolu) DO UPDATE SET
        liczba_zwyciestw = tabela_ligowa.liczba_zwyciestw + EXCLUDED.liczba_zwyciestw,
        liczba_porazek = tabela_ligowa.liczba_porazek + EXCLUDED.liczba_porazek;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Wyzwalacz dla operacji INSERT na tabeli mecz
DROP TRIGGER IF EXISTS trg_aktualizuj_tabele_ligowa_insert ON mecz;
CREATE TRIGGER trg_aktualizuj_tabele_ligowa_insert
AFTER INSERT ON mecz
FOR EACH ROW
EXECUTE FUNCTION aktualizuj_tabele_ligowa_po_meczu();

-- Wyzwalacz dla operacji UPDATE na tabeli mecz
-- Ważne: Wykonuje się PRZED INSERT (jeśli zmieniamy stary wynik) i PO INSERT (nowy wynik)
DROP TRIGGER IF EXISTS trg_aktualizuj_tabele_ligowa_update ON mecz;
CREATE TRIGGER trg_aktualizuj_tabele_ligowa_update
AFTER UPDATE OF wynik_gospodarza, wynik_goscia, id_sezonu, id_gospodarza, id_goscia ON mecz
FOR EACH ROW
WHEN (OLD.wynik_gospodarza IS DISTINCT FROM NEW.wynik_gospodarza OR
      OLD.wynik_goscia IS DISTINCT FROM NEW.wynik_goscia OR
      OLD.id_sezonu IS DISTINCT FROM NEW.id_sezonu OR
      OLD.id_gospodarza IS DISTINCT FROM NEW.id_gospodarza OR
      OLD.id_goscia IS DISTINCT FROM NEW.id_goscia)
EXECUTE FUNCTION aktualizuj_tabele_ligowa_po_meczu();

-- Wyzwalacz dla operacji DELETE na tabeli mecz
DROP TRIGGER IF EXISTS trg_aktualizuj_tabele_ligowa_delete ON mecz;
CREATE TRIGGER trg_aktualizuj_tabele_ligowa_delete
AFTER DELETE ON mecz
FOR EACH ROW
EXECUTE FUNCTION aktualizuj_tabele_ligowa_po_meczu();
