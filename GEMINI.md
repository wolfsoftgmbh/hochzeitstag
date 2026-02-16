# Project Context: Hochzeitstag Countdown

## Overview
This project is a standalone, client-side web application designed to track time since a specific wedding date and countdown to the next anniversary. It features a responsive, romantic design with a "soft pink" (or configurable) theme.

## Architecture
*   **Type:** Single-Page Application (SPA) / Static Site.
*   **Technology Stack:**
    *   **HTML5:** Semantic structure.
    *   **CSS3:** Custom styling with CSS Variables (`:root`) for easy theming (gradients, colors). Uses Flexbox and Grid for layout.
    *   **JavaScript (ES6+):** Vanilla JS for date calculations, DOM manipulation, and random quote generation. No external frameworks or libraries.
*   **Asset Management:** Images (e.g., `kiss.jpeg`) are referenced directly in CSS.

## Directory Structure
*   `frontend/`: Contains the main application files.
    *   `wedding.html`: The core file containing HTML, CSS (`<style>`), and JavaScript (`<script>`).
    *   `kiss.jpeg`: Background image for the main card.
*   `backend/`: Contains the WordPress Plugin.
    *   `hochzeitstag-plugin/`: Source code of the plugin.
    *   `hochzeitstag-plugin_v2.5.zip`: Latest distributable plugin file.

## Key Configurations
configuration is primarily handled within the `frontend/wedding.html` file:

1.  **Wedding Date:**
    *   Variable: `const WEDDING_DATE_STR` (JavaScript).
    *   Format: ISO 8601 string (e.g., `"2025-09-06T11:02:00"`).

2.  **Visual Theme:**
    *   CSS Variables in `:root`:
        *   `--bg-gradient-start` / `--bg-gradient-end`: Controls the page background gradient.
        *   `--primary-pink`: Main accent color.
    *   **Background Image:** defined in `.card::before` CSS rule.

3.  **Content:**
    *   **Quotes:** A JavaScript array `const quotes` contains localized (German) strings.
    *   **Milestones:** Logic in `calculateMilestones()` function.

## WordPress Plugin (Backend)
The `backend/hochzeitstag-plugin` is a full WordPress plugin that wraps the frontend logic and adds server-side email notifications.
*   **Version:** 2.12.3
*   **Versioning Standard:** 
    *   Version is managed via the `HOCHZEITSTAG_VERSION` constant in `hochzeitstag-plugin.php`.
    *   The version number MUST be updated in the plugin header, the constant, the `<h1>` of the admin page, and the `<h1>` of the frontend page.
    *   Always increment by 0.0.1 for fixes and 0.1.0 for features.
*   **Email Logic:** Sends emails on configured days before an event (default: 7, 1, and 0/Today).
*   **Cron:** Runs daily. If configured time is in the past for the current day, it runs immediately upon saving settings.
