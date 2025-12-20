<?php
/**
 * Plugin Name: Hochzeitstag Countdown
 * Description: A romantic countdown to your wedding anniversary. Available at /hochzeit/
 * Version: 2.5
 * Author: Gemini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'HOCHZEITSTAG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOCHZEITSTAG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

function hochzeitstag_log($msg) {
    $entry = "HOCHZEITSTAG-LOG: " . (is_array($msg) || is_object($msg) ? print_r($msg, true) : $msg);
    error_log($entry); // Writes to Docker/Server logs
    
    // Also try writing to file as backup
    $log_file = HOCHZEITSTAG_PLUGIN_PATH . 'debug.log';
    $file_entry = date('Y-m-d H:i:s') . " - " . (is_array($msg) || is_object($msg) ? print_r($msg, true) : $msg) . "\n";
    @file_put_contents($log_file, $file_entry, FILE_APPEND);
}

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

function hochzeitstag_settings_page() {
    ?>
    <div class="wrap">
        <h1>Hochzeitstag Konfiguration</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'hochzeitstagPlugin' );
            do_settings_sections( 'hochzeitstagPlugin' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
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
    add_settings_field( 'email_send_time', 'Sendezeit (HH:MM)', 'hochzeitstag_text_render', 'hochzeitstagPlugin', 'hochzeitstag_section_email', [
        'id' => 'email_send_time', 
        'desc' => 'Uhrzeit für den täglichen Versand (z.B. 09:00).<br><span style="color: #d63638; font-weight: bold;">Aktuelle Serverzeit: ' . current_time('H:i') . '</span>'
    ] );

    add_settings_section(
        'hochzeitstag_section_content',
        'Inhalte (Zufallstexte)',
        'hochzeitstag_section_content_callback',
        'hochzeitstagPlugin'
    );
    add_settings_field( 'quotes', 'Sprüche & Zitate (Einer pro Zeile)', 'hochzeitstag_textarea_render', 'hochzeitstagPlugin', 'hochzeitstag_section_content', ['id' => 'quotes', 'desc' => 'Diese Sprüche werden zufällig auf der Seite angezeigt.'] );
    add_settings_field( 'surprise_ideas', 'Überraschungsideen (Einer pro Zeile)', 'hochzeitstag_textarea_render', 'hochzeitstagPlugin', 'hochzeitstag_section_content', ['id' => 'surprise_ideas', 'desc' => 'Ideen für kleine Aufmerksamkeiten.'] );

}

/**
 * ------------------------------------------------------------------------
 * 2. CONFIGURATION HELPER & DEFAULTS
 * ------------------------------------------------------------------------
 */
