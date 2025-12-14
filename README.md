# Hochzeitstag Countdown

A romantic, single-page application and WordPress plugin to track the time since your wedding and countdown to the next anniversary.

## Features
*   **Live Countdown:** Years, days, hours, and minutes since the big day.
*   **Milestones:** Automatically calculates upcoming 1000-day marks and anniversaries.
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
The plugin configuration (dates, quotes, names) is located in `assets/config.js` within the plugin folder. You can edit this file directly via the Plugin Editor or via FTP.

## Development
*   **Frontend:** The core logic lives in `frontend/`. Open `wedding.html` in a browser to develop locally.
*   **Backend:** The WordPress plugin structure is in `backend/hochzeitstag-plugin/`.

## License
Proprietary / Private use.