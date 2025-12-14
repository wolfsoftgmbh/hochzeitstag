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

// Define reminder days for email notifications
define( 'HOCHZEITSTAG_REMINDER_DAYS_FIRST', 7 ); // First reminder 7 days before
define( 'HOCHZEITSTAG_REMINDER_DAYS_SECOND', 1 ); // Second reminder 1 day before

/**
 * Enqueue scripts and styles.
 */
function hochzeitstag_enqueue_assets() {
    // Only load assets if the shortcode is present (optional optimization, but good practice)
    // For simplicity, we load globally or check for post content. 
    // Here we load globally to ensure it works, but in production, conditional loading is better.
    
    // Enqueue Local Fonts
    wp_enqueue_style( 'hochzeitstag-fonts', plugins_url( 'assets/fonts/fonts.css', __FILE__ ), array(), '1.0' );

    // Enqueue Google Font 'Playfair Display'
    wp_enqueue_style( 'hochzeitstag-google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap', array(), null );

    // Enqueue Main Styles
    wp_enqueue_style( 'hochzeitstag-style', plugins_url( 'assets/style.css', __FILE__ ), array(), '1.0' );

    // Enqueue Script
    wp_enqueue_script( 'hochzeitstag-config', plugins_url( 'assets/config.js', __FILE__ ), array(), '1.0', true );
    wp_enqueue_script( 'hochzeitstag-script', plugins_url( 'assets/script.js', __FILE__ ), array('hochzeitstag-config'), '1.0', true );

    // Pass ajaxurl to our script
    wp_localize_script( 'hochzeitstag-script', 'hochzeitstag_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'hochzeitstag_enqueue_assets' );

/**
 * Shortcode callback to render the countdown.
 */
function hochzeitstag_render_shortcode() {
    ob_start();
    ?>
    <!-- Wrapper to potentially isolate styles if needed -->
    <div class="hochzeitstag-plugin-container">
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>

        <div class="container">
            <div class="glass-card">
                <div class="content-wrapper">
                    
                    <div class="card-header-image"></div>
                    <header class="header-section">
                        <h1>Unser Hochzeitstag</h1>
                        <div class="subtitle">Jeder Tag mit dir ist ein Geschenk</div>
                    </header>

                    <div id="quote-display" class="quote-box">
                        <!-- Quote will be inserted here by JavaScript -->
                    </div>

                    <div class="counter-grid">
                        <div class="glass-circle">
                            <span class="number" id="val-years">0</span>
                            <span class="label">Jahre</span>
                        </div>
                        <div class="glass-circle">
                            <span class="number" id="val-days">0</span>
                            <span class="label">Tage</span>
                        </div>
                        <div class="glass-circle">
                            <span class="number" id="val-hours">0</span>
                            <span class="label">Std</span>
                        </div>
                        <div class="glass-circle">
                            <span class="number" id="val-minutes">0</span>
                            <span class="label">Min</span>
                        </div>
                    </div>

                    <div class="section-title">Nächste Meilensteine</div>
                    <div class="timeline-container" id="milestone-list">
                        <!-- Milestones will be inserted here by JavaScript -->
                    </div>

                    <div class="section-title">Unsere Geschichte</div>
                    <div class="timeline-container history-mode" id="history-list">
                        <!-- History items will be inserted here by JavaScript -->
                    </div>

                    <div class="footer-stats">
                        <div class="stat-item">
                            <span id="total-days">0</span> Tage gesamt
                        </div>
                        <div class="stat-item">
                            <span id="total-seconds">0</span> Sekunden Liebe
                        </div>
                    </div>

                    <footer class="footer-info">
                        <div id="wedding-date-display" class="start-date"></div>
                        <button id="test-email-button" style="display:none;">Test E-Mail</button>
                        <div class="next-anniversary-pill" id="next-anniversary">
                            Berechne...
                        </div>
                    </footer>
                    
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'hochzeitstag', 'hochzeitstag_render_shortcode' );

/**
 * Helper function to prepare and send an email based on wedding configuration.
 *
 * @param array $atts Optional attributes to override config values for testing.
 * @return array Result of the email attempt (success/failure message).
 */
function _hochzeitstag_prepare_and_send_email( $atts = array() ) {
    if ( ! function_exists( 'wp_mail' ) ) {
        return array( 'success' => false, 'message' => 'WordPress Mail-Funktion (wp_mail) nicht verfügbar.' );
    }

    // Read config.js content
    $config_js_path = plugin_dir_path( __FILE__ ) . 'assets/config.js';
    $config_js_content = file_get_contents( $config_js_path );

    // Manual extraction of configuration variables to avoid fragile JSON parsing of JS files
    $wedding_date_str = '';
    // Match weddingDate: "..."
    if ( preg_match( '/weddingDate:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $wedding_date_str = $m[1];
    } else {
        return array( 'success' => false, 'message' => 'Fehler: Hochzeitsdatum (weddingDate) konnte nicht aus der Konfiguration gelesen werden.' );
    }

    $reminder_days_first = 7;
    if ( preg_match( '/emailReminderDaysFirst:\s*(\d+)/', $config_js_content, $m ) ) {
        $reminder_days_first = intval($m[1]);
    }
    
    $reminder_days_second = 1;
    if ( preg_match( '/emailReminderDaysSecond:\s*(\d+)/', $config_js_content, $m ) ) {
        $reminder_days_second = intval($m[1]);
    }

    $email_addresses = array();
    // Husband
    if ( preg_match( '/husband:\s*\{\s*email:\s*"([^"]+)"\s*,\s*name:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $email_addresses['husband'] = array( 'email' => $m[1], 'name' => $m[2] );
    }
    // Wife
    if ( preg_match( '/wife:\s*\{\s*email:\s*"([^"]+)"\s*,\s*name:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $email_addresses['wife'] = array( 'email' => $m[1], 'name' => $m[2] );
    }

    $quotes = array();
    if ( preg_match( '/quotes:\s*\[(.*?)\]/s', $config_js_content, $m_quotes_block ) ) {
        $quotes_content = $m_quotes_block[1];
        // Match all strings inside double quotes
        if ( preg_match_all( '/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $quotes_content, $m_quotes ) ) {
             $quotes = $m_quotes[1];
        }
    }
    if ( empty($quotes) ) {
        $quotes = array("Liebe ist alles."); // Fallback
    }

    $today = new DateTime();
    $wedding_date_config = new DateTime( $wedding_date_str );
    
    // Find the next upcoming anniversary based on the wedding date from config
    $next_anniversary = new DateTime( $wedding_date_str );
    while ($next_anniversary < $today) {
        $next_anniversary->modify('+1 year');
    }

    $diff_to_anniversary = $today->diff($next_anniversary);
    $days_to_anniversary = (int)$diff_to_anniversary->days;

    $send_first_reminder = false;
    $send_second_reminder = false;

    if ( $days_to_anniversary == $reminder_days_first ) {
        $send_first_reminder = true;
    }
    if ( $days_to_anniversary == $reminder_days_second ) {
        $send_second_reminder = true;
    }
    
    if ( ! $send_first_reminder && ! $send_second_reminder && ! (isset($atts['force_send']) && $atts['force_send']) ) {
        return array( 'success' => false, 'message' => 'Keine Erinnerung heute fällig.' );
    }

    $event_label_suffix = '';
    if ( $send_first_reminder ) {
        $event_label_suffix = ' (7-Tage-Erinnerung)';
    } elseif ( $send_second_reminder ) {
        $event_label_suffix = ' (1-Tag-Erinnerung)';
    }

    $defaults = array(
        'to'            => isset( $email_addresses['husband']['email'] ) ? $email_addresses['husband']['email'] : '',
        'event_label'   => 'Euer Hochzeitstag' . $event_label_suffix,
        'event_date'    => $next_anniversary->format( 'd.m.Y H:i' ),
        'recipient_name'=> isset( $email_addresses['husband']['name'] ) ? $email_addresses['husband']['name'] : 'Liebe/r',
        'send_to_wife'  => false,
    );

    $parsed_atts = shortcode_atts( $defaults, $atts, 'hochzeitstag_email' );

    // Override recipient if send_to_wife is true
    if ( filter_var( $parsed_atts['send_to_wife'], FILTER_VALIDATE_BOOLEAN ) && isset( $email_addresses['wife'] ) ) {
        $parsed_atts['to'] = $email_addresses['wife']['email'];
        $parsed_atts['recipient_name'] = $email_addresses['wife']['name'];
    }
    
    $to_email    = sanitize_email( $parsed_atts['to'] );
    $event_label = sanitize_text_field( $parsed_atts['event_label'] );
    $event_date  = sanitize_text_field( $parsed_atts['event_date'] );
    $recipient_name = sanitize_text_field( $parsed_atts['recipient_name'] );

    $greeting = empty($recipient_name) ? 'Hallo!' : "Hallo {$recipient_name}!";

    $random_quote = $quotes[ array_rand( $quotes ) ];

    $subject = 'Erinnerung: Ihr besonderes Ereignis mit Hochzeitstag Countdown';
    $message = "
        <html>
        <head>
            <title>Erinnerung an Ihr besonderes Ereignis!</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .email-container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                h2 { color: #e91e63; }
                p { color: #555555; line-height: 1.6; }
                .event-details { background-color: #fff0f5; border-left: 5px solid #e91e63; padding: 15px; margin: 20px 0; }
                .event-details p { margin: 5px 0; }
                .quote { font-style: italic; color: #777777; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eeeeee; }
                .footer { margin-top: 20px; font-size: 0.8em; color: #aaaaaa; text-align: center; }
            </style>
        </head>
        <body>
            <div class=\"email-container\">
                <h2>{$greeting}</h2>
                <p>Dies ist eine freundliche Erinnerung an Ihr bevorstehendes besonderes Ereignis:</p>
                <div class=\"event-details\">
                    <p><strong>Ereignis:</strong> {$event_label}</p>
                    <p><strong>Datum & Uhrzeit:</strong> {$event_date} Uhr</p>
                    <p>Merken Sie sich diesen wichtigen Tag vor!</p>
                </div>
                <p>Wir wünschen Ihnen viel Freude und unvergessliche Momente.</p>
                
                <div class=\"quote\">
                    <p>Ein kleiner Gruß, der Freude bringt:</p>
                    <p>{$random_quote}</p>
                </div>

                <p>Mit freundlichen Grüßen,</p>
                <p>Ihr Hochzeitstag Countdown Team</p>

                <div class=\"footer\">
                    <p>Diese E-Mail wurde vom Hochzeitstag Countdown Plugin gesendet.</p>
                </div>
            </div>
        </body>
        </html>
    ";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    $sent = wp_mail( $to_email, $subject, $message, $headers );

    if ( $sent ) {
        return array( 'success' => true, 'message' => "E-Mail wurde an <strong>{$to_email}</strong> gesendet." );
    } else {
        return array( 'success' => false, 'message' => "Fehler beim Senden der E-Mail an <strong>{$to_email}</strong>. Bitte prüfen Sie das Fehlerprotokoll Ihres Servers." );
    }
}

/**
 * Shortcode to trigger a test email.
 * This will now call the shared helper function.
 * Supports a 'force' attribute to bypass date checks (default: true).
 * Example: [hochzeitstag_test_email force="false"] to check dates.
 *
 * @param array $atts Shortcode attributes.
 * @return string Message indicating email status.
 */
function hochzeitstag_send_test_email_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'force' => 'true', // Default to true for backward compatibility and testing convenience
    ), $atts, 'hochzeitstag_test_email' );

    $force_send = filter_var( $atts['force'], FILTER_VALIDATE_BOOLEAN );

    // Merge force_send into the attributes passed to the helper
    // We pass the original $atts as well in case the user provided other overrides (like 'to')
    // but we filter 'force' out to avoid confusion, though array_merge handles overrides.
    $email_atts = array_merge( $atts, array( 'force_send' => $force_send ) );

    $result = _hochzeitstag_prepare_and_send_email( $email_atts );
    
    if ( $result['success'] ) {
        return $result['message'] . ' Bitte überprüfen Sie Ihren Posteingang (und Spam-Ordner).';
    } else {
        return $result['message'];
    }
}
add_shortcode( 'hochzeitstag_test_email', 'hochzeitstag_send_test_email_shortcode' );

/**
 * AJAX handler to send a test email.
 */
function hochzeitstag_ajax_send_test_email() {
    // Check for capabilities if this should be restricted
    // if ( ! current_user_can( 'manage_options' ) ) {
    //     wp_send_json_error( array( 'message' => 'Sie haben keine Berechtigung, diese Aktion auszuführen.' ) );
    // }

    $result = _hochzeitstag_prepare_and_send_email( $_POST ); // Pass POST data as attributes
    
    if ( $result['success'] ) {
        wp_send_json_success( array( 'message' => $result['message'] ) );
    } else {
        wp_send_json_error( array( 'message' => $result['message'] ) );
    }
    wp_die(); // Always include this to terminate script execution
}
add_action( 'wp_ajax_hochzeitstag_send_test_email', 'hochzeitstag_ajax_send_test_email' );
add_action( 'wp_ajax_nopriv_hochzeitstag_send_test_email', 'hochzeitstag_ajax_send_test_email' );