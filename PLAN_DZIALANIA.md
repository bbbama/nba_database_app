# Plan Działania - Aplikacja Bazodanowa NBA

Ten plik opisuje kolejne kroki potrzebne do ukończenia aplikacji klienckiej zgodnie z dokumentacją i wymaganiami projektu.

---

### Faza 1: Ukończenie Podstawowych Modułów (CRUD)

Celem tej fazy jest stworzenie możliwości zarządzania kluczowymi, niezależnymi danymi w systemie.

- [x] **Zarządzanie Zawodnikami (`zawodnik`)**
  - [x] Wyświetlanie listy zawodników.
  - [x] Formularz do dodawania i edycji zawodników.
  - [x] Logika usuwania zawodników.

- [ ] **Zarządzanie Zespołami (`zespol`)**
  - [x] Wyświetlanie listy zespołów.
  - [ ] Formularz do dodawania i edycji zespołów (`zespol_form.php`).
  - [ ] Logika usuwania zespołów (`zespol_delete.php`).

- [ ] **Zarządzanie Arenami (`arena`)**
  - [ ] Stworzenie prostego modułu CRUD do zarządzania arenami. Jest to potrzebne, aby móc przypisywać areny do zespołów.

- [ ] **Zarządzanie Sezonami (`sezon`)**
  - [ ] Stworzenie prostego modułu CRUD do zarządzania sezonami. Będzie to potrzebne do przypisywania meczów do konkretnego sezonu.

---

### Faza 2: Główne Funkcjonalności Systemu

W tej fazie skupimy się na sercu aplikacji - meczach i statystykach.

- [ ] **Zarządzanie Meczami (`mecz`)**
  - [ ] Stworzenie strony `mecze.php` wyświetlającej listę rozegranych meczów wraz z wynikami (na podstawie widoku `widok_wyniki_meczy`).
  - [ ] Przygotowanie formularza `mecz_form.php` do dodawania/edycji meczu, który pozwoli wybrać gospodarza i gościa z listy zespołów.
  - [ ] Dodanie logiki usuwania meczów.

- [ ] **Zarządzanie Statystykami Meczowymi (`statystyki_meczu`)**
  - [ ] Stworzenie dedykowanego interfejsu (np. `statystyki_meczu.php?id_meczu=X`), dostępnego z poziomu listy meczów.
  - [ ] W tym interfejsie będzie można dodawać i edytować statystyki (punkty, asysty, zbiórki itp.) dla każdego zawodnika biorącego udział w danym meczu.
  - [ ] Interfejs powinien wyświetlać listę zawodników z obu drużyn grających w meczu i umożliwiać wprowadzanie dla nich danych.

---

### Faza 3: Funkcje Zaawansowane i Raporty (na ocenę dobrą/bardzo dobrą)

Rozbudowa aplikacji o dodatkowe moduły i funkcje analityczne.

- [ ] **Implementacja Raportów (`raporty.php`)**
  - [ ] Stworzenie strony `raporty.php`, która będzie nawigować do różnych zestawień.
  - [ ] Implementacja co najmniej 3 raportów, np.:
    1.  Tabela średnich statystyk zawodników (na podstawie widoku `widok_srednie_statystyki_zawodnika`).
    2.  Tabela ligowa dla wybranego sezonu.
    3.  Zawodnik z największą liczbą punktów w pojedynczym meczu.

- [ ] **Zarządzanie Pozostałymi Encjami**
  - [ ] Stworzenie interfejsów CRUD dla pozostałych tabel, zgodnie z dokumentacją:
    - `kontrakt`
    - `kontuzja`
    - `trener`
    - `nagroda`
    - `tabela_ligowa`

- [ ] **Logika po stronie bazy danych**
  - [ ] Zapewnienie, że aplikacja poprawnie obsługuje błędy pochodzące z wyzwalaczy i walidacji zdefiniowanych w bazie danych (np. blokada wprowadzania niepoprawnych wartości).

- [ ] **System autoryzacji (na ocenę bardzo dobrą)**
  - [ ] Opcjonalna rozbudowa o system logowania i różne poziomy uprawnień użytkowników (np. admin, redaktor).

---

### Faza 4: Finalizacja Projektu

Ostatnie szlify i przygotowanie do oddania projektu.

- [ ] Przegląd i ewentualna poprawa całego kodu aplikacji.
- [ ] Finalne testy wszystkich funkcjonalności.
- [ ] Uzupełnienie i sfinalizowanie dokumentacji projektu (`wstepna_dokumentacja.txt` oraz `Tamplatka_dokumantacji.txt`).
