# Hochzeitstag Countdown

A charming and responsive single-page HTML application, now also available as a WordPress plugin, designed to celebrate a wedding anniversary. It beautifully displays the time elapsed since the wedding day and counts down to the next anniversary, all within a soft, romantic theme. **Google Fonts are now self-hosted for improved privacy and DSGVO compliance.**

## ✨ Features

*   **Time Tracking**: Displays years, days, hours, minutes, and seconds since the wedding.
*   **Total Time**: Shows total hours and total seconds elapsed.
*   **Next Anniversary Countdown**: Provides a clear countdown to the upcoming wedding anniversary, including the weekday and date.
*   **Humorous Quotes**: Features a random, light-hearted German quote on each page load, emphasizing togetherness and joy.
*   **Milestone Dates**: Highlights significant dates like the 100th, 200th, and 300th day, as well as 1/4, 1/2, and 3/4 year anniversaries.
*   **Responsive Design**: Adapts beautifully to various screen sizes, from desktop to mobile.
*   **Themable**: Soft pink gradient background, rounded info boxes, and custom background image for a personal touch.
*   **Localization**: All displayed text is in German, while the code and comments are in English.
*   **Self-hosted Fonts**: Utilizes locally hosted Google Fonts (`Baloo 2`) for enhanced privacy and performance, adhering to DSGVO regulations.

## 🏛️ Architecture

*   **Type:** Single-Page Application (SPA) / Static Site.
*   **Frontend Technology Stack:**
    *   **HTML5:** Semantic structure (`frontend/wedding.html`).
    *   **CSS3:** Custom styling with CSS Variables (`:root`) for easy theming (gradients, colors), Flexbox and Grid for layout (`frontend/style.css`, `frontend/fonts/fonts.css`).
    *   **JavaScript (ES6+):** Vanilla JS for date calculations, DOM manipulation, and random quote generation (`frontend/script.js`). No external frameworks or libraries are used.
*   **Backend (WordPress Plugin):** The `backend/hochzeitstag-plugin/hochzeitstag-plugin.php` file integrates the standalone frontend into WordPress. It enqueues all necessary frontend assets (copied to `backend/hochzeitstag-plugin/assets/`) and provides a `[hochzeitstag]` shortcode to embed the application's HTML structure directly into WordPress pages.

## ⚙️ Key Configurations

Configuration is primarily handled within the `frontend/config.js` file for the standalone version, which is then bundled with the WordPress plugin.

### 1. Wedding and Other Significant Dates

The main dates are configured in `frontend/config.js` within the `HOCHZEITSTAG_CONFIG` object:

```javascript
const HOCHZEITSTAG_CONFIG = {
    weddingDate: "YYYY-MM-DDTHH:mm:ss",     // Your wedding date (e.g., "2025-09-06T11:02:00")
    firstContactDate: "YYYY-MM-DDTHH:mm:ss",// Optional: Date of first contact
    firstMeetDate: "YYYY-MM-DDTHH:mm:ss",   // Optional: Date of first meeting
    // ... other configurations
};
```
Replace `YYYY-MM-DDTHH:mm:ss` with your specific dates and times.

### 2. Visual Theme & Background Image

*   **CSS Variables:** Customize colors and gradients by modifying CSS variables in `:root` within `frontend/style.css` (e.g., `--bg-gradient-start`, `--primary-pink`).
*   **Background Image:** The background image for the main card is defined in `frontend/config.js` and referenced in `frontend/style.css`.
    *   To change the image, update `backgroundImage` in `frontend/config.js`:
        ```javascript
        backgroundImage: 'url("kiss.jpeg")', // Path relative to the CSS file
        ```
    *   Ensure the image file (e.g., `kiss.jpeg`) is located in the same directory as `frontend/wedding.html` (for standalone) or `backend/hochzeitstag-plugin/assets/` (for WordPress plugin).

### 3. Quotes

A JavaScript array `quotes` in `frontend/config.js` contains localized (German) strings:

```javascript
const HOCHZEITSTAG_CONFIG = {
    // ...
    quotes: [
        "Ein Leben ohne Liebe ist wie ein Garten ohne Sonne, in dem die Blumen gestorben sind.",
        "Liebe ist nicht das, was man erwartet zu bekommen, sondern das, was man bereit ist zu geben.",
        // ... more quotes
    ],
};
```

## 🚀 Getting Started (Standalone HTML)

To view the standalone HTML application, simply clone the repository and open the `wedding.html` file in your web browser.

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/wolfsoftgmbh/hochzeitstag.git
    ```
2.  **Navigate to the frontend directory:**
    ```bash
    cd hochzeitstag/frontend
    ```
3.  **Open in your browser:**
    Open `wedding.html` with your preferred web browser (e.g., `file:///path/to/hochzeitstag/frontend/wedding.html`).

## 🚀 WordPress Plugin

A dedicated WordPress plugin is now available to easily integrate the wedding countdown into your WordPress site using a shortcode.

### ✨ Features (Plugin Specific)

*   **Shortcode Integration**: Use `[hochzeitstag]` to display the countdown on any page, post, or widget.
*   **Self-contained Assets**: Includes all necessary CSS, JavaScript, and font files within the plugin for easy deployment and consistent styling.
*   **Configurable**: The plugin uses the `config.js` from the frontend, making it easy to configure dates and content by modifying the `config.js` in the plugin's `assets` folder after installation (or before packaging).

### 📦 Installation

1.  **Download the plugin:**
    *   The plugin is available as `backend/hochzeitstag-plugin.zip` in this repository.
2.  **Upload via WordPress Admin:**
    *   Log in to your WordPress admin dashboard.
    *   Navigate to **Plugins > Add New**.
    *   Click the **"Upload Plugin"** button at the top of the page.
    *   Click **"Choose File"**, select `backend/hochzeitstag-plugin.zip` from your cloned repository, and click **"Install Now"**.
    *   After installation, click **"Activate Plugin"**.

### 💡 Usage (Plugin)

Once activated, simply add the shortcode `[hochzeitstag]` to any post, page, or text widget where you want the countdown to appear.

### ⚙️ Configuration (WordPress Plugin)

For the WordPress plugin, the `config.js` file is located within the plugin's `assets` folder after installation. To modify the wedding date or other configurations:

1.  **Locate the plugin files:** Access your WordPress installation files (via FTP or hosting file manager).
2.  **Navigate to the plugin's assets folder:** `wp-content/plugins/hochzeitstag-plugin/assets/`
3.  **Edit `config.js`:** Open `config.js` and update the `HOCHZEITSTAG_CONFIG` variables as described in the "Wedding and Other Significant Dates" section above.

**Note:** If you are packaging the plugin yourself, you should modify `frontend/config.js` before zipping `hochzeitstag-plugin/` to ensure your desired default configuration is included.

## 💻 Development & Usage

*   **Running:** To run the standalone version, open `frontend/wedding.html` directly in a modern web browser. A simple HTTP server is recommended for better asset loading behavior in some browsers.
*   **Conventions:**
    *   **Localization:** UI text is in **German**. Code comments and variable names are in **English**.
    *   **Formatting:** Standard indentation (4 spaces), clear separation of CSS, HTML, and JS blocks.

## 🌍 Technologies Used

*   HTML5
*   CSS3 (with CSS Variables, Flexbox, Grid)
*   JavaScript (ES6+, Vanilla JS)
*   WordPress (PHP for plugin integration)

## 🤝 Contributing

Suggestions and improvements are welcome! Feel free to fork the repository and submit pull requests.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.
