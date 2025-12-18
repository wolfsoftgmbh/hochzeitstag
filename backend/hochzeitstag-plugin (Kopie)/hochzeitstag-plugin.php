<?php
/**
 * Plugin Name: Hochzeitstag Countdown
 * Description: A romantic countdown to your wedding anniversary. Shows on page /hochzeit/
 * Version: 1.5
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
 * PAGE & TEMPLATE HANDLING
 */

// 1. Create Page on Activation
function hochzeitstag_create_page() {
    $page_title = 'Hochzeitstag';
    $page_slug = 'hochzeit';
    $page_content = ''; // Content handled by template

    $page_check = get_page_by_path( $page_slug );
    $new_page = array(
        'post_type'     => 'page',
        'post_title'    => $page_title,
        'post_name'     => $page_slug,
        'post_content'  => $page_content,
        'post_status'   => 'publish',
        'post_author'   => 1,
    );

    if ( ! isset( $page_check->ID ) ) {
        wp_insert_post( $new_page );
    }
    
    // Schedule cron
    if ( ! wp_next_scheduled( 'hochzeitstag_daily_event' ) ) {
        wp_schedule_event( time(), 'daily', 'hochzeitstag_daily_event' );
    }
}
register_activation_hook( __FILE__, 'hochzeitstag_create_page' );

