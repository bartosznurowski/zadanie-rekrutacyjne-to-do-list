# Co zostało zrobione

- CRUD dla zadań z uwzględnieniem nazwy, opisu, priorytetu, statusu i terminu wykonania.
- System logowania i rejestracji użytkowników.
- Filtrowanie listy zadań według priorytetów, statusu i terminu.
- Możliwość dodawania zadań do kalendarza Google.
- Zmieniono klucze główne na UUID dla bezpieczeństwa.
- Powiadomienie e-mail na 1 dzień przed terminem zadania z użyciem cron.
- Walidacja danych
- Historia zmian w zadaniach, logując zmiany pól.
- Udostępnianie zadań bez autoryzacji za pomocą linka z tokenem dostępowym

# Przemyślenia i uwagi

- Ze względu na charakter zadania, frontend jest bardzo uproszczony pod swobodne sprawdzenie funkcjonalności backendu.
- Projekt jest gotowy do rozbudowy o dodatkowe funkcjonalności.
- Google OAuth wymagał konkretnej konfiguracji pod testowanie na localhost który umożliwia dodawanie zadań do kalendarza tylko użytkownikowi którego email zgadza się z określonym emailem testowym w Google Cloud Console.