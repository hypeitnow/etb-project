RAPORT STUDIUM WYKONALNOSCI
System obslugi zespolu sportowego ETB
Aplikacja webowa w technologii Laravel

Zlecajacy: ETB (Jakub Borowski)
Wykonanie: Filip Kijewski w ramach organizacji Eat The Ball
Technologie: Laravel, Blade, Tailwind, Alpine.js, Vite, MySQL
Zakres: zarzadzanie druzyna, tresciami, uzytkownikami i komunikacja


Dokument przygotowany jako kompletna baza do dalszego opracowania projektu.

Spis tresci
1. Zalozenia realizacji
   1.1 Zlecajacy
   1.2 Podstawa opracowania
   1.3 Cel studium
   1.4 Zakres raportu
2. Opis projektu
   2.1 Glowne cele systemu
   2.2 Uzytkownicy systemu
3. Stan obecny i problem do rozwiazania
   3.1 Glowne problemy
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
Zlecajacym projektu jest organizacja sportowa ETB, reprezentowana przez wlasciciela projektu Jakuba Borowskiego. System tworzony jest w ramach prac inzynierskich i ma sluzyc do zarzadzania druzyna sportowa oraz komunikacja z uzytkownikami.

1.2 Podstawa opracowania
- wymagania zlecajacego i biezace ustalenia z rozmowy projektowej,
- analiza podobnych stron klubow i druzyn sportowych,
- stan obecny kodu, ktory wymaga uporzadkowania i podzialu na moduly,
- wybor technologii webowych: Laravel, Blade, Tailwind, Alpine.js, Vite oraz MySQL.

1.3 Cel studium
Celem studium jest ocena wykonalnosci systemu od strony technicznej, organizacyjnej i czasowej, a takze wskazanie zakresu funkcji, ktore powinny wejsc do wersji podstawowej oraz ktore mozna pozostawic jako rozwoj w przyszlosci.

1.4 Zakres raportu
Raport obejmuje opis systemu, wymagania, architekture, zakres funkcjonalny, plan wdrozenia, ryzyka, testy oraz propozycje dalszego rozwoju.

2. Opis projektu

Projekt zaklada stworzenie internetowej platformy dla druzyny sportowej ETB. System bedzie pelnil jednoczesnie funkcje strony publicznej, zaplecza administracyjnego oraz wewnetrznego narzedzia do organizacji pracy druzyny.

2.1 Glowne cele systemu
- uporzadkowanie informacji o druzynie w jednym miejscu,
- ulatwienie zarzadzania zawodnikami, meczami, treningami i tresciami,
- poprawa komunikacji miedzy klubem, trenerem, zawodnikami i fanami,
- stworzenie nowoczesnej i skalowalnej platformy,
- przygotowanie projektu do dalszego rozwoju.

2.2 Uzytkownicy systemu
- Administrator
- Trener
- Pracownik klubu
- Zawodnik
- Fan / gosc

3. Stan obecny i problem do rozwiazania

Obecnie problemem jest brak jednego spojnego systemu oraz nieuporzadkowany kod.

3.1 Glowne problemy
- brak centralnego systemu,
- brak panelu admina,
- chaotyczna struktura kodu,
- trudnosci w rozwoju projektu.

3.2 Wniosek
Najwiekszym wyzwaniem jest uporzadkowanie architektury systemu.

4. Wymagania systemowe

4.1 Wymagania funkcjonalne
- przegladanie strony,
- zarzadzanie trescia,
- CRUD zawodnikow, meczow, treningow,
- zarzadzanie uzytkownikami,
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
    Projekt jest wykonalny, kluczowe jest uporzadkowanie architektury.

13. Zalacznik
    Encje:
- users
- roles
- players
- matches
- training
- news
- notifications
