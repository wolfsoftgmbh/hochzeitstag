# Hochzeitstag Countdown

A romantic, single-page application and WordPress plugin to track the time since your wedding and countdown to the next anniversary.

## 🌟 Features
*   **Live Countdown:** Real-time display of years, days, hours, and minutes since the big day.
*   **Surprise Ideas (Überraschungsidee):** A dedicated section displaying random romantic ideas to surprise your partner. Includes a library of 100+ suggestions.
*   **Dynamic Milestones:** 
    *   Automatically calculates upcoming birthdays, annual wedding anniversaries, and special repdigit days (Schnapszahlen like 111, 2222 days).
    *   Includes quarterly year markers (1/4 year, 1/2 year, etc.).
    *   **Custom Events:** Support for manual milestones (e.g., Engagement, House purchase).
    *   Shows the next 5 chronological events.
*   **Email Notification System:**
    *   **Automated Cron Job:** Reminders are sent automatically every day at **09:00 AM**.
    *   **Smart Timing:** Reminders are sent 7 days and 1 day before major milestones.
    *   **Multi-Recipient Support:** Configurable email addresses for both partners with individual enable/disable flags.
    *   **Test-Email Button:** Send a manual reminder email anytime to see the current upcoming milestone.
    *   **Dynamic Content:** Emails include 5 randomly selected surprise ideas to inspire you.
    *   **Schedule Info:** Displays the date of the next scheduled automatic email right on the page.
*   **Interactive History:** Displays a timeline of your relationship history.
*   **Theming:** Custom primary color support via configuration.
*   **Responsive Design:** Modern Glassmorphism aesthetic that looks great on mobile and desktop.
*   **Flexible Integration:** Use the standalone page (`/hochzeit/`) or embed it anywhere via shortcode.

## 🚀 Installation (WordPress)

1.  **Download:** Get the `hochzeitstag-plugin.zip` file from the `backend/` directory.
2.  **Upload:** Go to your WordPress Dashboard -> **Plugins -> Add New -> Upload Plugin**. Select the zip file and install.
3.  **Activate:** Activate the plugin.
4.  **Setup URL (Optional but recommended):**
    *   Go to **Settings -> Permalinks**.
    *   Click **"Save Changes"** (this flushes the rewrite rules to enable the `/hochzeit/` URL).
5.  **View:** 
    *   Visit `your-site.com/hochzeit/` for the standalone page.
    *   **OR** use the shortcode `[hochzeitstag]` on any existing page or post.

## ⚙️ Configuration
The configuration is located in `assets/config.js` (for the plugin) or `config.js` (for the standalone frontend).

### Visuals
*   `themeColor`: Hex code for the primary accent color (Default: `"#b76e79"`).
*   `backgroundImage`: Filename or URL for the header image.

### Dates
*   `weddingDate`: ISO 8601 format base for the countdown (e.g., `"2025-09-06T11:02:00"`).
*   `birthdays`: Key-value pairs for milestones (e.g., `klaus: "1967-08-02"`).
*   `customEvents`: Array of custom objects `{ date: "YYYY-MM-DD", label: "Event Name" }`.

### Notifications (Email)
*   `emailAddresses`: Define `husband` and `wife` details.
    *   `email`: Recipient address.
    *   `name`: Recipient name for the greeting.
    *   `sendEmail`: Boolean (`true`/`false`) to enable/disable notifications for this person.
*   `emailAutoSend`: Toggle automatic milestone reminders.
*   `emailReminderDays`: Array defining how many days before an event to send a mail (Default: `[7, 1]`).

### Content
*   `quotes`: Array of romantic or humorous quotes.
*   `surpriseIdeas`: Array of 100+ ideas for partner surprises.

## 🛠 Development
*   **Frontend:** Core logic in `frontend/`. Open `wedding.html` for local development.
*   **Backend:** WordPress plugin structure in `backend/hochzeitstag-plugin/`.

## 📜 License
Proprietary / Private use.