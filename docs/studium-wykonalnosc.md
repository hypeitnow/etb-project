RAPORT STUDIUM WYKONALNOSCI
System obslugi zespolu sportowego ETB
Aplikacja webowa w technologii Laravel

Zlecajacy: ETB (Jakub Borowski)
Wykonanie: Filip Kijewski w ramach organizacji Eat The Ball
Technologie: Laravel, Blade, Tailwind, Alpine.js, Vite, MySQL
Zakres: zarządzanie drużyną, treściami, użytkownikami i komunikacją


Dokument przygotowany jako kompletna baza do dalszego opracowania projektu.

Spis tresci
1. Zalozenia realizacji
   1.1 Zlecajacy
   1.2 Podstawa opracowania
   1.3 Cel studium
   1.4 Zakres raportu
2. Opis projektu
   2.1 Główne cele systemu
   2.2 Użytkownicy systemu
3. Stan obecny i problem do rozwiązania
   3.1 Główne problemy
   3.2 Wniosek
4. Wymagania systemowe
   4.1 Wymagania funkcjonalne
   4.2 Wymagania niefunkcjonalne
   4.3 Wymagania jakosciowe
5. Architektura systemu
   5.1 Backend
   5.2 Frontend
   5.3 Baza danych
   5.4 Bezpieczenstwo
   5.5 Wymagania prawne i ochrona danych osobowych
6. Moduly funkcjonalne
7. Funkcje rozszerzone systemu
8. Future features
9. Plan realizacji
10. Ryzyka i ograniczenia
11. Testy i wdrozenie
12. Podsumowanie
13. Zalacznik: struktura modulow


1. Zalozenia realizacji

1.1 Zlecajacy
Zlecającym projektu jest organizacja sportowa ETB, reprezentowana przez właściciela projektu Jakuba Borowskiego. System tworzony jest w ramach prac inżynierskich i ma służyć do zarządzania drużyną sportową oraz komunikacją z użytkownikami.

1.2 Podstawa opracowania
- wymagania zlecającego i bieżące ustalenia z rozmowy projektowej,
- analiza podobnych stron klubów i drużyn sportowych,
- stan obecny kodu, który wymaga uporządkowania i podziału na moduły,
- wybór technologii webowych: Laravel, Blade, Tailwind, Alpine.js, Vite oraz MySQL.

1.3 Cel studium
Celem studium jest ocena wykonalności systemu od strony technicznej, organizacyjnej i czasowej, a także wskazanie zakresu funkcji, które powinny wejść do wersji podstawowej oraz które można pozostawić jako rozwój w przyszłości.

1.4 Zakres raportu
Raport obejmuje opis systemu, wymagania, architekturę, zakres funkcjonalny, plan wdrożenia, ryzyka, testy oraz propozycje dalszego rozwoju.

2. Opis projektu

Projekt zakłada stworzenie internetowej platformy dla drużyny sportowej ETB. System będzie pełnił jednocześnie funkcję strony publicznej, zaplecza administracyjnego oraz wewnętrznego narzędzia do organizacji pracy drużyny.

2.1 Główne cele systemu
- uporządkowanie informacji o drużynie w jednym miejscu,
- ułatwienie zarządzania zawodnikami, meczami, treningami i treściami,
- poprawa komunikacji między klubem, trenerem, zawodnikami i fanami,
- stworzenie nowoczesnej i skalowalnej platformy,
- przygotowanie projektu do dalszego rozwoju.

2.2 Użytkownicy systemu
- Administrator
- Trener
- Pracownik klubu
- Zawodnik
- Fan / gość

3. Stan obecny i problem do rozwiązania

Obecnie problemem jest brak jednego spójnego systemu oraz nieuporządkowany kod.

3.1 Główne problemy
- brak centralnego systemu,
- brak panelu admina,
- chaotyczna struktura kodu,
- trudności w rozwoju projektu.

3.2 Wniosek
Największym wyzwaniem jest uporządkowanie architektury systemu.

4. Wymagania systemowe

4.1 Wymagania funkcjonalne
- przeglądanie strony,
- zarządzanie treścią,
- CRUD zawodników, meczów, treningów,
- zarządzanie użytkownikami,
- formularze,
- powiadomienia,
- prosty AI chat,
- galeria.

4.2 Wymagania niefunkcjonalne
- responsywnosc,
- czas odpowiedzi < 2s,
- bezpieczenstwo,
- skalowalnosc.

4.3 Wymagania jakosciowe
- intuicyjny UI,
- czytelnosc,
- spojnosc.

5. Architektura systemu

System klient-serwer.

5.1 Backend
- Laravel
- PHP
- MVC
- RBAC

5.2 Frontend
- Blade
- Tailwind
- Alpine.js
- Vite

5.3 Baza danych
- MySQL
- SQLite (dev)

5.4 Bezpieczenstwo
- hashowanie hasel
- middleware
- role
- walidacja

6. Moduly funkcjonalne

6.1 Public
6.2 Admin
6.3 Trener
6.4 Zawodnik
6.5 Fan

7. Funkcje rozszerzone
- harmonogram
- kalendarz
- statystyki
- powiadomienia
- SMS
- AI chat

8. Future features
- mobile app
- live score
- social media
- platnosci
- raporty

9. Plan realizacji
- analiza
- projekt
- implementacja
- testy
- wdrozenie

10. Ryzyka
- techniczne
- organizacyjne
- projektowe

11. Testy i wdrozenie
- testy
- deployment
- utrzymanie

12. Podsumowanie
    Projekt jest wykonalny, kluczowe jest uporządkowanie architektury.

13. Zalacznik
    Encje:
- users
- roles
- players
- matches
- training
- news
- notifications
