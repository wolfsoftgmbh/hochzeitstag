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
                    <div id="quote-display" class="quote-box anker-zitate">
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

/**
 * Temporary shortcode to send a test email.
 * Remove this once email functionality is confirmed.
 *
 * @param array $atts Shortcode attributes.
 * @return string Message indicating email status.
 */
function hochzeitstag_send_test_email_shortcode( $atts ) {
    if ( ! function_exists( 'wp_mail' ) ) {
        return 'WordPress Mail-Funktion (wp_mail) nicht verfügbar.';
    }

        // Read config.js content

        $config_js_path = plugin_dir_path( __FILE__ ) . 'assets/config.js';

        $config_js_content = file_get_contents( $config_js_path );

    

        // Extract HOCHZEITSTAG_CONFIG object using regex

        // This regex is basic and might need refinement for more complex JS objects

        preg_match( '/const HOCHZEITSTAG_CONFIG = (\{[^;]+\});/s', $config_js_content, $matches );

        $hochzeitstag_config_json = isset( $matches[1] ) ? $matches[1] : '{}';

    

        // Convert to valid JSON (replace single quotes with double quotes for keys/values)

        // This is a simplistic approach; a proper JS parser would be more robust.

        $hochzeitstag_config_json = preg_replace( "/(\w+):/", "\"
    \":", $hochzeitstag_config_json ); // keys

        $hochzeitstag_config_json = str_replace( "'", "\"", $hochzeitstag_config_json ); // string values

    

        // Decode JSON into PHP array

        $hochzeitstag_config = json_decode( $hochzeitstag_config_json, true );

    

        if ( ! $hochzeitstag_config ) {

            return 'Fehler: Konfiguration konnte nicht geladen oder geparst werden.';

        }

    

        $wedding_date_str = $hochzeitstag_config['weddingDate'];

        $reminder_days_first = defined( 'HOCHZEITSTAG_REMINDER_DAYS_FIRST' ) ? HOCHZEITSTAG_REMINDER_DAYS_FIRST : ( isset( $hochzeitstag_config['emailReminderDaysFirst'] ) ? $hochzeitstag_config['emailReminderDaysFirst'] : 7 );

        $reminder_days_second = defined( 'HOCHZEITSTAG_REMINDER_DAYS_SECOND' ) ? HOCHZEITSTAG_REMINDER_DAYS_SECOND : ( isset( $hochzeitstag_config['emailReminderDaysSecond'] ) ? $hochzeitstag_config['emailReminderDaysSecond'] : 1 );

        $quotes = $hochzeitstag_config['quotes'];

        $email_addresses = $hochzeitstag_config['emailAddresses'];

    

        $today = new DateTime();

        $wedding_date_config = new DateTime( $wedding_date_str );

        

        // Find the next upcoming anniversary based on the wedding date from config

        $next_anniversary = new DateTime( $wedding_date_str );

        // Adjust year to ensure it's the next upcoming anniversary

        while ($next_anniversary < $today) {

            $next_anniversary->modify('+1 year');

        }

    

        $diff_to_anniversary = $today->diff($next_anniversary);

        $days_to_anniversary = (int)$diff_to_anniversary->days;

    

        $send_first_reminder = false;

        $send_second_reminder = false;

    

        // Check if it's the day for the first reminder

        if ( $days_to_anniversary == $reminder_days_first ) {

            $send_first_reminder = true;

        }

    

        // Check if it's the day for the second reminder

        if ( $days_to_anniversary == $reminder_days_second ) {

            $send_second_reminder = true;

        }

        

        // If no reminder is due, exit

        if ( ! $send_first_reminder && ! $send_second_reminder ) {

            return 'Keine Erinnerung heute fällig.';

        }

    

        $event_label_suffix = '';

        if ( $send_first_reminder ) {

            $event_label_suffix = ' (7-Tage-Erinnerung)';

        } elseif ( $send_second_reminder ) {

            $event_label_suffix = ' (1-Tag-Erinnerung)';

        }

    

        $atts = shortcode_atts(

            array(

                'to'            => isset( $email_addresses['husband']['email'] ) ? $email_addresses['husband']['email'] : '',

                'event_label'   => 'Euer Hochzeitstag' . $event_label_suffix,

                'event_date'    => $next_anniversary->format( 'd.m.Y H:i' ),

                'recipient_name'=> isset( $email_addresses['husband']['name'] ) ? $email_addresses['husband']['name'] : 'Liebe/r',

                'send_to_wife'  => false, // Option to send to wife instead

            ),

            $atts,

            'hochzeitstag_test_email'

        );

    

        // Override recipient if send_to_wife is true

        if ( filter_var( $atts['send_to_wife'], FILTER_VALIDATE_BOOLEAN ) && isset( $email_addresses['wife'] ) ) {

            $atts['to'] = $email_addresses['wife']['email'];

            $atts['recipient_name'] = $email_addresses['wife']['name'];

        }

        

        $to_email    = sanitize_email( $atts['to'] );

        $event_label = sanitize_text_field( $atts['event_label'] );

        $event_date  = sanitize_text_field( $atts['event_date'] );

        $recipient_name = sanitize_text_field( $atts['recipient_name'] );

    

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
        return "Test E-Mail wurde an <strong>{$to_email}</strong> gesendet. Bitte überprüfen Sie Ihren Posteingang (und Spam-Ordner).";
    } else {
        return "Fehler beim Senden der Test E-Mail an <strong>{$to_email}</strong>. Bitte prüfen Sie das Fehlerprotokoll Ihres Servers.";
    }
}
add_shortcode( 'hochzeitstag_test_email', 'hochzeitstag_send_test_email_shortcode' );