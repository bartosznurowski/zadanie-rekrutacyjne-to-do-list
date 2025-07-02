# Task Manager z integracją Google Calendar

## Opis
Aplikacja webowa do zarządzania zadaniami (to-do list), umożliwiająca tworzenie, edytowanie, śledzenie i oznaczanie zadań z uwzględnieniem priorytetów oraz statusów. Zawiera integrację z Google Calendar (OAuth2), system przypomnień mailowych o nadchodzących terminach oraz historię zmian zadań.
Projekt zrealizowany w ramach zadania rekrutacyjnego.

## Wymagania
- PHP 8.x
- Laravel 11
- Composer
- MySQL/PostgreSQL/SQLite
- Konto Google Cloud z włączonym API Google Calendar i OAuth 2.0 Client ID

## Instalacja

1. Sklonuj repozytorium: git clone [URL_REPO]
2. Zainstaluj zależności: composer install
3. Skopiuj plik .env.example lub dodaj do swojego pliku .env zmienne odpowiadające za Gmail SMTP, OAuth i Google Calendar
4. Wykonaj migracje: php artisan migrate
5. Utworzenie projektu Google Cloud
- Przejdź na https://console.cloud.google.com/
- Utwórz nowy projekt
- Wybierz "Interfejsy API i usługi" -> "Dane logowania" -> "Utwórz dane logowania" -> "Identyfikator klienta OAuth" -> "Typ aplikacji: Aplikacja internetowa" 
-> "Autoryzowane źródła JavaScriptu: http://localhost:8000" -> "Autoryzowane identyfikatory URI przekierowania: http://localhost:8000/google/callback" i wklej go do .env "GOOGLE_REDIRECT_URI" -> "Zapisz"
-> Znajdź w "Tajne klucze klienta" w widoku identyfikatora klienta -> Wybierz " + Add secret " -> Skopiuj utworzony Tajny klucz klienta i wklej do .env "GOOGLE_CLIENT_SECRET"
-> Następnie wróc do ""Interfejsy API i usługi" -> "Dane logowania"" -> Skopiuj identyfikator klienta i wklej do .env "GOOGLE_CLIENT_ID"
- Wybierz "Interfejsy API i usługi" -> "Ekran zgody OAuth" -> "Odbiorcy" -> "Użytkownicy testowi: + Add users" -> Wpisz swój gmail z którego będziesz korzystał przy OAuth lokalnie
6. Włączenie Google Calendar API
- Przejdź do "Interfejsy API i usługi" -> "Bibliotetka"
- Wyszukaj "Google Calendar API"
- Kliknij "Włącz"
7. Włączenie dostępu do konta Gmail
- Przejdź do https://myaccount.google.com/security
- W sekcji "Weryfikacja dwuetapowa" → Włącz
- Wyszukaj "hasła do aplikacji" -> Stwórz hasło -> Skopiuj i wklej do .env "MAIL_PASSWORD"
8. Uruchomienie lokalne: php artisan serve

## Użycie

- Zarejestruj i zaloguj użytkownika testowego. ( email musi się zgadzać z emailem podanym w google cloud dla użytkownika testowego oauth )
- Cały interfejs intuicyjnie naprowadzi do wszystkich wymaganych operacji.