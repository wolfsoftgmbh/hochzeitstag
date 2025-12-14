# Hochzeitstag Countdown

A romantic, single-page application and WordPress plugin to track the time since your wedding and countdown to the next anniversary.

## Features
*   **Live Countdown:** Years, days, hours, and minutes since the big day.
*   **Dynamic Milestones:** 
    *   Automatically calculates upcoming birthdays, annual wedding anniversaries, and special repdigit days (Schnapszahlen like 111, 2222 days).
    *   Includes quarterly year markers (1/4 year, 1/2 year, etc.) for more frequent celebrations.
    *   Shows the next 5 chronological events.
*   **Interactive History:** Displays a timeline of your relationship, including first meeting, wedding, and all past anniversaries unlocked over time.
*   **Responsive Design:** Looks great on mobile and desktop using a modern Glassmorphism aesthetic.
*   **Standalone Page:** Runs on a dedicated URL (e.g., `/hochzeit/`) without affecting your WordPress theme.

## Installation (WordPress)

1.  **Download:** Get the `hochzeitstag-plugin.zip` file from the `backend/` directory.
2.  **Upload:** Go to your WordPress Dashboard -> Plugins -> Add New -> Upload Plugin. Select the zip file and install.
3.  **Activate:** Activate the plugin.
4.  **Setup URL:**
    *   Go to **Settings -> Permalinks**.
    *   Click **"Save Changes"** (you don't need to change any settings, just saving flushes the rewrite rules).
5.  **View:** Visit `your-site.com/hochzeit/` to see your countdown.

## Configuration
The plugin configuration is located in `assets/config.js` within the plugin folder. You can edit this file directly via the Plugin Editor or via FTP.

The configuration object `HOCHZEITSTAG_CONFIG` contains the following customizable properties:

### Dates
*   `weddingDate` (string): The date and time of your wedding (ISO 8601 format, e.g., `"2025-09-06T11:02:00"`). This is the base for the main countdown.
*   `firstContactDate` (string): Date of first contact ("Erster Kontakt").
*   `firstMeetDate` (string): Date when you got together ("Zusammen").
*   `birthdays` (object): Key-value pairs for birthdays to appear in the milestone timeline.
    *   Format: `name: "YYYY-MM-DD"` (e.g., `klaus: "1967-08-02"`).

### Visuals
*   `backgroundImage` (string): Filename of the background image (located in `assets/`). Defaults to `"kiss.jpeg"`.

### Notifications (Email)
*   `emailAddresses` (object): Configuration for email reminders.
    *   `husband` / `wife`: Objects containing `email` and `name`.
*   `emailReminderDaysFirst` (number): Days before an event to send the first reminder (Default: 7).
*   `emailReminderDaysSecond` (number): Days before an event to send the second reminder (Default: 1).
*   `enableEmailTestButton` (boolean): Set to `true` to show a "Test Email" button in the footer for debugging.

### Content
*   `quotes` (array of strings): A list of romantic or humorous quotes that are displayed randomly on the page.

## Development
*   **Frontend:** The core logic lives in `frontend/`. Open `wedding.html` in a browser to develop locally.
*   **Backend:** The WordPress plugin structure is in `backend/hochzeitstag-plugin/`.

## License
Proprietary / Private use.