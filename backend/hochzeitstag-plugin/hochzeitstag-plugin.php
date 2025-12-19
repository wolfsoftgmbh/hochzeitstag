<?php
/**
 * Plugin Name: Hochzeitstag Countdown
 * Description: A romantic countdown to your wedding anniversary. Available at /hochzeit/
 * Version: 2.0
 * Author: Gemini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'HOCHZEITSTAG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOCHZEITSTAG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * ------------------------------------------------------------------------
 * 1. SETTINGS & MENU
 * ------------------------------------------------------------------------
 */

add_action( 'admin_menu', 'hochzeitstag_add_admin_menu' );
add_action( 'admin_init', 'hochzeitstag_settings_init' );

function hochzeitstag_add_admin_menu() {
    add_menu_page(
        'Hochzeitstag Konfiguration',
        'Hochzeitstag',
        'manage_options',
        'hochzeitstag-settings',
        'hochzeitstag_settings_page',
        'dashicons-heart',
        100
    );
}

function hochzeitstag_settings_init() {
    register_setting( 'hochzeitstagPlugin', 'hochzeitstag_settings' );

    add_settings_section(
        'hochzeitstag_section_general',
        'Allgemeine Einstellungen',
        'hochzeitstag_section_general_callback',
        'hochzeitstagPlugin'
    );

    add_settings_field( 'wedding_date', 'Hochzeitsdatum', 'hochzeitstag_date_render', 'hochzeitstagPlugin', 'hochzeitstag_section_general', ['id' => 'wedding_date'] );
    add_settings_field( 'first_contact_date', 'Erster Kontakt', 'hochzeitstag_date_render', 'hochzeitstagPlugin', 'hochzeitstag_section_general', ['id' => 'first_contact_date'] );
    add_settings_field( 'first_meet_date', 'Zusammengekommen', 'hochzeitstag_date_render', 'hochzeitstagPlugin', 'hochzeitstag_section_general', ['id' => 'first_meet_date'] );
    
    add_settings_field( 'birthday_husband', 'Geburtstag (Ehemann)', 'hochzeitstag_date_render', 'hochzeitstagPlugin', 'hochzeitstag_section_general', ['id' => 'birthday_husband'] );
    add_settings_field( 'birthday_wife', 'Geburtstag (Ehefrau)', 'hochzeitstag_date_render', 'hochzeitstagPlugin', 'hochzeitstag_section_general', ['id' => 'birthday_wife'] );

    add_settings_section(
        'hochzeitstag_section_events',
        'Ereignisse',
        'hochzeitstag_section_events_callback',
        'hochzeitstagPlugin'
    );
    add_settings_field( 'custom_events', 'Benutzerdefinierte Events (JSON)', 'hochzeitstag_textarea_render', 'hochzeitstagPlugin', 'hochzeitstag_section_events', ['id' => 'custom_events', 'desc' => 'Beispiel: [{"date":"2025-12-24","label":"Weihnachten"}]'] );

    add_settings_section(
        'hochzeitstag_section_email',
        'E-Mail Einstellungen',
        'hochzeitstag_section_email_callback',
        'hochzeitstagPlugin'
    );
    add_settings_field( 'email_husband', 'E-Mail (Ehemann)', 'hochzeitstag_text_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'email_husband'] );
    add_settings_field( 'name_husband', 'Name (Ehemann)', 'hochzeitstag_text_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'name_husband'] );
    add_settings_field( 'active_husband', 'Aktiv (Ehemann)', 'hochzeitstag_checkbox_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'active_husband'] );
    
    add_settings_field( 'email_wife', 'E-Mail (Ehefrau)', 'hochzeitstag_text_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'email_wife'] );
    add_settings_field( 'name_wife', 'Name (Ehefrau)', 'hochzeitstag_text_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'name_wife'] );
    add_settings_field( 'active_wife', 'Aktiv (Ehefrau)', 'hochzeitstag_checkbox_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'active_wife'] );
    
    add_settings_field( 'reminder_days', 'Erinnerungstage (z.B. 7, 1)', 'hochzeitstag_text_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', ['id' => 'reminder_days'] );

}

// Callbacks
function hochzeitstag_section_general_callback() { echo 'Geben Sie hier die wichtigsten Daten ein.'; }
function hochzeitstag_section_events_callback() { echo 'Format: JSON Array oder leer lassen.'; }
function hochzeitstag_section_email_callback() { echo 'Konfiguration der Benachrichtigungen.'; }

function hochzeitstag_date_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    $val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
    // Format YYYY-MM-DD for input type date, potentially strip time if present
    $date_val = substr($val, 0, 10);
    echo "<input type='date' name='hochzeitstag_settings[{$args['id']}]' value='{$date_val}'>";
}

function hochzeitstag_text_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    $val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
    echo "<input type='text' name='hochzeitstag_settings[{$args['id']}]' value='{$val}' class='regular-text'>";
}

function hochzeitstag_checkbox_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    $val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : false;
    $checked = $val ? 'checked' : '';
    echo "<input type='checkbox' name='hochzeitstag_settings[{$args['id']}]' value='1' {$checked}>";
}

function hochzeitstag_textarea_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    $val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';
    echo "<textarea name='hochzeitstag_settings[{$args['id']}]' rows='5' cols='50' class='large-text code'>{$val}</textarea>";
    if(isset($args['desc'])) echo "<p class='description'>{$args['desc']}</p>";
}

function hochzeitstag_settings_page() {
    ?>
    <div class="wrap">
        <h1>Hochzeitstag Konfiguration</h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields( 'hochzeitstagPlugin' );
            do_settings_sections( 'hochzeitstagPlugin' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * ------------------------------------------------------------------------
 * 2. GET CONFIGURATION (Helper)
 * ------------------------------------------------------------------------
 */
function hochzeitstag_get_config() {
    $options = get_option( 'hochzeitstag_settings' );
    
    // DEFAULTS (Hardcoded Fallback)
    $defaults = [
        'wedding_date' => '2025-09-06',
        'first_contact_date' => '2014-01-11',
        'first_meet_date' => '2014-04-01',
        'birthday_husband' => '1967-08-02',
        'birthday_wife' => '1972-07-04',
        'custom_events' => '[{"date":"2025-12-21","label":"Kasalla"},{"date":"2013-12-24","label":"XMAS"},{"date":"2013-12-22","label":"Cleverfit"},{"date":"2025-12-20","label":"geb. Party Frank 18:00"}]',
        'email_husband' => 'klaus@wolfsoft.de',
        'name_husband' => 'Klaus',
        'active_husband' => true,
        'email_wife' => 'tanja-risse@gmx.de',
        'name_wife' => 'Tanja',
        'active_wife' => true,
        'reminder_days' => '7, 1'
    ];

    // Merge defaults
    $config = shortcode_atts($defaults, $options);
    
    // Process Arrays
    $reminder_days = array_map('intval', explode(',', $config['reminder_days']));
    $custom_events = json_decode(stripslashes($config['custom_events']), true);
    if (!is_array($custom_events)) $custom_events = [];

    // Return structured object for PHP logic
    return [
        'dates' => [
            'wedding' => $config['wedding_date'],
            'contact' => $config['first_contact_date'],
            'meet' => $config['first_meet_date']
        ],
        'birthdays' => [
            'klaus' => $config['birthday_husband'],
            'tanja' => $config['birthday_wife']
        ],
        'recipients' => [
            ['email' => $config['email_husband'], 'name' => $config['name_husband'], 'active' => $config['active_husband']],
            ['email' => $config['email_wife'], 'name' => $config['name_wife'], 'active' => $config['active_wife']]
        ],
        'customEvents' => $custom_events,
        'reminderDays' => $reminder_days
    ];
}


/**
 * ------------------------------------------------------------------------
 * 3. FRONTEND INTEGRATION
 * ------------------------------------------------------------------------
 */

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

function hochzeitstag_enqueue_assets() {
    // Local Fonts & Styles
    wp_enqueue_style( 'hochzeitstag-fonts', plugins_url( 'assets/fonts/fonts.css', __FILE__ ), array(), '1.5' );
    wp_enqueue_style( 'hochzeitstag-style', plugins_url( 'assets/style.css', __FILE__ ), array(), '1.5' );

    // Script
    wp_enqueue_script( 'hochzeitstag-script', plugins_url( 'assets/script.js', __FILE__ ), array(), '2.0', true );

    // INJECT CONFIGURATION FROM DB
    $cfg = hochzeitstag_get_config();
    
    // Construct JS Object
    $js_config = [
        'weddingDate' => $cfg['dates']['wedding'] . 'T11:02:00', // Keep time for now
        'firstContactDate' => $cfg['dates']['contact'] . 'T19:02:00',
        'firstMeetDate' => $cfg['dates']['meet'] . 'T21:02:00',
        'birthdays' => $cfg['birthdays'],
        'customEvents' => $cfg['customEvents'],
        'emailReminderDays' => $cfg['reminderDays'],
        'quotes' => [
            "Liebe ist: zu zweit albern sein.", "Wir passen, wie Topf und Deckel.", "Liebe ist alles." 
            // Truncated for brevity in inline script, full list ideally also in DB or kept in JS file if static
        ],
        'surpriseIdeas' => [
            "Frühstück am Bett", "Essen gehen"
        ]
    ];
    
    // We keep the huge static lists in JS for now unless you want them in DB too (messy)
    // We only override the "dynamic" parts.
    // Actually, script.js checks `typeof HOCHZEITSTAG_CONFIG !== 'undefined'`.
    // We will inject the object. But we need the quotes. 
    // Trick: In script.js, merge with defaults if missing?
    // Current script.js uses the whole object or fallback.
    // Let's provide the essential override values.
    
    // PROBLEM: script.js has `const HOCHZEITSTAG_CONFIG = ...`. It's not a var we can easily merge BEFORE it runs if it's hardcoded.
    // Wait, script.js says: `const CONFIG = (typeof HOCHZEITSTAG_CONFIG !== 'undefined') ? HOCHZEITSTAG_CONFIG : { ...defaults... }`
    // So we just need to define `HOCHZEITSTAG_CONFIG` before script.js loads.
    
    // We need to include the FULL list of quotes/ideas if we overwrite the object, OR we change script.js to merge.
    // Changing script.js to merge is safer.
    
    wp_add_inline_script( 'hochzeitstag-script', 'var HOCHZEITSTAG_DB_CONFIG = ' . json_encode($js_config) . ';', 'before' );
    
    wp_localize_script( 'hochzeitstag-script', 'hochzeitstag_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

/**
 * ------------------------------------------------------------------------
 * 4. CRON & EMAIL
 * ------------------------------------------------------------------------
 */

function hochzeitstag_activate() {
    hochzeitstag_rewrite_rule();
    flush_rewrite_rules();
    if ( ! wp_next_scheduled( 'hochzeitstag_daily_event' ) ) {
        $time = strtotime( 'tomorrow 09:00:00' );
        wp_schedule_event( $time, 'daily', 'hochzeitstag_daily_event' );
    }
}
register_activation_hook( __FILE__, 'hochzeitstag_activate' );

function hochzeitstag_deactivate() {
    wp_clear_scheduled_hook( 'hochzeitstag_daily_event' );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hochzeitstag_deactivate' );

add_action( 'hochzeitstag_daily_event', 'hochzeitstag_cron_check' );
function hochzeitstag_cron_check() {
    _hochzeitstag_prepare_and_send_email( array( 'force_send' => false ) );
}

function _hochzeitstag_prepare_and_send_email( $atts = array() ) {
    if ( ! function_exists( 'wp_mail' ) ) return ['success'=>false, 'message'=>'wp_mail fail'];

    $cfg = hochzeitstag_get_config();
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $wedding_date = new DateTime( $cfg['dates']['wedding'] );
    $wedding_date->setTime(0, 0, 0);

    // --- MILESTONE LOGIC (Simplified for PHP) ---
    // (We reuse the logic from before but use $cfg array now)
    $upcoming_events = [];
    
    $add_annual = function($date_str, $label_base) use ($today, &$upcoming_events) {
        if (!$date_str) return;
        $base_date = new DateTime($date_str);
        $base_date->setTime(0,0,0);
        $current_year = $today->format('Y');
        for ($y = $current_year; $y <= $current_year + 1; $y++) {
            $evt_date = clone $base_date;
            $evt_date->setDate($y, $base_date->format('m'), $base_date->format('d'));
            if ($evt_date >= $today) {
                $years = $y - $base_date->format('Y');
                $label = ($years > 0) ? "{$years}. {$label_base}" : $label_base;
                $upcoming_events[] = array('label' => $label, 'date' => $evt_date);
            }
        }
    };

    $add_annual($cfg['dates']['wedding'], "Hochzeitstag");
    $add_annual($cfg['dates']['contact'], "Jahrestag (Erster Kontakt)");
    $add_annual($cfg['dates']['meet'], "Jahrestag (Zusammen)");

    foreach ($cfg['birthdays'] as $name => $date) {
        $add_annual($date, "Geburtstag " . ucfirst($name));
    }

    foreach ($cfg['customEvents'] as $ce) {
        if(isset($ce['date'])) $add_annual($ce['date'], "Special Event: " . $ce['label']);
    }

    // 1000s & Schnapszahlen
    $diff_days = $today->diff($wedding_date)->days;
    $next_thousand = ceil(($diff_days + 1) / 1000) * 1000;
    $d_thousand = clone $wedding_date; $d_thousand->modify("+$next_thousand days");
    $upcoming_events[] = ['label'=>"{$next_thousand}. Tag gemeinsam", 'date'=>$d_thousand];

    for ($digits = 3; $digits <= 5; $digits++) {
        for ($n = 1; $n <= 9; $n++) {
            $num = intval(str_repeat((string)$n, $digits));
            if ($num > $diff_days) {
                $d = clone $wedding_date; $d->modify("+$num days");
                if ($d->diff($today)->days < 750) $upcoming_events[] = ['label'=>"{$num}. Tag (Schnapszahl!)", 'date'=>$d];
            }
        }
    }
    
    // Sort
    usort($upcoming_events, function($a, $b) { return $a['date'] <=> $b['date']; });

    // Trigger Check
    $target_event = null;
    $reminder_suffix = '';
    $force_send = (isset($atts['force_send']) && filter_var($atts['force_send'], FILTER_VALIDATE_BOOLEAN));
    
    $days_1 = isset($cfg['reminderDays'][0]) ? $cfg['reminderDays'][0] : 7;
    $days_2 = isset($cfg['reminderDays'][1]) ? $cfg['reminderDays'][1] : 1;

    foreach($upcoming_events as $evt) {
        if ($evt['date'] < $today) continue;
        $diff = $today->diff($evt['date'])->days;
        
        if ($diff == $days_1) { $target_event = $evt; $reminder_suffix=" (in $days_1 Tagen)"; break; }
        if ($diff == $days_2) { $target_event = $evt; $reminder_suffix=" (Morgen!)"; break; }
    }

    if ($force_send) {
        $target_event = ['label' => isset($atts['event_label'])?$atts['event_label']:'Test', 'date' => $today];
        $reminder_suffix = " (Test)";
    }

    if (!$target_event) return ['success'=>false, 'message'=>'Kein Event.'];

    // Send
    $sent = 0;
    foreach($cfg['recipients'] as $rcp) {
        if(empty($rcp['email']) || !$rcp['active']) continue;
        
        $subject = "📅 Countdown-Alarm: {$target_event['label']} steht an!{$reminder_suffix}";
        $message = "Hallo {$rcp['name']}!<br>Bald ist es soweit: <b>{$target_event['label']}</b> am " . $target_event['date']->format('d.m.Y');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        if(wp_mail($rcp['email'], $subject, $message, $headers)) $sent++;
    }

    return ['success'=>true, 'message'=>"Gesendet an $sent Empfänger."];
}


// Shortcode & Ajax (Legacy Wrappers)
add_shortcode( 'hochzeitstag_test_email', 'hochzeitstag_send_test_email_shortcode' );
function hochzeitstag_send_test_email_shortcode( $atts ) {
    return _hochzeitstag_prepare_and_send_email(array_merge($atts, ['force_send'=>true]))['message'];
}

add_action( 'wp_ajax_hochzeitstag_send_test_email', 'hochzeitstag_ajax_send_test_email' );
add_action( 'wp_ajax_nopriv_hochzeitstag_send_test_email', 'hochzeitstag_ajax_send_test_email' );
function hochzeitstag_ajax_send_test_email() {
    $res = _hochzeitstag_prepare_and_send_email( $_POST );
    if($res['success']) wp_send_json_success($res); else wp_send_json_error($res);
    wp_die();
}