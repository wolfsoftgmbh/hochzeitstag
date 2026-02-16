## 📅 Changelog

### v2.13.0 (2026-02-16)
*   **Feature:** Granulare Steuerung für Schnapszahlen (separat für Tage, Stunden und Sekunden schaltbar).
*   **Verbesserung:** Bereinigte Meilenstein-Konfiguration im Admin-Bereich.

### v2.12.3 (2026-02-16)
*   **Fix:** Zentrale Versionierung über `HOCHZEITSTAG_VERSION` Konstante.
*   **UX:** Versionsnummer wird nun konsistent im Admin-Bereich und auf der Hochzeitsseite angezeigt.
*   **UX:** Entfernung des schwer auffindbaren Einstellungs-Links im Footer.

### v2.12.2 (2026-02-16)
*   **UX:** Rückkehr zum separaten Admin-Menü mit Herz-Icon (Wunsch des Nutzers).
*   **UX:** Einstellungs-Link im Footer bleibt als zusätzliche Option erhalten.

### v2.12.1 (2026-02-16)
*   **UX:** Menü unter "Einstellungen > Hochzeitstag" verschoben.
*   **UX:** Direkter Einstellungs-Link im Footer der Hochzeitstag-Seite (nur für Admins sichtbar).

### v2.12.0 (2026-02-16)
*   **Feature:** Vollständig konfigurierbare Meilensteine (Sekunden, Stunden, Tage, Schnapszahlen ein-/ausschaltbar).
*   **Feature:** Einstellbare Schrittweiten für alle Meilenstein-Typen.
*   **Feature:** E-Mail-Frequenz-Vorschau in den Einstellungen (berechnet geschätzte Mails pro Jahr).
*   **Verbesserung:** Optimierte Berechnungslogik für stabilere Performance.

### v2.11.1 (2026-02-16)
*   **Feature:** Sekunden-Meilensteine auf 10-Millionen-Schritte reduziert, um die Anzahl der E-Mails zu verringern.

### v2.10.17 (2025-12-23)
 *   **Design:** Entfernung des Header-Bildes und weiterer Footer-Elemente (Serverzeit, Pille) für ein minimalistisches Layout.
 
 ### v2.10.16 (2025-12-23)
*   **Cleanup:** Entfernung von Debug-Elementen im Frontend (Test-Email Button, Countdown-Pille), um das Design schlichter zu halten.
*   **Fix:** Sprüche aus der Konfiguration werden nun priorisiert (Failsafe gelockert).

### v2.10.15 (2025-12-23)
*   **Fix:** Gelockerter Failsafe-Check für Zitate (Eigene Zitate werden nun auch genutzt, wenn weniger als 5 hinterlegt sind).

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