function hochzeitstag_get_defaults() {
    return [
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
        'reminder_days' => '7, 1, 0',
        'email_send_time' => '09:00',
        'quotes' => "Liebe ist: zu zweit albern sein.\nWir passen, wie Topf und Deckel.\nEhe: Streit um Fernbedienung, endet mit Lachen.\nZusammen sind wir besser, wie Kaffee & Kuchen.\nMein Lieblingsmensch, trotz letztem Keks.\nLiebe: Das Einzige, was mehr wird, wenn man es verschwendet.\nMit dir wird jeder Einkauf zum Abenteuer.\nEchte Liebe erträgt auch Schnarchen.\nMein Anker im Sturm, mein Konfetti im Alltag.\nZuhause ist, wo du bist (und WLAN).\nWir: Ein Team für Chaos und Ordnung.\nLiebe heißt: immer wieder vertragen.\nMit dir: Jeder Tag ein Feiertag.\nDu bist der Grund für mein Handy-Lächeln.\nUnsere Liebe: guter Wein, wird besser mit Jahren.\nDanke, dass du meine Macken magst.\nWir zwei gegen den Rest der Welt (und Abwasch).\nDu bringst mich zum Lachen, auch mürrisch.\nGlück ist: herrlich albern sein.\nDu bist der Zucker in meinem Kaffee.\nEgal wohin, Hauptsache zusammen.\nMit dir macht sogar Nichtstun Spaß.\nDu bist mein Happy Place.\nLiebe ist: blind verstehen.\nIch mag dich mehr als Pizza.\nDu und ich – das passt.\nMein Herz schlägt im Takt von deinem.\nDeine Umarmung: mein Lieblingsort.\nZusammen ist man weniger allein.\nDu bist mein Lieblings-Nervzwerg.\nIch liebe dich mehr als Kaffee (sag's nicht).\nWir sind wie Pech und Schwefel, nur hübscher.\nDu hast den Schlüssel zum Herzen (und Kühlschrank).\nLiebe: Du lässt mir die letzte Schokolade.\nDu bist der Grund, warum ich aufstehe (meistens).\nMit dir ist sogar der Abwasch erträglich.\nWir sind das perfekte Chaos.\nDu bist mein liebster Zeitvertreib.\nIch liebe dich, auch wenn du hungrig bist.\nDu bist mein persönlicher Superheld (ohne Umhang).\nZusammen sind wir unschlagbar (im Faulenzen).\nDu bist mein Lieblingsmensch, Punkt.\nLiebe ist: schweigend anschreien können.\nDu bist süßer als Zuckerwatte.\nIch würde mein Handy für dich weglegen.\nDu bist der Käse auf meiner Pizza.\nWir passen zusammen wie Pommes und Ketchup.\nDu bist mein Einhorn in Pferdeherde.\nLiebe ist: gemeinsam dick werden.\nDu bist der Grund für mein Dauergrinsen.\nIch liebe dich mehr als gestern (weniger als morgen).\nDu bist meine bessere, vernünftigere Hälfte.\nMit dir kann man Pferde stehlen (und Ponys).\nDu bist mein Lieblings-Kuscheltier.\nLiebe: Decke teilen (widerwillig).\nDu bist der Hit in meinen Charts.\nIch folge dir (außer aufs Klo).\nDu bist mein Highlight des Tages.\nWir sind Bonnie & Clyde, ohne Banküberfall.\nDu bist mein 6er im Lotto.\nIch liebe dich bis zur Unendlichkeit.\nDu bist mein Fels and mein Kissen.\nMit dir wird's nie langweilig.\nDu bist mein Lieblings-Abenteuer.\nLiebe ist: blind vertrauen (trotzdem Google Maps checken).\nDu bist mein Sternenhimmel.\nIch hab dich zum Fressen gern.\nDu bist mein Lieblings-Gedanke vor dem Einschlafen.\nWir sind ein Dream-Team.\nDu bist mein Sonnenschein, auch nachts.\nIch liebe dich mehr als Schokolade.\nDu bist mein Herzblatt.\nMit dir ist das Leben ein Ponyhof.\nDu bist mein allerliebster Lieblingsmensch.\nLiebe ist: gegenseitig Sätze beenden.\nDu bist der Grund, warum ich glücklich bin.\nIch bin süchtig nach dir.\nDu bist mein Zuhause.\nWir sind einfach füreinander gemacht.\nZwei Herzen, ein Beat, unser Rhythmus.\nLiebe ist: den anderen in Jogginghose lieben.\nDu bist mein Happy End, jeden Tag.\nUnsere Liebe: mehr als tausend Worte.\nEin Blick sagt mehr als jede Rede.\nMit dir ist jeder Moment Gold wert.\nDu machst mein Leben heller.\nUnsere Herzen tanzen im Gleichklang.\nDu bist mein Traum, der wahr wurde.\nEin Leben ohne dich? Undenkbar!\nLiebe ist die schönste Reise.\nDu bist das Puzzleteil, das fehlte.\nJeder Tag mit dir ist ein Geschenk.\nDu gibst meinem Leben Sinn.\nDu bist mein Anker, mein Halt.\nUnsere Liebe: ein unendliches Band.\nDu bist mein größtes Abenteuer.\nMit dir ist alles leichter.\nDu bist mein Lächeln, meine Freude.\nUnsere Liebe wächst jeden Tag.\nDu bist mein Herz, meine Seele.\nDu bist der Grund für mein Glück.\nEin Kuss von dir: mein Lieblingsgefühl.\nDu bist mein Zuhause, wo immer wir sind.\nUnsere Liebe: stärker als alles.\nDu bist mein Wunsch, der in Erfüllung ging.\nMit dir ist jeder Tag ein Gedicht.\nDu bist mein Held, mein Retter.\nUnsere Liebe: ein ewiges Feuer.\nDu bist mein Schatz, mein größter Gewinn.\nDu machst mich komplett.\nUnsere Herzen sind verbunden.\nDu bist mein Glück, mein Schicksal.\nMit dir ist jeder Weg das Ziel.\nDu bist mein Stern, der leuchtet.\nUnsere Liebe: unendlich und rein.\nDu bist mein Leben, mein Atem.\nDu bist mein Licht, meine Sonne.\nUnsere Liebe: ein Wunder, das bleibt.\nDu bist mein Alles, mein Nichts.\nDu bist meine Ewigkeit.",
        'surprise_ideas' => "Frühstück am Bett servieren\nEinen handgeschriebenen Liebesbrief verstecken\nEin Picknick im Wohnzimmer veranstalten\nEinen Überraschungs-Wochenendtrip planen\nDas Lieblingsessen kochen\nEin entspannendes Massage-Abend mit Duftöl\nEin Fotoalbum mit gemeinsamen Erinnerungen erstellen\nEine kleine Schatzsuche durch die Wohnung organisieren\nKinokarten für den neuesten Film besorgen\nEin gemeinsames Bad mit Kerzenschein und Musik vorbereiten\nEinfach mal ohne Grund Blumen mitbringen\nEin Kompliment im Vorbeigehen flüstern\nEinen Stern nach dem Partner benennen\nEine Playlist mit 'unseren' Liedern erstellen\nDen Partner von der Arbeit abholen\nEinen gemeinsamen Kochkurs besuchen\nEin Schloss an einer Brücke anbringen\nEinen Liebesbrief per Post schicken\nEinen Wellness-Tag zu Hause einlegen\nZusammen den Sonnenaufgang anschauen\nEin Überraschungs-Date im Lieblingsrestaurant\nDie Lieblingsserie zusammen schauen (auch wenn man sie nicht mag)\nEinen Abend lang alle Hausarbeiten übernehmen\nEin kleines Geschenk ohne Anlass kaufen\nGemeinsam ein neues Hobby ausprobieren\nEinen Brief für die Zukunft schreiben\nEin Kompliment auf den Badezimmerspiegel schreiben\nEin gerahmtes Foto von einem besonderen Moment schenken\nEinen Tanzabend im Wohnzimmer machen\nZusammen Schlittschuhlaufen oder Rollschuhfahren gehen\nEinen Drachen steigen lassen\nGemeinsam in den Zoo oder Botanischen Garten gehen\nEinen Spieleabend mit den Lieblingsspielen organisieren\nEine Massage-Gutschein basteln\nEinen Korb mit Lieblings-Snacks zusammenstellen\nEin privates Fotoshooting machen\nEinen Baum zusammen pflanzen\nEinfach mal 'Ich liebe dich' sagen, wenn es niemand erwartet\nEine Nacht unter freiem Himmel schlafen\nEin Boot mieten und über einen See rudern\nEinen Flohmarktbummel machen\nZusammen ein Puzzle lösen\nEine Flaschenpost vorbereiten\nDen Partner mit einem warmen Handtuch nach dem Duschen überraschen\nEinen Liebes-Post-it am Kühlschrank hinterlassen\nZusammen alte Kinderfotos anschauen\nEin Lied für den Partner singen (oder rappen)\nEinen Ausflug an den Ort des ersten Treffens machen\nGemeinsam ein Museum besuchen\nEine Weinprobe zu Hause machen\nEinen Karaoke-Abend veranstalten\nEin gemeinsames Vision-Board für die Zukunft erstellen\nEinen Malkurs für Paare besuchen\nZusammen in eine Therme gehen\nEinen Kuss im Regen genießen\nEinen Spaziergang im Mondschein machen\nEin Frühstück im Freien (Balkon/Garten)\nEinen gemeinsamen Sportkurs belegen\nEinander vorlesen\nEinen Tag lang das Handy ausschalten und Zeit genießen\nEin DIY-Projekt zusammen starten\nEinen Städtetrip in eine unbekannte Stadt\nZusammen ein Konzert besuchen\nEin Überraschungs-Kaffeetrinken organisieren\nEinander die Haare waschen oder kämmen\nEinen Gutschein für eine Autowäsche schenken\nEinen Tag lang 'Ja' zu allen Wünschen des Partners sagen\nEinen gemeinsamen Back-Abend\nEine Zeitkapsel vergraben\nZusammen in den Zirkus oder Varieté gehen\nEinen Wanderurlaub planen\nEin kleines Gedicht schreiben\nDie Bettwäsche frisch beziehen und mit Blüten bestreuen\nEin Eis essen gehen\nZusammen Tretboot fahren\nEinen Sonnenuntergang am Strand/See beobachten\nEinander massieren (Nacken, Füße)\nEinen gemeinsamen Tanzkurs machen\nEinen Plan für das nächste Jahr schmieden\nEinen Brief schreiben, warum man dankbar ist\nEin Überraschungs-BBQ im Garten\nZusammen eine Sternwarte besuchen\nEinander ein Hobby erklären und ausprobieren lassen\nEinen Roadtrip ohne festes Ziel machen\nEine Nacht im Hotel in der eigenen Stadt buchen\nEinander beim Anziehen helfen\nEinen gemeinsam Garten oder Balkonkasten bepflanzen\nEin Kompliment vor Freunden machen\nZusammen einen Freizeitpark besuchen\nEin gemeinsames Bad mit Badebombe\nEinen Abend lang nur Musik hören und reden\nEin Foto von sich selbst in das Portemonnaie des Partners schleichen\nEinen besonderen Tee oder Kaffee kochen\nEinen kleinen Glücksbringer schenken\nEinen Video-Clip mit gemeinsamen Momenten schneiden\nZusammen die Sterne beobachten und Sternbilder suchen\nEinander etwas Neues beibringen\nEine heiße Schokolade mit Sahne an einem kalten Tag\nEinen Kuss auf die Stirn geben\nGemeinsam alt werden (als Plan)"
    ];
}

