## 📅 Changelog

### v2.10.14 (2025-12-23)
*   **Feature:** Intelligente Highlight-Box in Emails: Zeigt immer das absolut nächste Ereignis an, unabhängig vom Auslöser.
*   **Feature:** Vorschau-Liste: Zeigt zusätzlich alle weiteren Ereignisse der nächsten 14 Tage unterhalb des Highlights.
*   **Design:** Verbesserte Lesbarkeit der Highlight-Box (Creme-Hintergrund, Rahmen).
*   **Design:** Anpassbare E-Mail-Hintergrundfarben (Gesamt & Box) über die Einstellungen.
*   **Format:** Datum in E-Mails jetzt lokalisiert mit Wochentag (z.B. "am Mi den 23.12.2025").
*   **Fix:** Kritischen Fehler durch Code-Duplizierung behoben.

### v2.10.9 (2025-12-22)
*   **Fix:** Removed year count (e.g., "12.") and "Special Event:" prefix from custom events for cleaner email and UI display.
*   **Sync:** Unified label logic between frontend and backend.

### v2.10.8 (2025-12-22)
*   **Fix:** Refactored email notification logic to be more reliable (Issue #21).
    *   Tracks reminders individually via `sent_log`.
    *   Supports catch-up for missed cron runs.

### v2.10.7 (2025-12-22)
*   **Neu:** "Hearts & Gold"-Feiermodus.
*   **Sync:** Standalone-Version aktualisiert.