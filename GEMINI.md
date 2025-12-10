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
*   `backend/`: Currently empty/unused.

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

## Development & Usage
*   **Running:** Open `frontend/wedding.html` directly in a modern web browser. No local server or build process is strictly required, though a simple HTTP server is recommended for better asset loading behavior in some browsers.
*   **Conventions:**
    *   **Localization:** UI text is in **German**. Code comments and variable names are in **English**.
    *   **Formatting:** Standard indentation (4 spaces or 2 spaces), clear separation of CSS, HTML, and JS blocks within the single file.
