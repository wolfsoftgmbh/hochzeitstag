## 📅 Changelog

### v2.10.8 (2025-12-22)
*   **Fix:** Refactored email notification logic to be more reliable.
    *   Now tracks each specific reminder event separately.
    *   Allows "catch-up" emails if a cron job was missed (within a reasonable window).
    *   Prevents duplicate emails if multiple events occur on the same day.
    *   Fixed: Issue #21 (Email sending unreliability).

### v2.10.7 (2025-12-22)
*   **Neu:** "Hearts & Gold"-Feiermodus für den Hochzeitstag und Meilensteine.
*   **Sync:** Frontend-Standalone-Version mit Plugin-Features synchronisiert.
*   **Fix:** Kritischer PHP-Fehler (String-Konkatenation) im Logging behoben.
*   **Update:** Abhängigkeiten und Assets aktualisiert.