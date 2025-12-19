# Hochzeitstag Countdown Plugin

Eine romantische Anwendung und WordPress-Plugin, um die Zeit seit dem Hochzeitstag zu verfolgen und bis zum nächsten Jubiläum herunterzuzählen.

## 🌟 Funktionen

*   **Live-Countdown:** Echtzeitanzeige von Jahren, Tagen, Stunden und Minuten seit dem großen Tag.
*   **Überraschungsideen:** Ein spezieller Bereich, der zufällige romantische Ideen vorschlägt, um den Partner zu überraschen (über 100 Vorschläge integriert).
*   **Dynamische Meilensteine:**
    *   Automatische Berechnung von Geburtstagen, jährlichen Hochzeitstagen und speziellen "Schnapszahl-Jubiläen" (z.B. 1111 Tage, 2222 Tage).
    *   Vierteljährliche Marker (1/4 Jahr, 1/2 Jahr etc.).
    *   **Benutzerdefinierte Events:** Unterstützung für eigene Meilensteine (z.B. Verlobung, Hauskauf).
    *   Zeigt die nächsten 5 chronologischen Ereignisse an.
*   **E-Mail-Benachrichtigungssystem:**
    *   **Automatischer Versand:** Erinnerungen werden täglich um **09:00 Uhr** geprüft und versendet.
    *   **Intelligentes Timing:** Erinnerungen kommen standardmäßig 7 Tage und 1 Tag vor dem Ereignis (konfigurierbar).
    *   **Mehrere Empfänger:** Konfigurierbare E-Mail-Adressen für beide Partner (Ehemann/Ehefrau) mit individueller Aktivierung.
    *   **Test-Button:** Senden Sie jederzeit eine Test-E-Mail, um die Funktion zu prüfen.
    *   **Inhalt:** E-Mails enthalten den Meilenstein und 5 zufällige Überraschungsideen zur Inspiration.
*   **Interaktive Geschichte:** Zeigt eine Zeitleiste Ihrer Beziehungshistorie.
*   **Responsive Design:** Modernes "Glassmorphism"-Design, optimiert für Handy und Desktop.
*   **Einfache Integration:** Eigene Seite unter `/hochzeit/` oder per Shortcode `[hochzeitstag]` einbindbar.

## 🚀 Installation (WordPress)

1.  **Download:** Laden Sie die Datei `backend/hochzeitstag-plugin_final.zip` herunter.
2.  **Hochladen:** Gehen Sie in Ihr WordPress-Dashboard zu **Plugins -> Installieren -> Plugin hochladen**. Wählen Sie die ZIP-Datei aus und installieren Sie sie.
3.  **Aktivieren:** Aktivieren Sie das Plugin.
4.  **Setup (Optional):**
    *   Gehen Sie zu **Einstellungen -> Permalinks**.
    *   Klicken Sie einmal auf **"Änderungen speichern"** (dies aktualisiert die URL-Struktur für die Seite `/hochzeit/`).

## ⚙️ Konfiguration (NEU in v2.0)

Sie müssen keine Dateien mehr bearbeiten! Das Plugin verfügt jetzt über eine eigene Einstellungsseite.

1.  Klicken Sie im WordPress-Menü auf **"Hochzeitstag"**.
2.  **Allgemeine Einstellungen:**
    *   Tragen Sie Ihr Hochzeitsdatum, Kennenlerndaten und Geburtstage ein.
3.  **Ereignisse:**
    *   Fügen Sie eigene Events im JSON-Format hinzu (z.B. Hauskauf, Verlobung).
4.  **E-Mail Einstellungen:**
    *   Hinterlegen Sie die E-Mail-Adressen und Namen für Ehemann und Ehefrau.
    *   Aktivieren Sie die Checkbox "Aktiv", um E-Mails zu empfangen.
    *   Legen Sie fest, wie viele Tage im Voraus Sie erinnert werden möchten (z.B. `7, 1`).

Klicken Sie auf **"Änderungen speichern"**, um die Einstellungen zu übernehmen.

## 🛠 Nutzung

*   **Direktlink:** Besuchen Sie `ihre-domain.de/hochzeit/`.
*   **Shortcode:** Fügen Sie `[hochzeitstag]` in eine beliebige Seite oder einen Beitrag ein.

## ⚠️ Hinweis zum automatischen Versand

WordPress führt geplante Aufgaben (wie den E-Mail-Versand um 09:00 Uhr) nur aus, wenn ein Besucher die Website aufruft. 
Für eine garantierte pünktliche Zustellung, auch wenn niemand die Seite besucht, empfehlen wir die Einrichtung eines externen Cron-Jobs (z.B. über *cron-job.org*), der einmal täglich `ihre-domain.de/wp-cron.php` aufruft.

## 📜 Lizenz
Privatnutzung.