// 2. Load Custom Template for specific page
function hochzeitstag_load_template( $template ) {
    if ( is_page( 'hochzeit' ) ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'page-hochzeitstag.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'hochzeitstag_load_template' );

/**
 * Deactivation Hook
 */
function hochzeitstag_deactivate() {
    wp_clear_scheduled_hook( 'hochzeitstag_daily_event' );
}
register_deactivation_hook( __FILE__, 'hochzeitstag_deactivate' );

/**
 * Daily Cron Callback
 * Checks all milestones and sends emails if due.
 */
function hochzeitstag_do_daily_check() {
    _hochzeitstag_check_all_milestones();
}
add_action( 'hochzeitstag_daily_event', 'hochzeitstag_do_daily_check' );

/**
 * Check all future milestones and trigger emails if a reminder is due today.
 */
function _hochzeitstag_check_all_milestones() {
    // 1. Get Config
    $config_js_path = plugin_dir_path( __FILE__ ) . 'assets/config.js';
    $config_js_content = file_get_contents( $config_js_path );

    $wedding_date_str = '';
    if ( preg_match( '/weddingDate:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $wedding_date_str = $m[1];
    } else {
        error_log('Hochzeitstag Plugin: Wedding date not found in config.');
        return;
    }

    $birthdays = array();
    // Simple regex to find birthdays object block, then parse lines
    if ( preg_match( '/birthdays:\s*\{(.*?)\}/s', $config_js_content, $m_bday_block ) ) {
        $bday_lines = explode( ',', $m_bday_block[1] );
        foreach ( $bday_lines as $line ) {
            if ( preg_match( '/(\w+):\s*"([^"]+)"/', $line, $m_b ) ) {
                $birthdays[ trim($m_b[1]) ] = $m_b[2]; // name => date
            }
        }
    }

    $reminder_days_first = 7;
    if ( preg_match( '/emailReminderDaysFirst:\s*(\d+)/', $config_js_content, $m ) ) {
        $reminder_days_first = intval($m[1]);
    }
    
    $reminder_days_second = 1;
    if ( preg_match( '/emailReminderDaysSecond:\s*(\d+)/', $config_js_content, $m ) ) {
        $reminder_days_second = intval($m[1]);
    }

    // 2. Generate Milestones (PHP version of JS logic)
    $wedding_date = new DateTime( $wedding_date_str );
    $today = new DateTime(); // Use server time, or current_time('mysql')? DateTime() uses server timezone usually.
    // Reset time to 00:00:00 for accurate day comparison
    $today->setTime(0, 0, 0); 
    
    $milestones = array();

    // Helper to add annual events
    $current_year = (int)$today->format('Y');
    for ($y = $current_year; $y <= $current_year + 2; $y++) {
        // A. Wedding Anniversaries
        $anniv_date = new DateTime( $wedding_date_str );
        $anniv_date->setDate( $y, $anniv_date->format('m'), $anniv_date->format('d') );
        $anniv_date->setTime(0, 0, 0);
        
        if ( $anniv_date >= $today ) {
            $num = $y - (int)$wedding_date->format('Y');
            if ( $num > 0 ) {
                $milestones[] = array( 'date' => $anniv_date, 'label' => "{$num}. Hochzeitstag" );
            }
        }

        // B. Birthdays
        foreach ( $birthdays as $name => $bday_str ) {
            $bday_date = new DateTime( $bday_str );
            $target_bday = new DateTime( $bday_str );
            $target_bday->setDate( $y, $bday_date->format('m'), $bday_date->format('d') );
            $target_bday->setTime(0, 0, 0);

            if ( $target_bday >= $today ) {
                $formatted_name = ucfirst($name);
                $milestones[] = array( 'date' => $target_bday, 'label' => "Geburtstag {$formatted_name}" );
            }
        }

        // C. Quarterly Years
        // Iterate relative to wedding year
        $diff_year = $y - (int)$wedding_date->format('Y');
        // Logic: 1/4, 1/2, 3/4
        for ($q = 1; $q <= 3; $q++) {
            $q_date = clone $wedding_date;
            // Add years + quarter months
            // We want (Wedding Date + Y years + Q*3 months)
            // Wait, JS logic: y is calendar year.
            // Let's replicate strict logic:
            // Calculate target date: WeddingDate + (diff_year years) + (q*3 months)
            // Check if year matches $y? Or just add generally. 
            
            // Simplified: Generate quarters for current timeframe
            $base_years = $diff_year; 
            // Try base_years and base_years-1 to cover overlap
            foreach ([$base_years, $base_years-1] as $by) {
                if ($by < 0) continue;
                $target_q_date = clone $wedding_date;
                $target_q_date->modify("+{$by} years");
                $target_q_date->modify("+" . ($q*3) . " months");
                $target_q_date->setTime(0,0,0);

                if ( $target_q_date >= $today ) {
                    // Check if it's too far in future? Limit to e.g. 1 year ahead
                    $interval = $today->diff($target_q_date);
                    if ( $interval->days > 365 ) continue;

                    $fraction = ($q == 1) ? "1/4" : (($q == 2) ? "1/2" : "3/4");
                    $label = "{$by} {$fraction} Jahre";
                    if ($by == 0) $label = "{$fraction} Jahr";
                    
                    // Avoid duplicates if added multiple times
                    $milestones[] = array( 'date' => $target_q_date, 'label' => $label );
                }
            }
        }
    }

    // D. Schnapszahlen (Repdigits)
    $diff_seconds = $today->getTimestamp() - $wedding_date->getTimestamp();
    $days_passed = floor($diff_seconds / 86400); // Rough days passed
    $start_day = max(0, $days_passed);
    
    for ($digits = 3; $digits <= 5; $digits++) {
        for ($n = 1; $n <= 9; $n++) {
            $num = (int)str_repeat((string)$n, $digits);
            if ($num > $start_day) {
                $target_s_date = clone $wedding_date;
                $target_s_date->modify("+{$num} days");
                $target_s_date->setTime(0,0,0);
                
                // Limit to near future (e.g. 1 year)
                $interval = $today->diff($target_s_date);
                if ( $interval->days > 365 ) continue;

                $milestones[] = array( 'date' => $target_s_date, 'label' => "{$num}. Tag" );
            }
        }
    }

    // 3. Check Milestones against Reminder Days
    // We want to send email if: Milestone Date == Today + X Days
    $target_date_1 = clone $today;
    $target_date_1->modify("+{$reminder_days_first} days");
    
    $target_date_2 = clone $today;
    $target_date_2->modify("+{$reminder_days_second} days");

    foreach ($milestones as $m) {
        $m_date = $m['date'];
        $m_label = $m['label'];
        
        $send = false;
        $suffix = '';

        // Strict comparison Y-m-d
        if ( $m_date->format('Y-m-d') === $target_date_1->format('Y-m-d') ) {
            $send = true;
            $suffix = " (in {$reminder_days_first} Tagen)";
        } elseif ( $m_date->format('Y-m-d') === $target_date_2->format('Y-m-d') ) {
            $send = true;
            $suffix = " (Morgen)";
        }

        if ( $send ) {
            // Send to Husband
            _hochzeitstag_prepare_and_send_email( array(
                'force_send' => true,
                'event_label' => $m_label . $suffix,
                'event_date' => $m_date->format('d.m.Y'),
                'send_to_wife' => false
            ));
            
            // Send to Wife
            _hochzeitstag_prepare_and_send_email( array(
                'force_send' => true,
                'event_label' => $m_label . $suffix,
                'event_date' => $m_date->format('d.m.Y'),
                'send_to_wife' => true
            ));
        }
    }
}

/**
 * Enqueue scripts and styles (Legacy/Shortcode support)
 */
function hochzeitstag_enqueue_assets() {
    // Pass ajaxurl to our script
    wp_localize_script( 'hochzeitstag-script', 'hochzeitstag_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

/**
 * Helper function to prepare and send an email.
 *
 * @param array $atts Attributes overriding defaults.
 * @return array Result.
 */
function _hochzeitstag_prepare_and_send_email( $atts = array() ) {
    if ( ! function_exists( 'wp_mail' ) ) {
        return array( 'success' => false, 'message' => 'WordPress Mail-Funktion (wp_mail) nicht verfügbar.' );
    }

    // Config extraction (Repeated here or passed? Better to repeat for robustness if called isolated)
    $config_js_path = plugin_dir_path( __FILE__ ) . 'assets/config.js';
    $config_js_content = file_get_contents( $config_js_path );

    $email_addresses = array();
    if ( preg_match( '/husband:\s*{\s*email:\s*"([^"]+)"\s*,\s*name:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $email_addresses['husband'] = array( 'email' => $m[1], 'name' => $m[2] );
    }
    if ( preg_match( '/wife:\s*{\s*email:\s*"([^"]+)"\s*,\s*name:\s*"([^"]+)"/', $config_js_content, $m ) ) {
        $email_addresses['wife'] = array( 'email' => $m[1], 'name' => $m[2] );
    }

    $quotes = array();
    if ( preg_match( '/quotes:\s*\[(.*?)((\s*))\]/s', $config_js_content, $m_quotes_block ) ) {
        $quotes_content = $m_quotes_block[1];
        if ( preg_match_all( '/"([^"\\]*(?:\\.[^"\\]*)*)"/', $quotes_content, $m_quotes ) ) {
             $quotes = $m_quotes[1];
        }
    }
    if ( empty($quotes) ) $quotes = array("Liebe ist alles.");

    // Extract Ideas
    $ideas = array();
    // Look for 'ideas: [' and then the content until ']'
    if ( preg_match( '/ideas:\s*\[(.*?)((\s*))\]/s', $config_js_content, $m_ideas_block ) ) {
        $ideas_content = $m_ideas_block[1];
        if ( preg_match_all( '/"([^"\\]*(?:\\.[^"\\]*)*)"/', $ideas_content, $m_ideas ) ) {
             $ideas = $m_ideas[1];
        }
    }
    if ( empty($ideas) ) $ideas = array("Mach heute einfach ein Kompliment.");

    // Defaults
    $defaults = array(
        'to'            => isset( $email_addresses['husband']['email'] ) ? $email_addresses['husband']['email'] : '',
        'recipient_name'=> isset( $email_addresses['husband']['name'] ) ? $email_addresses['husband']['name'] : 'Liebe/r',
        'event_label'   => 'Ein besonderer Tag',
        'event_date'    => date('d.m.Y'),
        'send_to_wife'  => false,
        'force_send'    => false
    );

    $parsed_atts = shortcode_atts( $defaults, $atts );

    // Override recipient if send_to_wife is TRUE
    if ( filter_var( $parsed_atts['send_to_wife'], FILTER_VALIDATE_BOOLEAN ) && isset( $email_addresses['wife'] ) ) {
        $parsed_atts['to'] = $email_addresses['wife']['email'];
        $parsed_atts['recipient_name'] = $email_addresses['wife']['name'];
    }
    
    $to_email    = sanitize_email( $parsed_atts['to'] );
    $event_label = sanitize_text_field( $parsed_atts['event_label'] );
    $event_date  = sanitize_text_field( $parsed_atts['event_date'] );
    $recipient_name = sanitize_text_field( $parsed_atts['recipient_name'] );

    if ( empty( $to_email ) ) return array( 'success' => false, 'message' => 'Keine E-Mail-Adresse gefunden.' );

    $random_quote = $quotes[ array_rand( $quotes ) ];
    $random_idea = $ideas[ array_rand( $ideas ) ];
    
    $subject = 'Erinnerung: ' . $event_label; 

    // Enhanced HTML Email Template (Pink/Glass style)
    $message = "
        <html>
        <head>
            <title>{$subject}</title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #fce4ec; padding: 20px; margin: 0; }
                .email-container { 
                    background-color: #ffffff; 
                    padding: 40px;
                    border-radius: 12px;
                    max-width: 600px;
                    margin: 0 auto;
                    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.1);
                    border: 1px solid #f8bbd0;
                }
                h1 { color: #883e4c; font-size: 24px; margin-top: 0; text-align: center; font-family: 'Georgia', serif; }
                h2 { color: #b76e79; font-size: 18px; margin-top: 30px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; }
                p { color: #555555; line-height: 1.6; font-size: 16px; margin: 10px 0; }
                .highlight-box { 
                    background: linear-gradient(135deg, #fff0f5, #ffebee);
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                    text-align: center;
                    border: 1px solid #ffcdd2;
                }
                .highlight-box strong { color: #e91e63; font-size: 18px; display: block; margin-bottom: 5px; }
                .idea-box {
                    background-color: #f1f8e9;
                    border-left: 5px solid #aed581;
                    padding: 15px;
                    margin: 20px 0;
                    font-style: italic;
                    color: #558b2f;
                }
                .quote-box {
                    text-align: center;
                    font-family: 'Georgia', serif;
                    font-style: italic;
                    color: #883e4c;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px dashed #e0e0e0;
                }
                .footer { margin-top: 30px; font-size: 12px; color: #999; text-align: center; }
                .btn {
                    display: inline-block;
                    background-color: #b76e79;
                    color: #ffffff;
                    text-decoration: none;
                    padding: 10px 20px;
                    border-radius: 20px;
                    font-weight: bold;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <h1>Hallo {$recipient_name}! ❤️</h1>
                
                <p>Ein besonderer Meilenstein steht vor der Tür. Zeit, die Liebe zu feiern!</p>
                
                <div class="highlight-box">
                    <strong>{$event_label}</strong>
                    <span>am {$event_date}</span>
                </div>

                <h2>💡 Deine Überraschungs-Idee</h2>
                <div class="idea-box">
                    „{$random_idea}“
                </div>
                <p>Kleine Gesten erhalten die Liebe. Vielleicht ist das ja eine Inspiration für dich?</p>
                
                <div class="quote-box">
                    „{$random_quote}“
                </div>

                <div class="footer">
                    <p>Dein Hochzeitstag Countdown</p>
                    <p><a href="