function hochzeitstag_get_config() {
    $options = get_option( 'hochzeitstag_settings' );
    $defaults = hochzeitstag_get_defaults();

    if ( ! is_array( $options ) ) {
        $options = [];
        $config = $defaults;
    } else {
        // Merge defaults
        $config = shortcode_atts($defaults, $options);
        
        // Fix Checkbox Logic: 
        // If $options was saved but a checkbox is missing, it means it's unchecked (false).
        // shortcode_atts would have filled it with the default (true).
        if ( ! isset( $options['active_husband'] ) ) $config['active_husband'] = false;
        if ( ! isset( $options['active_wife'] ) )    $config['active_wife'] = false;
    }
    
    // Process Arrays
    $reminder_days = array_map('intval', explode(',', $config['reminder_days']));
    $custom_events = json_decode(stripslashes($config['custom_events']), true);
    if (!is_array($custom_events)) $custom_events = [];

    // Process Multiline strings to Arrays
    $quotes = array_values(array_filter(array_map('trim', explode("\n", $config['quotes']))));
    $surprise_ideas = array_values(array_filter(array_map('trim', explode("\n", $config['surprise_ideas']))));

    // FAILSAFE: If user config has very few items (likely accidental empty save or test), fallback to defaults
    if (count($quotes) < 5) {
        $quotes = array_values(array_filter(array_map('trim', explode("\n", $defaults['quotes']))));
    }
    if (count($surprise_ideas) < 5) {
        $surprise_ideas = array_values(array_filter(array_map('trim', explode("\n", $defaults['surprise_ideas']))));
    }

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
        'reminderDays' => $reminder_days,
        'quotes' => $quotes,
        'surpriseIdeas' => $surprise_ideas
    ];
}

