# Hochzeitstag Countdown Plugin

Eine romantische Anwendung und WordPress-Plugin, um die Zeit seit dem Hochzeitstag zu verfolgen und bis zum nächsten Jubiläum herunterzuzählen.

## 🌟 Funktionen

*   **Live-Countdown:** Echtzeitanzeige von Jahren, Tagen, Stunden und Minuten seit dem großen Tag.
*   **Feiermodus (Neu):** "Hearts & Gold"-Animation mit schwebenden Herzen und goldenem Leuchteffekt bei besonderen Anlässen (Meilenstein = Heute).
*   **Überraschungsideen:** Ein spezieller Bereich, der zufällige romantische Ideen vorschlägt, um den Partner zu überraschen (über 100 Vorschläge integriert).
*   **Dynamische Meilensteine:**
    *   Automatische Berechnung von Geburtstagen, jährlichen Hochzeitstagen und speziellen "Schnapszahl-Jubiläen" (z.B. 1111 Tage, 2222 Tage).
    *   Vierteljährliche Marker (1/4 Jahr, 1/2 Jahr etc.).
    *   **Benutzerdefinierte Events:** Unterstützung für eigene Meilensteine (z.B. Verlobung, Hauskauf).
    *   Zeigt die nächsten 5 chronologischen Ereignisse an.
*   **E-Mail-Benachrichtigungssystem:**
    *   **Automatischer Versand:** Erinnerungen werden täglich um **09:00 Uhr** geprüft und versendet.
    *   **Intelligentes Timing:** Erinnerungen kommen im frei konfigurierbaren Intervall (z.B. 7 Tage, 1 Tag und am Tag des Ereignisses selbst).
    *   **Mehrere Empfänger:** Konfigurierbare E-Mail-Adressen für beide Partner (Ehemann/Ehefrau) mit individueller Aktivierung.
    *   **Test-Button:** Senden Sie jederzeit eine Test-E-Mail, um die Funktion zu prüfen.
    *   **Debug-Modus:** Integriertes Logging zur Fehlersuche bei Cron-Jobs oder Mailversand.
    *   **Inhalt:** E-Mails enthalten den nächsten Meilenstein (Highlight), eine Vorschau auf die nächsten 14 Tage und 5 zufällige Überraschungsideen.
    *   **Design:** Anpassbare Hintergrundfarben für die E-Mail.
*   **Interaktive Geschichte:** Zeigt eine Zeitleiste Ihrer Beziehungshistorie.
*   **Responsive Design:** Modernes "Glassmorphism"-Design, optimiert für Handy und Desktop.
*   **Einfache Integration:** Eigene Seite unter `/hochzeit/` oder per Shortcode `[hochzeitstag]` einbindbar.

## 🚀 Installation (WordPress)

1.  **Download:** Laden Sie die Datei `backend/hochzeitstag-plugin_v2.10.16.zip` herunter.
2.  **Hochladen:** Gehen Sie in Ihr WordPress-Dashboard zu **Plugins -> Installieren -> Plugin hochladen**. Wählen Sie die ZIP-Datei aus und installieren Sie sie.
3.  **Aktivieren:** Aktivieren Sie das Plugin.
4.  **Setup (Optional):**
    *   Gehen Sie zu **Einstellungen -> Permalinks**.
    *   Klicken Sie einmal auf **"Änderungen speichern"** (dies aktualisiert die URL-Struktur für die Seite `/hochzeit/`).

## ⚙️ Konfiguration

Sie müssen keine Dateien mehr bearbeiten! Das Plugin verfügt jetzt über eine eigene Einstellungsseite.

1.  Klicken Sie im WordPress-Menü auf **"Hochzeitstag"**.
2.  **Allgemeine Einstellungen:**
    *   Tragen Sie Ihr Hochzeitsdatum, Kennenlerndaten und Geburtstage ein.
3.  **Ereignisse:**
    *   Fügen Sie eigene Events im JSON-Format hinzu.
4.  **E-Mail Einstellungen:**
    *   Hinterlegen Sie E-Mail-Adressen und Namen.
    *   **Erinnerungstage:** Geben Sie kommagetrennt die Tage ein (z.B. `7, 1, 0`). Die `0` steht für eine Benachrichtigung am Tag des Ereignisses selbst.
    *   **Sendezeit:** Legen Sie die tägliche Uhrzeit fest (orientieren Sie sich an der angezeigten Serverzeit).

## 🛠 Fehlersuche (Debugging)

Sollten keine E-Mails ankommen, bietet das Plugin ein integriertes Log:
1.  Prüfen Sie die Datei `wp-content/plugins/hochzeitstag-plugin/debug.log` auf Ihrem Server.
2.  Achten Sie auf Einträge wie `MATCH` (Erinnerungstag erkannt) oder `SUCCESS` (Mail an WordPress übergeben).
3.  Die Logs werden zusätzlich in das Standard PHP error_log (z.B. Docker Logs) geschrieben.

Klicken Sie auf **"Änderungen speichern"**, um die Einstellungen zu übernehmen.

## 🛠 Nutzung

*   **Direktlink:** Besuchen Sie `ihre-domain.de/hochzeit/`.
*   **Shortcode:** Fügen Sie `[hochzeitstag]` in eine beliebige Seite oder einen Beitrag ein.

## 🛠 Testen der E-Mail-Funktion

Sie haben zwei Möglichkeiten, den Versand zu prüfen:

1.  **"Test-Email senden" Button:**
    *   Auf der Frontend-Seite (`/hochzeit/`) im Footer.
    *   Sendet sofort eine E-Mail, ignoriert alle Regeln und Logs. Ideal zum Testen der SMTP-Verbindung.

2.  **Echte Logik testen (Cron):**
    *   Installieren Sie das Plugin **WP Crontrol**.
    *   Suchen Sie unter *Werkzeuge -> Cron-Events* nach `hochzeitstag_daily_event`.
    *   Klicken Sie auf **"Jetzt ausführen"**.
    *   Dies prüft die echten Regeln (Datum, bereits gesendet?).

## ⚠️ Hinweis zum automatischen Versand

WordPress führt geplante Aufgaben (wie den E-Mail-Versand um 09:00 Uhr) nur aus, wenn ein Besucher die Website aufruft. 
Für eine garantierte pünktliche Zustellung, auch wenn niemand die Seite besucht, empfehlen wir die Einrichtung eines externen Cron-Jobs (z.B. über *cron-job.org*), der einmal täglich `ihre-domain.de/wp-cron.php` aufruft.

## 📜 Lizenz
Privatnutzung.

## 📅 Änderungen
Siehe [CHANGELOG.md](CHANGELOG.md) für die Historie.
