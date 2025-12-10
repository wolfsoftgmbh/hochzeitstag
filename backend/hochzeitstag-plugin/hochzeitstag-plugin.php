<?php
/**
 * Plugin Name: Hochzeitstag Countdown & Notifier
 * Description: Displays your wedding countdown at /hochzeitstag/ and sends email notifications 1 day before special milestones.
 * Version: 1.0
 * Author: Wolfsoft GmbH
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Configuration
define('HOCHZEITSTAG_WEDDING_DATE', '2025-09-06 11:02:00');
define('HOCHZEITSTAG_EMAIL_TO', 'klaus@wolfsoft.de');
define('HOCHZEITSTAG_SLUG', 'hochzeitstag');

/**
 * 1. DISPLAY LOGIC
 * Intercept requests to /hochzeitstag/ and serve the static HTML file.
 */
function hochzeitstag_rewrite_rule() {
    add_rewrite_rule('^' . HOCHZEITSTAG_SLUG . '/?$', 'index.php?hochzeitstag_page=1', 'top');
}
add_action('init', 'hochzeitstag_rewrite_rule');

function hochzeitstag_query_vars($vars) {
    $vars[] = 'hochzeitstag_page';
    return $vars;
}
add_filter('query_vars', 'hochzeitstag_query_vars');

function hochzeitstag_template_include($template) {
    if (get_query_var('hochzeitstag_page')) {
        $plugin_dir = plugin_dir_path(__FILE__);
        $html_file = $plugin_dir . 'index.html';

        if (file_exists($html_file)) {
            // We serve the file directly. 
            // We need to fix the image path because the HTML expects 'kiss.jpeg' to be relative.
            // We can read the content and replace the image URL with the full plugin URL.
            $content = file_get_contents($html_file);
            $image_url = plugins_url('kiss.jpeg', __FILE__);
            
            // Replace 'kiss.jpeg' with the full URL to the image in the plugin folder
            $content = str_replace("url('kiss.jpeg')", "url('$image_url')", $content);
            $content = str_replace('url("kiss.jpeg")', "url('$image_url')", $content);

            echo $content;
            exit;
        }
    }
    return $template;
}
add_action('template_include', 'hochzeitstag_template_include');


/**
 * 2. EMAIL NOTIFICATION LOGIC
 * Schedule a daily event to check for milestones.
 */

// Add a custom schedule interval if needed (daily is default in WP, but let's be sure)
function hochzeitstag_cron_schedules($schedules) {
    if (!isset($schedules['daily'])) {
        $schedules['daily'] = array(
            'interval' => 86400,
            'display'  => __('Once Daily')
        );
    }
    return $schedules;
}
add_filter('cron_schedules', 'hochzeitstag_cron_schedules');

// Schedule the event on plugin activation
register_activation_hook(__FILE__, 'hochzeitstag_activate');
function hochzeitstag_activate() {
    if (!wp_next_scheduled('hochzeitstag_daily_event')) {
        wp_schedule_event(time(), 'daily', 'hochzeitstag_daily_event');
    }
    // Flush rewrite rules so the /hochzeitstag/ link works immediately
    hochzeitstag_rewrite_rule();
    flush_rewrite_rules();
}

// Clear the schedule on deactivation
register_deactivation_hook(__FILE__, 'hochzeitstag_deactivate');
function hochzeitstag_deactivate() {
    $timestamp = wp_next_scheduled('hochzeitstag_daily_event');
    wp_unschedule_event($timestamp, 'hochzeitstag_daily_event');
}

// The core logic to check dates and send mail
add_action('hochzeitstag_daily_event', 'hochzeitstag_check_dates');
function hochzeitstag_check_dates() {
    $wedding_date = new DateTime(HOCHZEITSTAG_WEDDING_DATE);
    
    // We want to notify 1 day BEFORE the event.
    // So if Tomorrow == EventDate, we send mail today.
    $tomorrow = new DateTime('tomorrow'); 

    // Define Milestones (Must match the JS logic)
    $milestones = array();

    // Days: 100, 200, 300
    foreach ([100, 200, 300] as $days) {
        $date = clone $wedding_date;
        $date->modify("+$days days");
        $milestones["$days. Tag"] = $date;
    }

    // Months: 3 (1/4 Jahr), 6 (2/4 Jahr), 9 (3/4 Jahr)
    $quarter_map = [3 => '1/4 Jahr', 6 => '2/4 Jahr', 9 => '3/4 Jahr'];
    foreach ($quarter_map as $months => $label) {
        $date = clone $wedding_date;
        $date->modify("+$months months");
        $milestones[$label] = $date;
    }

    // Check matches
    foreach ($milestones as $label => $date) {
        // Compare Y-m-d format
        if ($date->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
            hochzeitstag_send_mail($label, $date);
        }
    }
}

function hochzeitstag_send_mail($label, $date) {
    $to = HOCHZEITSTAG_EMAIL_TO;
    $subject = "Erinnerung: Morgen ist ein besonderer Hochzeitstag-Meilenstein!";
    $message = "Hallo Klaus,\n\n";
    $message .= "Nur zur Erinnerung: Morgen erreichen wir einen besonderen Meilenstein!\n\n";
    $message .= "Meilenstein: " . $label . "\n";
    $message .= "Datum: " . $date->format('d.m.Y') . "\n\n";
    $message .= "Vergiss nicht, morgen auf die Countdown-Seite zu schauen!\n";
    $message .= site_url('/' . HOCHZEITSTAG_SLUG . '/');
    
    wp_mail($to, $subject, $message);
}
