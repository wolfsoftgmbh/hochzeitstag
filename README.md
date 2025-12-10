# Hochzeitstag Countdown

A charming and responsive single-page HTML application designed to celebrate a wedding anniversary. It beautifully displays the time elapsed since the wedding day and counts down to the next anniversary, all within a soft, romantic theme.

## ✨ Features

*   **Time Tracking**: Displays years, days, hours, minutes, and seconds since the wedding.
*   **Total Time**: Shows total hours and total seconds elapsed.
*   **Next Anniversary Countdown**: Provides a clear countdown to the upcoming wedding anniversary, including the weekday and date.
*   **Humorous Quotes**: Features a random, light-hearted German quote on each page load, emphasizing togetherness and joy.
*   **Milestone Dates**: Highlights significant dates like the 100th, 200th, and 300th day, as well as 1/4, 1/2, and 3/4 year anniversaries.
*   **Responsive Design**: Adapts beautifully to various screen sizes, from desktop to mobile.
*   **Themable**: Soft pink gradient background, rounded info boxes, and custom background image for a personal touch.
*   **Localization**: All displayed text is in German, while the code and comments are in English.

## 🚀 Getting Started

To view this application, simply clone the repository and open the `wedding.html` file in your web browser.

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

## 🛠️ Configuration

You can easily customize the wedding date and background image:

### Wedding Date

Edit the `WEDDING_DATE_STR` constant in `frontend/wedding.html`:

```javascript
const WEDDING_DATE_STR = "YYYY-MM-DDTHH:mm:ss"; 
```
Replace `YYYY-MM-DDTHH:mm:ss` with your specific wedding date and time (e.g., "2025-09-06T11:02:00").

### Background Image

The background image for the main card can be changed by replacing `frontend/kiss.jpeg` with your desired image. Make sure the new image is also named `kiss.jpeg` or update the `background-image` URL in the `<style>` block of `frontend/wedding.html`:

```css
.card::before {
    /* ... other styles ... */
    background-image: url('your-image-name.jpeg'); /* Update this line */
    /* ... other styles ... */
}
```

## 💻 Technologies Used

*   HTML5
*   CSS3
*   JavaScript (ES6+)

## 🤝 Contributing

Suggestions and improvements are welcome! Feel free to fork the repository and submit pull requests.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.