/**
 * ------------------------------------------------------------------------
 * 1. SETTINGS & MENU (Callbacks)
 * ------------------------------------------------------------------------
 */

// Callbacks
function hochzeitstag_section_general_callback() { echo 'Geben Sie hier die wichtigsten Daten ein.'; }
function hochzeitstag_section_events_callback() { echo 'Format: JSON Array oder leer lassen.'; }
function hochzeitstag_section_email_callback() { echo 'Konfiguration der Benachrichtigungen.'; }
function hochzeitstag_section_content_callback() { echo 'Verwalten Sie die Texte, die zufällig angezeigt werden.'; }

function hochzeitstag_date_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    if ( !is_array($options) ) $options = [];
    $defaults = hochzeitstag_get_defaults();
    $id = $args['id'];
    
    // Use saved value OR default
    $val = isset( $options[$id] ) ? $options[$id] : $defaults[$id];
    
    // Format YYYY-MM-DD
    $date_val = substr($val, 0, 10);
    echo "<input type='date' name='hochzeitstag_settings[{$id}]' value='{$date_val}'>";
}

function hochzeitstag_text_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    if ( !is_array($options) ) $options = [];
    $defaults = hochzeitstag_get_defaults();
    $id = $args['id'];
    
    $val = isset( $options[$id] ) ? $options[$id] : $defaults[$id];
    echo "<input type='text' name='hochzeitstag_settings[{$id}]' value='{$val}' class='regular-text'>";
}

