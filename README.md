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

## ⚙️ Configuration (Standalone HTML)

You can easily customize the wedding date and background image for the standalone version:

### Wedding Date

Edit the `WEDDING_DATE_STR` constant in `frontend/script.js`:

```javascript
const WEDDING_DATE_STR = "YYYY-MM-DDTHH:mm:ss"; 
```
Replace `YYYY-MM-DDTHH:mm:ss` with your specific wedding date and time (e.g., "2025-09-06T11:02:00").

### Background Image

The background image for the main card can be changed by replacing `frontend/kiss.jpeg` with your desired image. Make sure the new image is also named `kiss.jpeg` or update the `background-image` URL in `frontend/style.css`:

```css
.card::before {
    /* ... other styles ... */
    background-image: url('your-image-name.jpeg'); /* Update this line */
    /* ... other styles ... */
}
```

## 🚀 WordPress Plugin

A dedicated WordPress plugin is now available to easily integrate the wedding countdown into your WordPress site using a shortcode.

### ✨ Features

*   **Shortcode Integration**: Use `[hochzeitstag]` to display the countdown on any page, post, or widget.
*   **Self-contained Assets**: Includes all necessary CSS, JavaScript, and font files within the plugin for easy deployment and consistent styling.
*   **Configurable**: The `WEDDING_DATE_STR` can be updated directly within the plugin's `hochzeitstag-plugin.php` file for now (future versions might include admin settings).

### 📦 Installation

1.  **Download the plugin:**
    *   The plugin is available as `backend/hochzeitstag-plugin.zip` in this repository.
2.  **Upload via WordPress Admin:**
    *   Log in to your WordPress admin dashboard.
    *   Navigate to **Plugins > Add New**.
    *   Click the **"Upload Plugin"** button at the top of the page.
    *   Click **"Choose File"**, select `backend/hochzeitstag-plugin.zip` from your cloned repository, and click **"Install Now"**.
    *   After installation, click **"Activate Plugin"**.

### 💡 Usage

Once activated, simply add the shortcode `[hochzeitstag]` to any post, page, or text widget where you want the countdown to appear.

### ⚙️ Configuration (WordPress Plugin)

For now, the wedding date for the WordPress plugin needs to be edited directly in the plugin file.

Edit the `hochzeitstag_render_shortcode` function in `backend/hochzeitstag-plugin/hochzeitstag-plugin.php` to adjust the `WEDDING_DATE_STR` within the script block. (Note: Future versions might offer admin settings for easier configuration.)

```php
// Inside hochzeitstag_render_shortcode function
<script>
    const WEDDING_DATE_STR = "YYYY-MM-DDTHH:mm:ss"; // Adjust this line
    // ... rest of the script ...
</script>
```

Replace `YYYY-MM-DDTHH:mm:ss` with your specific wedding date and time.

## 💻 Technologies Used

*   HTML5
*   CSS3
*   JavaScript (ES6+)
*   WordPress (PHP)

## 🤝 Contributing

Suggestions and improvements are welcome! Feel free to fork the repository and submit pull requests.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.