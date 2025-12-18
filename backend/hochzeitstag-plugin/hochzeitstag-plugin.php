<?php
/**
 * Plugin Name: Hochzeitstag Countdown
 * Description: A romantic countdown to your wedding anniversary. Available at /hochzeit/
 * Version: 1.6
 * Author: Gemini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'HOCHZEITSTAG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOCHZEITSTAG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Define reminder days for email notifications
define( 'HOCHZEITSTAG_REMINDER_DAYS_FIRST', 7 ); // First reminder 7 days before
define( 'HOCHZEITSTAG_REMINDER_DAYS_SECOND', 1 ); // Second reminder 1 day before

/**
 * REWRITE RULES FOR /hochzeit/
 */
function hochzeitstag_rewrite_rule() {
    add_rewrite_rule( '^hochzeit/?$', 'index.php?hochzeitstag_page=1', 'top' );
}
add_action( 'init', 'hochzeitstag_rewrite_rule' );

function hochzeitstag_query_vars( $query_vars ) {
    $query_vars[] = 'hochzeitstag_page';
    return $query_vars;
}
add_filter( 'query_vars', 'hochzeitstag_query_vars' );

function hochzeitstag_template_include( $template ) {
    if ( get_query_var( 'hochzeitstag_page' ) ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'page-hochzeitstag.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'hochzeitstag_template_include' );

/**
 * Activation Hook to flush rules and schedule cron
 */
function hochzeitstag_activate() {
    hochzeitstag_rewrite_rule();
    flush_rewrite_rules();

    if ( ! wp_next_scheduled( 'hochzeitstag_daily_event' ) ) {
        // Schedule for 09:00:00
        $time = strtotime( 'tomorrow 09:00:00' );
        wp_schedule_event( $time, 'daily', 'hochzeitstag_daily_event' );
    }
}
register_activation_hook( __FILE__, 'hochzeitstag_activate' );

/**
 * Deactivation Hook to clear cron
 */
function hochzeitstag_deactivate() {
    wp_clear_scheduled_hook( 'hochzeitstag_daily_event' );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hochzeitstag_deactivate' );

/**
 * Cron Handler
 */
add_action( 'hochzeitstag_daily_event', 'hochzeitstag_cron_check' );
function hochzeitstag_cron_check() {
    // Attempt to send email with automatic date checking (force_send = false)
    _hochzeitstag_prepare_and_send_email( array( 'force_send' => false ) );
}

/**
 * Enqueue scripts and styles (Legacy/Shortcode support)
 */
function hochzeitstag_enqueue_assets() {
    // Only load assets if the shortcode is present (optional optimization, but good practice)
    // For simplicity, we load globally or check for post content. 
    // Here we load globally to ensure it works, but in production, conditional loading is better.
    
    // Enqueue Local Fonts
    wp_enqueue_style( 'hochzeitstag-fonts', plugins_url( 'assets/fonts/fonts.css', __FILE__ ), array(), '1.5' );



    // Enqueue Main Styles
    wp_enqueue_style( 'hochzeitstag-style', plugins_url( 'assets/style.css', __FILE__ ), array(), '1.5' );

    // Enqueue Script
    wp_enqueue_script( 'hochzeitstag-config', plugins_url( 'assets/config.js', __FILE__ ), array(), '1.5', true );
    wp_enqueue_script( 'hochzeitstag-script', plugins_url( 'assets/script.js', __FILE__ ), array('hochzeitstag-config'), '1.5', true );

    // Pass ajaxurl to our script
    wp_localize_script( 'hochzeitstag-script', 'hochzeitstag_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
// Removed global enqueue to prevent style leakage
// add_action( 'wp_enqueue_scripts', 'hochzeitstag_enqueue_assets' );

/**
 * Shortcode callback to render the countdown (Legacy support).
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
                        <div id="quote-display" class="quote-box">
                            </div>
                    </header>

                    

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
                            <span id="total-days">0</span> Tage gemeinsam
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

    // --- CONFIG EXTRACTION ---

    // 1. Dates
    $dates = array();
    
    // Wedding Date
    if ( preg_match( '/weddingDate:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $dates['wedding'] = $m[1];
    } else {
        return array( 'success' => false, 'message' => 'Fehler: Hochzeitsdatum (weddingDate) konnte nicht aus der Konfiguration gelesen werden.' );
    }

    // First Contact & Meet
    if ( preg_match( '/firstContactDate:\s*"([^"]+)"/', $config_js_content, $m ) ) $dates['contact'] = $m[1];
    if ( preg_match( '/firstMeetDate:\s*"([^"]+)"/', $config_js_content, $m ) )    $dates['meet'] = $m[1];

    // Birthdays
    $birthdays = array();
    if ( preg_match( '/birthdays:\s*\{(.*?)\}/s', $config_js_content, $m_block ) ) {
        if ( preg_match_all( '/(\w+):\s*"([^"]+)"/', $m_block[1], $m_bday ) ) {
            foreach ($m_bday[1] as $index => $key) {
                $birthdays[$key] = $m_bday[2][$index];
            }
        }
    }

    // 2. Reminder Days
    $reminder_days_first = 7;
    $reminder_days_second = 1;
    if ( preg_match( '/emailReminderDays:\s*\[\s*(\d+)\s*,\s*(\d+)\s*\]/', $config_js_content, $m ) ) {
        $reminder_days_first = intval($m[1]);
        $reminder_days_second = intval($m[2]);
    }

    // 3. Email Addresses
    $email_addresses = array();
    if ( preg_match( '/husband:\s*{\s*email:\s*"([^"]+)"\s*,\s*name:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $email_addresses['husband'] = array( 'email' => $m[1], 'name' => $m[2] );
    }
    if ( preg_match( '/wife:\s*{\s*email:\s*"([^"]+)"\s*,\s*name:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $email_addresses['wife'] = array( 'email' => $m[1], 'name' => $m[2] );
    }

    // 4. Quotes & Ideas
    $quotes = array();
    if ( preg_match( '/quotes:\s*\[(.*?)(\s*)\]/s', $config_js_content, $m_quotes_block ) ) {
        $quotes_content = $m_quotes_block[1];
        if ( preg_match_all( '/"([^"\\]*(?:\\.[^"\\]*)*)"/', $quotes_content, $m_quotes ) ) {
             $quotes = $m_quotes[1];
        }
    }
    if ( empty($quotes) ) $quotes = array("Liebe ist alles.");

    $surprise_ideas = array();
    if ( preg_match( '/surpriseIdeas:\s*\[(.*?)(\s*)\]/s', $config_js_content, $m_ideas_block ) ) {
        $ideas_content = $m_ideas_block[1];
         if ( preg_match_all( '/"([^"\\]*(?:\\.[^"\\]*)*)"/', $ideas_content, $m_ideas ) ) {
             $surprise_ideas = $m_ideas[1];
        }
    }

    // --- MILESTONE CALCULATION ---
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $wedding_date = new DateTime( $dates['wedding'] );
    $wedding_date->setTime(0, 0, 0);

    $upcoming_events = array();
    $one_day_interval = new DateInterval('P1D');

    // Helper to add annual event
    $add_annual = function($date_str, $label_base) use ($today, &$upcoming_events) {
        if (!$date_str) return;
        $base_date = new DateTime($date_str);
        $base_date->setTime(0,0,0);
        $current_year = $today->format('Y');
        
        // Check this year and next
        for ($y = $current_year; $y <= $current_year + 1; $y++) {
            $evt_date = clone $base_date;
            $evt_date->setDate($y, $base_date->format('m'), $base_date->format('d'));
            
            if ($evt_date >= $today) {
                // Calculate anniversary number if it's an annual thing
                $years = $y - $base_date->format('Y');
                $label = ($years > 0) ? "{$years}. {$label_base}" : $label_base;
                $upcoming_events[] = array('label' => $label, 'date' => $evt_date);
            }
        }
    };

    // A. Annual Events
    $add_annual($dates['wedding'], "Hochzeitstag");
    if (isset($dates['contact'])) $add_annual($dates['contact'], "Jahrestag (Erster Kontakt)");
    if (isset($dates['meet']))    $add_annual($dates['meet'], "Jahrestag (Zusammen)");

    // B. Birthdays
    foreach ($birthdays as $name => $date_str) {
        $name_uc = ucfirst($name);
        $add_annual($date_str, "Geburtstag {$name_uc}");
    }

    // C. 1000s & Repdigits (Schnapszahlen)
    $diff_days = $today->diff($wedding_date)->days;
    // Check next 2000 days range to cover upcoming
    $check_range = 2000; 
    
    // 1000s
    $next_thousand = ceil(($diff_days + 1) / 1000) * 1000;
    $date_thousand = clone $wedding_date;
    $date_thousand->modify("+$next_thousand days");
    $upcoming_events[] = array('label' => "{$next_thousand}. Tag gemeinsam", 'date' => $date_thousand);

    // Repdigits (111, 222 ... 1111, 2222 ... 11111)
    for ($digits = 3; $digits <= 5; $digits++) {
        for ($n = 1; $n <= 9; $n++) {
            $num = intval(str_repeat((string)$n, $digits));
            if ($num > $diff_days) {
                $d = clone $wedding_date;
                $d->modify("+$num days");
                // Only add if relatively close (within 2 years) to avoid huge array
                if ($d->diff($today)->days < 750) {
                     $upcoming_events[] = array('label' => "{$num}. Tag (Schnapszahl!)", 'date' => $d);
                }
            }
        }
    }

    // D. Quarter Years
    // Logic: Iterate quarters relative to wedding date
    $current_year_num = $today->format('Y');
    $wedding_year_num = $wedding_date->format('Y');
    
    for ($y_offset = -1; $y_offset <= 2; $y_offset++) {
        $target_year = $current_year_num + $y_offset;
        $years_passed = $target_year - $wedding_year_num;
        
        for ($q = 1; $q <= 3; $q++) { // 1=1/4, 2=1/2, 3=3/4
            $q_date = clone $wedding_date;
            $q_date->modify("+{$years_passed} years");
            $months_add = $q * 3;
            $q_date->modify("+{$months_add} months");
            
            if ($q_date >= $today) {
                $fraction = ($q === 1) ? "1/4" : (($q === 2) ? "1/2" : "3/4");
                $label_years = $years_passed;
                // If we added months, we are effectively in the year "years_passed".
                // Example: Wedding 2000. Target 2000. Q1 = +3 months. Label: "0 1/4 Jahre".
                // Target 2001. +3 months. Label: "1 1/4 Jahre".
                $upcoming_events[] = array('label' => "{$label_years} {$fraction} Jahre", 'date' => $q_date);
            }
        }
    }

    // --- TRIGGER LOGIC ---
    $target_event = null;
    $reminder_suffix = '';
    $force_send = (isset($atts['force_send']) && filter_var($atts['force_send'], FILTER_VALIDATE_BOOLEAN));
    
    // Sort events by date
    usort($upcoming_events, function($a, $b) {
        return $a['date'] <=> $b['date'];
    });

    // Check triggers
    foreach($upcoming_events as $evt) {
        // We need exact day match.
        // Diff returns absolute days usually, but let's be precise.
        // We know evt->date >= today.
        $interval = $today->diff($evt['date']);
        $days_until = $interval->days; // This is absolute difference

        // Safety check: verify date is in future
        if ($evt['date'] < $today) continue;

        if ($days_until == $reminder_days_first) {
            $target_event = $evt;
            $reminder_suffix = ' (in 7 Tagen)';
            break; // Prioritize closest (though sorting handles this, closest comes first)
        }
        if ($days_until == $reminder_days_second) {
            $target_event = $evt;
            $reminder_suffix = ' (Morgen!)';
            break;
        }
    }

    // Override if forced (Test Email)
    if ( $force_send ) {
        // Use provided label/date or fallback to first upcoming
        $lbl = isset($atts['event_label']) ? sanitize_text_field($atts['event_label']) : (isset($upcoming_events[0]) ? $upcoming_events[0]['label'] : 'Test-Event');
        $dt_val = isset($atts['event_date']) ? sanitize_text_field($atts['event_date']) : (isset($upcoming_events[0]) ? $upcoming_events[0]['date'] : $today);
        
        // Normalize date object if it came from array
        if (is_string($dt_val) && !empty($dt_val)) {
             // Try parsing german format dd.mm.yyyy? Or assume ISO?
             // Frontend sends dd.mm.yyyy usually (e.g. 05.09.2025)
             $dt_obj = DateTime::createFromFormat('d.m.Y', $dt_val);
             if (!$dt_obj) $dt_obj = new DateTime($dt_val); // Try ISO
        } elseif ($dt_val instanceof DateTime) {
            $dt_obj = $dt_val;
        } else {
            $dt_obj = $today;
        }

        $target_event = array(
            'label' => $lbl,
            'date'  => $dt_obj
        );
        $reminder_suffix = ' (Test)';
    }

    if ( ! $target_event ) {
        return array( 'success' => false, 'message' => 'Keine Erinnerung heute fällig.' );
    }

    // --- EMAIL PREPARATION ---
    $defaults = array(
        'to'            => isset( $email_addresses['husband']['email'] ) ? $email_addresses['husband']['email'] : '',
        'recipient_name'=> isset( $email_addresses['husband']['name'] ) ? $email_addresses['husband']['name'] : 'Liebe/r',
        'send_to_wife'  => false,
    );
    $parsed_atts = shortcode_atts( $defaults, $atts, 'hochzeitstag_email' );

    // Override recipient if send_to_wife
    if ( filter_var( $parsed_atts['send_to_wife'], FILTER_VALIDATE_BOOLEAN ) && isset( $email_addresses['wife'] ) ) {
        $parsed_atts['to'] = $email_addresses['wife']['email'];
        $parsed_atts['recipient_name'] = $email_addresses['wife']['name'];
    }

    $to_email = sanitize_email( $parsed_atts['to'] );
    $recipient_name = sanitize_text_field( $parsed_atts['recipient_name'] );
    
    if (empty($to_email)) {
         return array( 'success' => false, 'message' => 'Keine E-Mail-Adresse konfiguriert.' );
    }

    // Formatting date string
    $event_date_str = ($target_event['date'] instanceof DateTime) ? $target_event['date']->format('d.m.Y') : $target_event['date'];

    // Handle Ideas
    $ideas_list = array();
    // Check if passed via AJAX
    if ( isset($atts['ideas']) && is_array($atts['ideas']) ) {
        $ideas_list = array_map('sanitize_text_field', $atts['ideas']);
    }
    // If empty (automatic mode), pick random from config
    if ( empty($ideas_list) && !empty($surprise_ideas) ) {
        shuffle($surprise_ideas);
        $ideas_list = array_slice($surprise_ideas, 0, 5);
    }
    // Fallback
    if ( empty($ideas_list) ) {
        $ideas_list = array("Frühstück am Bett", "Ein kleiner Liebesbrief", "Gemeinsamer Spaziergang", "Essen bestellen", "Massieren");
    }

    $ideas_html = '<ul style="text-align: left; background: #fff; padding: 15px 15px 15px 30px; border-radius: 8px; border: 1px dashed #e91e63;">';
    foreach($ideas_list as $idea) {
        $ideas_html .= "<li style=\"margin-bottom: 8px; color: #555;\">{$idea}</li>";
    }
    $ideas_html .= '</ul>';

    $random_quote = $quotes[ array_rand( $quotes ) ];
    $greeting = empty($recipient_name) ? 'Hallo!' : "Hallo {$recipient_name}!";

    $subject = "📅 Countdown-Alarm: {$target_event['label']} steht an!";
    
    $message = "
        <html>
        <head>
            <title>Meilenstein-Alarm</title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; padding: 20px; color: #333; }
                .email-container { background-color: #ffffff; padding: 40px; border-radius: 12px; max-width: 600px; margin: 0 auto; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
                h2 { color: #b76e79; margin-top: 0; }
                .highlight-box { background: linear-gradient(135deg, #fff0f5 0%, #ffe6ee 100%); border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; border: 1px solid #ffcdd2; }
                .event-name { font-size: 1.4em; font-weight: bold; color: #880e4f; display: block; margin-bottom: 5px; }
                .event-date { font-size: 1.1em; color: #ad1457; }
                .intro-text { line-height: 1.6; font-size: 16px; color: #555; }
                .ideas-section { margin-top: 30px; }
                .ideas-title { font-weight: bold; color: #b76e79; font-size: 1.1em; margin-bottom: 10px; display: block; }
                .quote-box { font-style: italic; color: #777; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; }
                .footer { margin-top: 30px; font-size: 0.8em; color: #aaa; text-align: center; }
            </style>
        </head>
        <body>
            <div class=\"email-container\">
                <h2>{$greeting}</h2>
                
                <p class=\"intro-text\">
                    Aufgepasst! Eure gemeinsame Reise erreicht bald den nächsten wunderbaren Meilenstein.
                    Zeit, die Herzen höher schlagen zu lassen!
                </p>

                <div class=\"highlight-box\">
                    <span class=\"event-name\">{$target_event['label']}</span>
                    <span class=\"event-date\">am {$event_date_str}</span>
                </div>

                <div class=\"ideas-section\">
                    <span class=\"ideas-title\">💡 5 Ideen für eine kleine Überraschung:</span>
                    <p>Damit du nicht mit leeren Händen (oder leerem Kopf) dastehst, hier ein paar Inspirationen, um deinem Schatz ein Lächeln ins Gesicht zu zaubern:</p>
                    {$ideas_html}
                </div>

                <div class=\"quote-box\">
                    „{$random_quote}“
                </div>

                <div class=\"footer\">
                    <p>Gesendet mit Liebe vom Hochzeitstag Countdown Plugin.</p>
                </div>
            </div>
        </body>
        </html>
    ";

    $headers = array('Content-Type: text/html; charset=UTF-8');

    $sent = wp_mail( $to_email, $subject, $message, $headers );

    if ( $sent ) {
        return array( 'success' => true, 'message' => "E-Mail für <strong>{$target_event['label']}</strong> wurde gesendet." );
    } else {
        return array( 'success' => false, 'message' => "Fehler beim Senden der E-Mail." );
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