function hochzeitstag_checkbox_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    if ( !is_array($options) ) $options = [];
    $defaults = hochzeitstag_get_defaults();
    $id = $args['id'];
    
    // Checkbox logic: 
    // If $options is explicitly set (saved), use the value (which might be unset/false if unchecked).
    // But if $options is empty/false (plugin just installed), use the default.
    
    if ( !empty($options) ) {
        // Options exist, so respect the saved state (isset = checked)
        $checked = isset( $options[$id] ) ? 'checked' : '';
    } else {
        // First load, use default
        $checked = $defaults[$id] ? 'checked' : '';
    }
    
    echo "<input type='checkbox' name='hochzeitstag_settings[{$id}]' value='1' {$checked}>";
}

function hochzeitstag_textarea_render( $args ) {
    $options = get_option( 'hochzeitstag_settings' );
    if ( !is_array($options) ) $options = [];
    $defaults = hochzeitstag_get_defaults();
    $id = $args['id'];

    $val = isset( $options[$id] ) ? $options[$id] : $defaults[$id];
    echo "<textarea name='hochzeitstag_settings[{$id}]' rows='5' cols='50' class='large-text code'>{$val}</textarea>";
    if(isset($args['desc'])) echo "<p class='description'>{$args['desc']}</p>";
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
        'quotes' => $cfg['quotes'],
        'surpriseIdeas' => $cfg['surpriseIdeas']
    ];
    
    wp_add_inline_script( 'hochzeitstag-script', 'var HOCHZEITSTAG_DB_CONFIG = ' . json_encode($js_config) . ';', 'before' );
    
    wp_localize_script( 'hochzeitstag-script', 'hochzeitstag_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'hochzeitstag_enqueue_assets' );

/**
 * ------------------------------------------------------------------------
 * 4. CRON & EMAIL
 * ------------------------------------------------------------------------
 */

function hochzeitstag_reschedule_cron() {
    wp_clear_scheduled_hook( 'hochzeitstag_daily_event' );
    
    $options = get_option( 'hochzeitstag_settings' );
    $defaults = hochzeitstag_get_defaults();
    $time_str = isset($options['email_send_time']) ? $options['email_send_time'] : $defaults['email_send_time'];
    
    // Ensure format HH:MM
    if (!preg_match('/^\d{2}:\d{2}$/', $time_str)) $time_str = '09:00';

    $time = strtotime( $time_str );
    wp_schedule_event( $time, 'daily', 'hochzeitstag_daily_event' );
}

// Reschedule when settings are saved
add_action( 'update_option_hochzeitstag_settings', 'hochzeitstag_reschedule_cron' );

function hochzeitstag_activate() {
    hochzeitstag_rewrite_rule();
    flush_rewrite_rules();
    if ( ! wp_next_scheduled( 'hochzeitstag_daily_event' ) ) {
        hochzeitstag_reschedule_cron();
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
    hochzeitstag_log("CRON: Daily event triggered.");
    _hochzeitstag_prepare_and_send_email( array( 'force_send' => false ) );
}

function _hochzeitstag_prepare_and_send_email( $atts = array() ) {
    hochzeitstag_log("MAIL: Starting email check...");
    if ( ! function_exists( 'wp_mail' ) ) {
        hochzeitstag_log("ERROR: wp_mail function missing!");
        return ['success'=>false, 'message'=>'wp_mail fail'];
    }

    $cfg = hochzeitstag_get_config();
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    hochzeitstag_log("DATE: Today is " . $today->format('Y-m-d'));

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
    
    // Check all configured reminder days
    hochzeitstag_log("CHECK: Checking " . count($upcoming_events) . " events against reminders: " . implode(',', $cfg['reminderDays']));
    
    foreach($upcoming_events as $evt) {
        if ($evt['date'] < $today) continue;
        $diff = $today->diff($evt['date'])->days;
        
        // Detailed log for debugging specific day issues (optional, remove if too verbose)
        if ($diff <= 10) hochzeitstag_log("DEBUG: Event '{$evt['label']}' is in $diff days.");

        foreach($cfg['reminderDays'] as $d_remind) {
            if ($diff == $d_remind) {
                hochzeitstag_log("MATCH: Event '{$evt['label']}' matches reminder day $d_remind (Diff: $diff).");
                $target_event = $evt;
                if ($diff == 0) {
                    $reminder_suffix = " (Heute!)";
                } elseif ($diff == 1) {
                    $reminder_suffix = " (Morgen!)";
                } else {
                    $reminder_suffix = " (in $diff Tagen)";
                }
                break 2; // Break both loops
            }
        }
    }

    if ($force_send) {
        hochzeitstag_log("FORCE: Manual test triggered.");
        $test_date = $today;
        if (!empty($atts['event_date'])) {
            try {
                $test_date = new DateTime($atts['event_date']);
            } catch (Exception $e) {
                $test_date = $today;
            }
        }
        $target_event = ['label' => isset($atts['event_label']) ? $atts['event_label'] : 'Test', 'date' => $test_date];
        $reminder_suffix = " (Test)";
    }

    if (!$target_event) {
        hochzeitstag_log("RESULT: No matching event found for today.");
        return ['success'=>false, 'message'=>'Kein Event.'];
    }

    // --- PREPARE EMAIL CONTENT ---
    $quote = "Liebe ist alles.";
    if (!empty($cfg['quotes'])) {
        $quote = $cfg['quotes'][array_rand($cfg['quotes'])];
    }

    $ideas_list = '';
    $ideas = [];
    if (!empty($atts['ideas']) && is_array($atts['ideas'])) {
        $ideas = $atts['ideas'];
    } elseif (!empty($cfg['surpriseIdeas'])) {
        $all_ideas = $cfg['surpriseIdeas'];
        shuffle($all_ideas);
        $ideas = array_slice($all_ideas, 0, 5);
    }

    if (!empty($ideas)) {
        foreach ($ideas as $idea) {
            $ideas_list .= '<li style="margin-bottom: 8px; color: #555;">' . esc_html($idea) . '</li>';
        }
    }

    // Send
    $sent = 0;
    foreach($cfg['recipients'] as $rcp) {
        if(empty($rcp['email']) || !$rcp['active']) continue;
        
        $subject = "📅 Countdown-Alarm: {$target_event['label']} steht an!{$reminder_suffix}";
        
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
                <h2>Hallo " . esc_html($rcp['name']) . "!</h2>
                
                <p class=\"intro-text\">
                    Aufgepasst! Eure gemeinsame Reise erreicht bald den nächsten wunderbaren Meilenstein.
                    Zeit, die Herzen höher schlagen zu lassen!
                </p>

                <div class=\"highlight-box\">
                    <span class=\"event-name\">" . esc_html($target_event['label']) . "</span>
                    <span class=\"event-date\">am " . $target_event['date']->format('d.m.Y') . "</span>
                </div>

                " . (!empty($ideas_list) ? "
                <div class=\"ideas-section\">
                    <span class=\"ideas-title\">💡 5 Ideen für eine kleine Überraschung:</span>
                    <p>Damit du nicht mit leeren Händen (oder leerem Kopf) dastehst, hier ein paar Inspirationen, um deinem Schatz ein Lächeln ins Gesicht zu zaubern:</p>
                    <ul style=\"text-align: left; background: #fff; padding: 15px 15px 15px 30px; border-radius: 8px; border: 1px dashed #e91e63;\">
                        {$ideas_list}
                    </ul>
                </div>" : "") . "

                <div class=\"quote-box\">
                    „" . esc_html($quote) . "“
                </div>

                <div class=\"footer\">
                    <p>Gesendet mit Liebe vom Hochzeitstag Countdown Plugin.</p>
                </div>
            </div>
        </body>
        </html>";

        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        hochzeitstag_log("SENDING: To {$rcp['email']}...");
        $mail_result = wp_mail($rcp['email'], $subject, $message, $headers);
        if($mail_result) {
            hochzeitstag_log("SUCCESS: Mail sent to {$rcp['email']}.");
            $sent++;
        } else {
            hochzeitstag_log("FAIL: wp_mail returned false for {$rcp['email']}.");
        }
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