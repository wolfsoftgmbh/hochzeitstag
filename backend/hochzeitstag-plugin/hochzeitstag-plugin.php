<?php
/**
 * Plugin Name: Hochzeitstag Countdown
 * Description: A romantic countdown to your wedding anniversary. Use shortcode [hochzeitstag] to display.
 * Version: 1.0
 * Author: Gemini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue scripts and styles.
 */
function hochzeitstag_enqueue_assets() {
    // Only load assets if the shortcode is present (optional optimization, but good practice)
    // For simplicity, we load globally or check for post content. 
    // Here we load globally to ensure it works, but in production, conditional loading is better.
    
    // Enqueue Local Fonts
    wp_enqueue_style( 'hochzeitstag-fonts', plugins_url( 'assets/fonts/fonts.css', __FILE__ ), array(), '1.0' );

    // Enqueue Main Styles
    wp_enqueue_style( 'hochzeitstag-style', plugins_url( 'assets/style.css', __FILE__ ), array(), '1.0' );

    // Enqueue Script
    wp_enqueue_script( 'hochzeitstag-config', plugins_url( 'assets/config.js', __FILE__ ), array(), '1.0', true );
    wp_enqueue_script( 'hochzeitstag-script', plugins_url( 'assets/script.js', __FILE__ ), array('hochzeitstag-config'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'hochzeitstag_enqueue_assets' );

/**
 * Shortcode callback to render the countdown.
 */
function hochzeitstag_render_shortcode() {
    ob_start();
    ?>
    <!-- Wrapper to potentially isolate styles if needed, though scoped CSS is hard without Shadow DOM. 
         The styles in style.css are quite specific (.card, .container) but might conflict with themes. 
         For now, we use the provided structure. -->
    <div class="hochzeitstag-plugin-container">
        <div class="container">
            <div class="card">
                <div class="content-wrapper">
                    <!-- Header -->
                    <h1>Unser Hochzeitstag</h1>
                    <div class="subtitle">Zeit seit dem schönsten Tag unseres Lebens!</div>

                    <!-- Quote Block -->
                    <div id="quote-display" class="quote-box">
                        <!-- Quote will be inserted here by JavaScript -->
                    </div>

                    <!-- Row 1: Years, Days, Hours, Minutes -->
                    <div class="row-grid">
                        <div class="box">
                            <span class="number" id="val-years">0</span>
                            <span class="label">Jahre</span>
                        </div>
                        <div class="box">
                            <span class="number" id="val-days">0</span>
                            <span class="label">Tage</span>
                        </div>
                        <div class="box">
                            <span class="number" id="val-hours">0</span>
                            <span class="label">Stunden</span>
                        </div>
                        <div class="box">
                            <span class="number" id="val-minutes">0</span>
                            <span class="label">Minuten</span>
                        </div>
                    </div>



                    <!-- Row 3: Total Stats -->
                    <div class="stats-row">
                        <div class="box stats-box">
                            <span class="number" id="total-hours">0</span>
                            <span class="label">Gesamte Stunden</span>
                        </div>
                        <div class="box stats-box">
                            <span class="number" id="total-seconds">0</span>
                            <span class="label">Gesamte Sekunden</span>
                        </div>
                    </div>

                    <!-- Milestone Box -->
                    <div class="milestone-box">
                        <div class="milestone-title">Besondere Tage</div>
                        <div class="history-table-container">
                            <table class="history-table" id="milestone-table">
                                <thead>
                                    <tr>
                                        <th>Ereignis</th>
                                        <th>Datum</th>
                                        <th>Tage</th>
                                    </tr>
                                </thead>
                                <tbody id="milestone-list">
                                    <!-- Milestones will be inserted here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- History Box -->
                    <div class="milestone-box">
                        <div class="milestone-title">Unsere Geschichte</div>
                        <div class="history-table-container">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Ereignis</th>
                                        <th>Jahre</th>
                                        <th>Tage</th>
                                        <th>Std</th>
                                        <th>Min</th>
                                        <th>Sek</th>
                                    </tr>
                                </thead>
                                <tbody id="history-list">
                                    <!-- History items will be inserted here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer-info">
                        <span class="start-date" id="wedding-date-display"></span>
                        <div class="countdown-pill" id="next-anniversary">
                            Berechne...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'hochzeitstag', 'hochzeitstag_render_shortcode' );